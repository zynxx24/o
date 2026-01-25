<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Config\Database;
use App\Config\Session;
use App\Middleware\Security;
use App\Domain\VendorRepository;
use App\Domain\MenuRepository;
use App\Domain\UserRepository;
use App\Domain\CartRepository;
use App\Domain\OrderRepository;
use App\Domain\CategoryRepository;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Initialize Database
$database = new Database();
$db = $database->connect();

// Helper to render PHP views
function render(Response $response, $template, $data = [])
{
    extract($data);
    ob_start();
    require __DIR__ . '/../src/Views/' . $template . '.php';
    $content = ob_get_clean();
    $response->getBody()->write($content);
    return $response;
}

// Helper to redirect
function redirect(Response $response, $url)
{
    return $response->withHeader('Location', $url)->withStatus(302);
}

// Helper for JSON response
function json(Response $response, $data, $status = 200)
{
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
}

// ========================================
// PUBLIC ROUTES
// ========================================

// 1. Home Route - List Vendors
$app->get('/', function (Request $request, Response $response) use ($db) {
    if (!$db) {
        $response->getBody()->write("Database Connection Failed. Check .env file.");
        return $response;
    }

    $vendorRepo = new VendorRepository($db);
    $vendors = $vendorRepo->getAllVendors();

    // Get cart count for header
    $cartItemCount = 0;
    if (Session::isLoggedIn()) {
        $cartRepo = new CartRepository($db);
        $cartItemCount = $cartRepo->getCartItemCount(Session::get('user_id'));
    }

    return render($response, 'home', ['vendors' => $vendors, 'cartItemCount' => $cartItemCount]);
});



// 3. About Route
$app->get('/about', function (Request $request, Response $response) {
    return render($response, 'about');
});

// 4. Contact Route
$app->get('/contact', function (Request $request, Response $response) {
    return render($response, 'contact');
});

// 5. Terms Route
$app->get('/terms', function (Request $request, Response $response) {
    return render($response, 'terms');
});

// 6. Privacy Route
$app->get('/privacy', function (Request $request, Response $response) {
    return render($response, 'privacy');
});


// 5. Search Route
$app->get('/search', function (Request $request, Response $response) use ($db) {
    $params = $request->getQueryParams();
    $query = $params['q'] ?? '';
    $category = $params['category'] ?? '';
    $city = $params['city'] ?? '';
    $sort = $params['sort'] ?? 'rating';

    $vendorRepo = new VendorRepository($db);
    $categoryRepo = new CategoryRepository($db);

    // Get all vendors first for cities dropdown
    $allVendors = $vendorRepo->getAllVendors();
    $cities = array_unique(array_filter(array_column($allVendors, 'city')));
    sort($cities);

    // Search/filter vendors
    $vendors = $vendorRepo->searchVendors($query, $city, $sort);
    $categories = $categoryRepo->getAllCategories();

    return render($response, 'search', [
        'vendors' => $vendors,
        'categories' => $categories,
        'cities' => $cities,
        'query' => $query,
        'category' => $category,
        'city' => $city,
        'sort' => $sort,
        'totalResults' => count($vendors)
    ]);
});

// ========================================
// AUTHENTICATION ROUTES
// ========================================

// Login Page
$app->get('/login', function (Request $request, Response $response) {
    if (Session::isLoggedIn()) {
        return redirect($response, '/');
    }
    return render($response, 'login');
});

// Login Process
$app->post('/login', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();

    // CSRF validation
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request. Please try again.');
        return redirect($response, '/login');
    }

    // Rate limiting
    if (!Security::checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'], 5, 300)) {
        Session::flash('error', 'Terlalu banyak percobaan. Coba lagi dalam 5 menit.');
        return redirect($response, '/login');
    }

    $email = Security::sanitizeEmail($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        Session::flash('error', 'Email dan password harus diisi.');
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findByEmail($email);

    if (!$user || !Security::verifyPassword($password, $user['password_hash'])) {
        Session::flash('error', 'Email atau password salah.');
        return redirect($response, '/login');
    }

    // Set session
    Session::set('user_id', $user['user_id']);
    Session::set('user_name', $user['full_name']);
    Session::set('user_email', $user['email']);
    Session::set('user_role', $user['role']);

    // Create auth token (modern security)
    try {
        Security::createAuthToken($db, $user['user_id']);
    } catch (Exception $e) {
        // Token table might not exist yet, continue with session-only auth
    }

    Session::flash('success', 'Selamat datang kembali, ' . $user['full_name'] . '!');
    return redirect($response, '/');
});

// Register Page
$app->get('/register', function (Request $request, Response $response) {
    if (Session::isLoggedIn()) {
        return redirect($response, '/');
    }
    return render($response, 'register');
});

// Register Process
$app->post('/register', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();

    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request. Please try again.');
        return redirect($response, '/register');
    }

    $errors = [];
    $old = [
        'full_name' => Security::sanitize($data['full_name'] ?? ''),
        'username' => Security::sanitize($data['username'] ?? ''),
        'email' => Security::sanitizeEmail($data['email'] ?? ''),
        'phone' => Security::sanitize($data['phone'] ?? '')
    ];

    // Validation
    if (strlen($old['full_name']) < 2) {
        $errors['full_name'] = 'Nama lengkap minimal 2 karakter';
    }
    if (strlen($old['username']) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $old['username'])) {
        $errors['username'] = 'Username minimal 3 karakter (huruf, angka, underscore)';
    }
    if (!Security::validateEmail($old['email'])) {
        $errors['email'] = 'Format email tidak valid';
    }

    $passwordErrors = Security::validatePasswordStrength($data['password'] ?? '');
    if (!empty($passwordErrors)) {
        $errors['password'] = implode(', ', $passwordErrors);
    }
    if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
        $errors['password'] = 'Password tidak cocok';
    }

    // Check existing
    $userRepo = new UserRepository($db);
    if ($userRepo->emailExists($old['email'])) {
        $errors['email'] = 'Email sudah terdaftar';
    }
    if ($userRepo->usernameExists($old['username'])) {
        $errors['username'] = 'Username sudah digunakan';
    }

    if (!empty($errors)) {
        Session::flash('errors', $errors);
        Session::flash('old', $old);
        return redirect($response, '/register');
    }

    // Create user
    try {
        $userId = $userRepo->createUser([
            'username' => $old['username'],
            'email' => $old['email'],
            'password' => $data['password'],
            'full_name' => $old['full_name'],
            'phone' => $old['phone']
        ]);

        Session::flash('success', 'Registrasi berhasil! Silakan login.');
        return redirect($response, '/login');
    } catch (Exception $e) {
        Session::flash('error', 'Terjadi kesalahan. Silakan coba lagi.');
        Session::flash('old', $old);
        return redirect($response, '/register');
    }
});

// Logout
$app->post('/logout', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();
    if (Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        // Invalidate auth token
        try {
            Security::invalidateToken($db);
        } catch (Exception $e) {
            // Token table might not exist
        }
        Session::destroy();
    }
    return redirect($response, '/');
});

// ========================================
// PROTECTED ROUTES (Require Login)
// ========================================

// Profile Page
$app->get('/profile', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));

    return render($response, 'profile', ['user' => $user]);
});

// Update Profile
$app->post('/profile', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/profile');
    }

    $userRepo = new UserRepository($db);
    $userRepo->updateProfile(Session::get('user_id'), [
        'full_name' => Security::sanitize($data['full_name'] ?? ''),
        'phone' => Security::sanitize($data['phone'] ?? ''),
        'address' => Security::sanitize($data['address'] ?? '')
    ]);

    // Update session name
    Session::set('user_name', Security::sanitize($data['full_name'] ?? ''));

    Session::flash('success', 'Profil berhasil diperbarui.');
    return redirect($response, '/profile');
});

// Cart Page
$app->get('/cart', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $cartRepo = new CartRepository($db);
    $userId = Session::get('user_id');

    $cartItems = $cartRepo->getCartByUser($userId);
    $cartItemCount = $cartRepo->getCartItemCount($userId);
    $cartTotal = $cartRepo->getCartTotal($userId);

    return render($response, 'cart', [
        'cartItems' => $cartItems,
        'cartItemCount' => $cartItemCount,
        'cartTotal' => $cartTotal
    ]);
});

// Add to Cart
$app->post('/cart/add', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return json($response, ['error' => 'Please login first'], 401);
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return json($response, ['error' => 'Invalid request'], 400);
    }

    $cartRepo = new CartRepository($db);
    $cartRepo->addItem(
        Session::get('user_id'),
        (int) $data['vendor_id'],
        (int) $data['item_id'],
        (int) ($data['quantity'] ?? 1)
    );

    Session::flash('success', 'Item ditambahkan ke keranjang.');
    return redirect($response, '/vendor/' . $data['vendor_id']);
});

// Update Cart Quantity
$app->post('/cart/update', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/cart');
    }

    $cartRepo = new CartRepository($db);
    $userId = Session::get('user_id');
    $cartItemId = (int) $data['cart_item_id'];

    if ($cartRepo->validateOwnership($userId, $cartItemId)) {
        $cartRepo->updateQuantity($cartItemId, (int) $data['quantity']);
    }

    return redirect($response, '/cart');
});

// Remove from Cart
$app->post('/cart/remove', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/cart');
    }

    $cartRepo = new CartRepository($db);
    $userId = Session::get('user_id');
    $cartItemId = (int) $data['cart_item_id'];

    if ($cartRepo->validateOwnership($userId, $cartItemId)) {
        $cartRepo->removeItem($cartItemId);
        Session::flash('success', 'Item dihapus dari keranjang.');
    }

    return redirect($response, '/cart');
});

// Checkout Page
$app->get('/checkout', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $cartRepo = new CartRepository($db);
    $userRepo = new UserRepository($db);
    $userId = Session::get('user_id');

    $cartItems = $cartRepo->getCartByUser($userId);
    $cartTotal = $cartRepo->getCartTotal($userId);
    $user = $userRepo->findById($userId);

    if (empty($cartItems) || $cartTotal == 0) {
        return redirect($response, '/cart');
    }

    return render($response, 'checkout', [
        'cartItems' => $cartItems,
        'cartTotal' => $cartTotal,
        'user' => $user
    ]);
});

// Create Order
$app->post('/order/create', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request');
        return redirect($response, '/checkout');
    }

    $userId = Session::get('user_id');
    $cartRepo = new CartRepository($db);
    $orderRepo = new OrderRepository($db);

    $cartItems = $cartRepo->getCartByUser($userId);
    if (empty($cartItems)) {
        return redirect($response, '/cart');
    }

    // Get vendor from cart
    $vendorId = $cartItems[0]['vendor_id'];

    try {
        // Create order
        $order = $orderRepo->createOrder([
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'order_type' => 'custom',
            'event_type' => Security::sanitize($data['event_type'] ?? ''),
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'],
            'delivery_address' => Security::sanitize($data['delivery_address'] ?? ''),
            'delivery_city' => Security::sanitize($data['delivery_city'] ?? ''),
            'num_people' => (int) $data['num_people'],
            'subtotal' => (float) $data['subtotal'],
            'tax' => (float) $data['tax'],
            'delivery_fee' => (float) $data['delivery_fee'],
            'total_amount' => (float) $data['total_amount'],
            'special_request' => Security::sanitize($data['special_request'] ?? '')
        ]);

        // Add order items
        foreach ($cartItems as $item) {
            if ($item['cart_item_id']) {
                $orderRepo->addOrderItem($order['order_id'], [
                    'item_id' => $item['item_id'],
                    'package_id' => $item['package_id'],
                    'item_name' => $item['item_name'] ?? $item['package_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'] ?? $item['price_per_person']
                ]);
            }
        }

        // Clear cart
        $cartRepo->clearCart($userId, $vendorId);

        Session::flash('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order['order_number']);
        return redirect($response, '/order/' . $order['order_id']);

    } catch (Exception $e) {
        Session::flash('error', 'Terjadi kesalahan saat membuat pesanan.');
        return redirect($response, '/checkout');
    }
});

// Orders List
$app->get('/orders', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $orderRepo = new OrderRepository($db);
    $userId = Session::get('user_id');

    $orders = $orderRepo->getOrdersByUser($userId);
    $totalOrders = $orderRepo->getOrderCountByUser($userId);

    return render($response, 'orders', [
        'orders' => $orders,
        'totalOrders' => $totalOrders
    ]);
});

// Order Detail
$app->get('/order/{id}', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $orderId = (int) $args['id'];
    $orderRepo = new OrderRepository($db);
    $reviewRepo = new App\Domain\ReviewRepository($db);
    $userId = Session::get('user_id');

    if (!$orderRepo->validateOwnership($userId, $orderId)) {
        return render($response, '404')->withStatus(404);
    }

    $order = $orderRepo->getOrderById($orderId);
    $orderItems = $orderRepo->getOrderItems($orderId);

    // Check if user already reviewed this order
    $existingReview = $reviewRepo->getReviewByOrder($orderId);
    $hasReview = $existingReview !== false;

    return render($response, 'order_detail', [
        'order' => $order,
        'orderItems' => $orderItems,
        'hasReview' => $hasReview
    ]);
});

// Cancel Order
$app->post('/order/{id}/cancel', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/orders');
    }

    $orderId = (int) $args['id'];
    $orderRepo = new OrderRepository($db);
    $userId = Session::get('user_id');

    if ($orderRepo->validateOwnership($userId, $orderId)) {
        $order = $orderRepo->getOrderById($orderId);
        if ($order && $order['status'] === 'pending') {
            $orderRepo->cancelOrder($orderId, 'Dibatalkan oleh pelanggan');
            Session::flash('success', 'Pesanan berhasil dibatalkan.');
        } else {
            Session::flash('error', 'Pesanan tidak dapat dibatalkan.');
        }
    }

    return redirect($response, '/orders');
});

// ========================================
// REVIEW ROUTES
// ========================================

// Create Review
$app->post('/order/{id}/review', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request.');
        return redirect($response, '/orders');
    }

    $orderId = (int) $args['id'];
    $userId = Session::get('user_id');
    $orderRepo = new OrderRepository($db);
    $reviewRepo = new App\Domain\ReviewRepository($db);

    // Validate ownership
    if (!$orderRepo->validateOwnership($userId, $orderId)) {
        return render($response, '404')->withStatus(404);
    }

    // Check if can review
    if (!$reviewRepo->canReview($userId, $orderId)) {
        Session::flash('error', 'Anda tidak dapat memberikan review untuk pesanan ini.');
        return redirect($response, '/order/' . $orderId);
    }

    $order = $orderRepo->getOrderById($orderId);

    try {
        $reviewRepo->createReview([
            'order_id' => $orderId,
            'user_id' => $userId,
            'vendor_id' => $order['vendor_id'],
            'rating' => (int) ($data['rating'] ?? 5),
            'food_rating' => (int) ($data['food_rating'] ?? null),
            'service_rating' => (int) ($data['service_rating'] ?? null),
            'delivery_rating' => (int) ($data['delivery_rating'] ?? null),
            'review_text' => Security::sanitize($data['review_text'] ?? ''),
            'images' => null
        ]);

        Session::flash('success', 'Terima kasih! Review Anda telah dikirim.');
    } catch (Exception $e) {
        Session::flash('error', 'Terjadi kesalahan saat mengirim review.');
    }

    return redirect($response, '/order/' . $orderId);
});

// View Vendor Reviews
$app->get('/vendor/{id}/reviews', function (Request $request, Response $response, $args) use ($db) {
    $vendorId = (int) $args['id'];
    $vendorRepo = new VendorRepository($db);
    $reviewRepo = new App\Domain\ReviewRepository($db);

    $vendor = $vendorRepo->getVendorById($vendorId);
    if (!$vendor) {
        return render($response, '404')->withStatus(404);
    }

    $reviews = $reviewRepo->getReviewsByVendor($vendorId);
    $ratingSummary = $reviewRepo->getVendorRatingSummary($vendorId);

    $cartItemCount = 0;
    if (Session::isLoggedIn()) {
        $cartRepo = new CartRepository($db);
        $cartItemCount = $cartRepo->getCartItemCount(Session::get('user_id'));
    }

    return render($response, 'vendor_reviews', [
        'vendor' => $vendor,
        'reviews' => $reviews,
        'ratingSummary' => $ratingSummary,
        'cartItemCount' => $cartItemCount
    ]);
});

// ========================================
// PAYMENT ROUTES
// ========================================

// Create Payment
$app->post('/order/{id}/pay', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request.');
        return redirect($response, '/orders');
    }

    $orderId = (int) $args['id'];
    $userId = Session::get('user_id');
    $orderRepo = new OrderRepository($db);
    $paymentRepo = new App\Domain\PaymentRepository($db);

    if (!$orderRepo->validateOwnership($userId, $orderId)) {
        return render($response, '404')->withStatus(404);
    }

    $order = $orderRepo->getOrderById($orderId);
    $method = Security::sanitize($data['payment_method'] ?? 'transfer');
    $amount = (float) ($data['amount'] ?? $order['total_amount']);

    try {
        $paymentId = $paymentRepo->createPayment($orderId, $method, $amount);
        if ($paymentId) {
            Session::flash('success', 'Pembayaran berhasil dicatat. Silakan upload bukti pembayaran.');
        } else {
            Session::flash('error', 'Metode pembayaran tidak valid.');
        }
    } catch (Exception $e) {
        Session::flash('error', 'Terjadi kesalahan saat memproses pembayaran.');
    }

    return redirect($response, '/order/' . $orderId);
});

// ========================================
// PROMO ROUTES
// ========================================

// Validate Promo Code
$app->post('/promo/validate', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return json($response, ['error' => 'Please login first'], 401);
    }

    $data = $request->getParsedBody();
    $promoRepo = new App\Domain\PromoRepository($db);

    $code = Security::sanitize($data['promo_code'] ?? '');
    $orderAmount = (float) ($data['order_amount'] ?? 0);
    $vendorId = (int) ($data['vendor_id'] ?? 0);

    $promo = $promoRepo->validatePromo($code, $orderAmount, $vendorId ?: null);

    if (!$promo) {
        return json($response, ['success' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
    }

    // Check if user already used
    if ($promoRepo->hasUserUsedPromo($promo['promo_id'], Session::get('user_id'))) {
        return json($response, ['success' => false, 'message' => 'Anda sudah pernah menggunakan promo ini.']);
    }

    $discount = $promoRepo->calculateDiscount($promo, $orderAmount);

    return json($response, [
        'success' => true,
        'promo' => [
            'promo_id' => $promo['promo_id'],
            'promo_name' => $promo['promo_name'],
            'discount_amount' => $discount
        ]
    ]);
});

// ========================================
// USER PROFILE ROUTES
// ========================================

// Update Password
$app->post('/profile/password', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request.');
        return redirect($response, '/profile');
    }

    $userId = Session::get('user_id');
    $userRepo = new UserRepository($db);
    $user = $userRepo->findById($userId);

    // Verify current password
    $fullUser = $userRepo->findByEmail($user['email']);
    if (!Security::verifyPassword($data['current_password'] ?? '', $fullUser['password_hash'])) {
        Session::flash('error', 'Password saat ini salah.');
        return redirect($response, '/profile');
    }

    // Validate new password
    $passwordErrors = Security::validatePasswordStrength($data['new_password'] ?? '');
    if (!empty($passwordErrors)) {
        Session::flash('error', implode(', ', $passwordErrors));
        return redirect($response, '/profile');
    }

    if ($data['new_password'] !== $data['confirm_password']) {
        Session::flash('error', 'Konfirmasi password tidak cocok.');
        return redirect($response, '/profile');
    }

    // Update password
    $userRepo->updatePassword($userId, $data['new_password']);
    Session::flash('success', 'Password berhasil diubah.');
    return redirect($response, '/profile');
});

// ========================================
// VENDORS LIST PAGE
// ========================================

$app->get('/vendors', function (Request $request, Response $response) use ($db) {
    $params = $request->getQueryParams();
    $vendorRepo = new VendorRepository($db);
    $categoryRepo = new CategoryRepository($db);

    $vendors = $vendorRepo->getAllVendors();
    $categories = $categoryRepo->getAllCategories();
    $cities = array_unique(array_column($vendors, 'city'));

    $cartItemCount = 0;
    if (Session::isLoggedIn()) {
        $cartRepo = new CartRepository($db);
        $cartItemCount = $cartRepo->getCartItemCount(Session::get('user_id'));
    }

    return render($response, 'vendors', [
        'vendors' => $vendors,
        'categories' => $categories,
        'cities' => $cities,
        'cartItemCount' => $cartItemCount
    ]);
});

// ========================================
// CONTACT FORM HANDLER
// ========================================

$app->post('/contact', function (Request $request, Response $response) use ($db) {
    $data = $request->getParsedBody();

    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        Session::flash('error', 'Invalid request.');
        return redirect($response, '/contact');
    }

    // Rate limiting
    if (!Security::checkRateLimit('contact_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 3, 300)) {
        Session::flash('error', 'Terlalu banyak pesan. Coba lagi dalam 5 menit.');
        return redirect($response, '/contact');
    }

    $name = Security::sanitize($data['name'] ?? '');
    $email = Security::sanitizeEmail($data['email'] ?? '');
    $subject = Security::sanitize($data['subject'] ?? '');
    $message = Security::sanitize($data['message'] ?? '');

    // Validation
    if (strlen($name) < 2 || !Security::validateEmail($email) || strlen($message) < 10) {
        Session::flash('error', 'Mohon lengkapi semua field dengan benar.');
        return redirect($response, '/contact');
    }

    // Save to database
    try {
        $contactRepo = new App\Domain\ContactRepository($db);
        $contactRepo->createMessage($name, $email, $subject, $message);
        Session::flash('success', 'Terima kasih! Pesan Anda telah terkirim. Kami akan menghubungi Anda segera.');
    } catch (Exception $e) {
        Session::flash('error', 'Terjadi kesalahan. Silakan coba lagi.');
    }
    return redirect($response, '/contact');
});

// ========================================
// ADMIN ROUTES
// ========================================

// Admin - Update Order Status
$app->post('/admin/order/{id}/status', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        Session::flash('error', 'Akses ditolak.');
        return redirect($response, '/');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/admin');
    }

    $orderId = (int) $args['id'];
    $status = Security::sanitize($data['status'] ?? '');
    $validStatuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled'];

    if (in_array($status, $validStatuses)) {
        $stmt = $db->prepare("UPDATE orders SET status = :status, updated_at = NOW() WHERE order_id = :order_id");
        $stmt->execute([':status' => $status, ':order_id' => $orderId]);
        Session::flash('success', 'Status pesanan berhasil diupdate.');
    }

    return redirect($response, '/admin');
});

// Admin - Verify Payment
$app->post('/admin/payment/{id}/verify', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        Session::flash('error', 'Akses ditolak.');
        return redirect($response, '/');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/admin');
    }

    $paymentId = (int) $args['id'];
    $paymentRepo = new App\Domain\PaymentRepository($db);
    $paymentRepo->verifyPayment($paymentId, Session::get('user_id'));

    Session::flash('success', 'Pembayaran berhasil diverifikasi.');
    return redirect($response, '/admin');
});

// Admin Dashboard
$app->get('/admin', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));

    if ($user['role'] !== 'admin') {
        Session::flash('error', 'Akses ditolak. Halaman ini khusus administrator.');
        return redirect($response, '/profile');
    }

    // Get Dashboard Stats
    $vendorRepo = new VendorRepository($db);
    $orderRepo = new OrderRepository($db);
    $userRepo = new UserRepository($db);

    // Simple stats queries (direct DB for now for aggregates not in repos)
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    $totalCustomers = $stmt->fetchColumn();

    $stmt = $db->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'");
    $totalRevenue = $stmt->fetchColumn() ?: 0;

    $vendors = $vendorRepo->getAllVendors();

    // Get recent orders
    $stmt = $db->query("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY created_at DESC LIMIT 5");
    $recentOrders = $stmt->fetchAll();

    // Get contact messages
    $contactRepo = new App\Domain\ContactRepository($db);
    $contactMessages = $contactRepo->getRecentMessages(5);
    $unreadMessages = $contactRepo->getUnreadCount();

    return render($response, 'admin_dashboard', [
        'totalCustomers' => $totalCustomers,
        'totalRevenue' => $totalRevenue,
        'totalVendors' => count($vendors),
        'totalOrders' => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'recentOrders' => $recentOrders,
        'vendors' => $vendors,
        'contactMessages' => $contactMessages,
        'unreadMessages' => $unreadMessages
    ]);
});

// Admin - View All Messages
$app->get('/admin/messages', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        Session::flash('error', 'Akses ditolak.');
        return redirect($response, '/');
    }

    $contactRepo = new App\Domain\ContactRepository($db);
    $messages = $contactRepo->getAllMessages(100);
    $unreadCount = $contactRepo->getUnreadCount();

    return render($response, 'admin_messages', [
        'messages' => $messages,
        'unreadCount' => $unreadCount
    ]);
});

// Admin - View Single Message
$app->get('/admin/messages/{id}', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        return redirect($response, '/');
    }

    $contactRepo = new App\Domain\ContactRepository($db);
    $message = $contactRepo->getById((int) $args['id']);

    if (!$message) {
        return render($response, '404')->withStatus(404);
    }

    // Mark as read
    $contactRepo->markAsRead((int) $args['id']);

    return render($response, 'admin_message_detail', [
        'message' => $message
    ]);
});

// Admin - Delete Message
$app->post('/admin/messages/{id}/delete', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        return redirect($response, '/');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/admin/messages');
    }

    $contactRepo = new App\Domain\ContactRepository($db);
    $contactRepo->delete((int) $args['id']);

    Session::flash('success', 'Pesan berhasil dihapus.');
    return redirect($response, '/admin/messages');
});

// Admin - Mark All Messages as Read
$app->post('/admin/messages/mark-all-read', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'admin') {
        return redirect($response, '/');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/admin/messages');
    }

    $contactRepo = new App\Domain\ContactRepository($db);
    $contactRepo->markAllAsRead();

    Session::flash('success', 'Semua pesan telah ditandai sudah dibaca.');
    return redirect($response, '/admin/messages');
});

// ========================================
// VENDOR ROUTES
// ========================================

// Vendor Dashboard
$app->get('/vendor-dashboard', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));

    if ($user['role'] !== 'vendor') {
        Session::flash('error', 'Akses ditolak. Halaman ini khusus vendor.');
        return redirect($response, '/profile');
    }

    // Get vendor data for this user
    $stmt = $db->prepare("SELECT * FROM vendors WHERE user_id = :user_id AND status = 'active' LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor) {
        Session::flash('error', 'Vendor tidak ditemukan atau belum diaktifkan.');
        return redirect($response, '/profile');
    }

    $vendorId = $vendor['vendor_id'];

    // Get stats
    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE vendor_id = :vid");
    $stmt->execute([':vid' => $vendorId]);
    $totalOrders = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE vendor_id = :vid AND status = 'pending'");
    $stmt->execute([':vid' => $vendorId]);
    $pendingOrders = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE vendor_id = :vid AND status = 'completed'");
    $stmt->execute([':vid' => $vendorId]);
    $totalRevenue = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM menu_items WHERE vendor_id = :vid AND is_available = TRUE");
    $stmt->execute([':vid' => $vendorId]);
    $totalMenuItems = $stmt->fetchColumn();

    // Get recent orders
    $stmt = $db->prepare("SELECT o.*, u.full_name as user_name FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.vendor_id = :vid ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([':vid' => $vendorId]);
    $recentOrders = $stmt->fetchAll();

    // Get recent reviews
    $stmt = $db->prepare("SELECT r.*, u.full_name as user_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.vendor_id = :vid ORDER BY r.created_at DESC LIMIT 3");
    $stmt->execute([':vid' => $vendorId]);
    $recentReviews = $stmt->fetchAll();

    return render($response, 'vendor_dashboard', [
        'vendor' => $vendor,
        'totalOrders' => $totalOrders,
        'pendingOrders' => $pendingOrders,
        'totalRevenue' => $totalRevenue,
        'totalMenuItems' => $totalMenuItems,
        'recentOrders' => $recentOrders,
        'recentReviews' => $recentReviews
    ]);
});

// Vendor - Update Order Status
$app->post('/vendor/order/{id}/status', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'vendor') {
        Session::flash('error', 'Akses ditolak.');
        return redirect($response, '/');
    }

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/vendor-dashboard');
    }

    // Get vendor for this user
    $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor) {
        return redirect($response, '/vendor-dashboard');
    }

    $orderId = (int) $args['id'];
    $status = Security::sanitize($data['status'] ?? '');
    $validStatuses = ['confirmed', 'preparing', 'delivering', 'completed'];

    // Verify order belongs to this vendor
    $stmt = $db->prepare("SELECT vendor_id FROM orders WHERE order_id = :oid");
    $stmt->execute([':oid' => $orderId]);
    $order = $stmt->fetch();

    if ($order && $order['vendor_id'] == $vendor['vendor_id'] && in_array($status, $validStatuses)) {
        $stmt = $db->prepare("UPDATE orders SET status = :status, updated_at = NOW() WHERE order_id = :order_id");
        $stmt->execute([':status' => $status, ':order_id' => $orderId]);
        Session::flash('success', 'Status pesanan berhasil diupdate.');
    }

    return redirect($response, '/vendor-dashboard');
});

// Vendor - Orders List
$app->get('/vendor/orders', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'vendor') {
        return redirect($response, '/');
    }

    $stmt = $db->prepare("SELECT vendor_id, vendor_name FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor) {
        return redirect($response, '/profile');
    }

    $params = $request->getQueryParams();
    $status = $params['status'] ?? '';
    $date = $params['date'] ?? '';
    $query = $params['q'] ?? '';

    $sql = "SELECT o.*, u.full_name as user_name FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.vendor_id = :vid";

    $whereParams = [':vid' => $vendor['vendor_id']];

    if (!empty($status)) {
        $sql .= " AND o.status = :status";
        $whereParams[':status'] = $status;
    }

    if (!empty($date)) {
        $sql .= " AND o.event_date = :date";
        $whereParams[':date'] = $date;
    }

    if (!empty($query)) {
        $sql .= " AND (o.order_number LIKE :query OR u.full_name LIKE :query)";
        $whereParams[':query'] = "%$query%";
    }

    $sql .= " ORDER BY o.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($whereParams);
    $orders = $stmt->fetchAll();

    return render($response, 'vendor_orders', [
        'vendor' => $vendor,
        'orders' => $orders,
        'filters' => $params
    ]);
});

// Vendor - Menu Management
$app->get('/vendor/menu', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    $userRepo = new UserRepository($db);
    $user = $userRepo->findById(Session::get('user_id'));
    if ($user['role'] !== 'vendor') {
        return redirect($response, '/');
    }

    $stmt = $db->prepare("SELECT vendor_id, vendor_name FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor) {
        return redirect($response, '/profile');
    }

    $menuRepo = new MenuRepository($db);
    $menuItems = $menuRepo->getAllVendorItems($vendor['vendor_id']);

    $categoryRepo = new CategoryRepository($db);
    $categories = $categoryRepo->getAllCategories();

    return render($response, 'vendor_menu', [
        'vendor' => $vendor,
        'menuItems' => $menuItems,
        'categories' => $categories
    ]);
});

// Vendor - Add Menu Item
$app->post('/vendor/menu/add', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn()) {
        return redirect($response, '/login');
    }

    // Auth & Permission checks consistent with above...
    $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor)
        return redirect($response, '/vendor-dashboard');

    $data = $request->getParsedBody();
    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/vendor/menu');
    }

    // Handle Image Upload (Simplified)
    $imageUrl = null;
    // In a real implementation, handle $_FILES['image'] upload here

    $stmt = $db->prepare("INSERT INTO menu_items (vendor_id, category_id, item_name, description, price, preparation_time, is_available, image_url) VALUES (:vid, :cat, :name, :desc, :price, :prep, :avail, :img)");
    $stmt->execute([
        ':vid' => $vendor['vendor_id'],
        ':cat' => (int) $data['category_id'],
        ':name' => Security::sanitize($data['item_name']),
        ':desc' => Security::sanitize($data['description']),
        ':price' => (float) $data['price'],
        ':prep' => (int) ($data['preparation_time'] ?? 15),
        ':avail' => (int) ($data['is_available'] ?? 1),
        ':img' => $imageUrl
    ]);

    Session::flash('success', 'Menu berhasil ditambahkan.');
    return redirect($response, '/vendor/menu');
});

// Vendor - Update Menu Item
$app->post('/vendor/menu/{id}/update', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn())
        return redirect($response, '/login');

    $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor)
        return redirect($response, '/vendor-dashboard');

    $data = $request->getParsedBody();
    $itemId = (int) $args['id'];

    if (!Security::validateCsrfToken($data['csrf_token'] ?? '')) {
        return redirect($response, '/vendor/menu');
    }

    // Verify ownership
    $stmt = $db->prepare("SELECT vendor_id FROM menu_items WHERE item_id = :id");
    $stmt->execute([':id' => $itemId]);
    $item = $stmt->fetch();

    if ($item && $item['vendor_id'] == $vendor['vendor_id']) {
        $stmt = $db->prepare("UPDATE menu_items SET category_id = :cat, item_name = :name, description = :desc, price = :price, preparation_time = :prep, is_available = :avail WHERE item_id = :id");
        $stmt->execute([
            ':cat' => (int) $data['category_id'],
            ':name' => Security::sanitize($data['item_name']),
            ':desc' => Security::sanitize($data['description']),
            ':price' => (float) $data['price'],
            ':prep' => (int) ($data['preparation_time'] ?? 15),
            ':avail' => (int) ($data['is_available'] ?? 1),
            ':id' => $itemId
        ]);
        Session::flash('success', 'Menu berhasil diupdate.');
    }

    return redirect($response, '/vendor/menu');
});

// Vendor - Delete Menu Item
$app->post('/vendor/menu/{id}/delete', function (Request $request, Response $response, $args) use ($db) {
    if (!Session::isLoggedIn())
        return redirect($response, '/login');

    $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor)
        return redirect($response, '/vendor-dashboard');

    $itemId = (int) $args['id'];

    // Verify ownership
    $stmt = $db->prepare("SELECT vendor_id FROM menu_items WHERE item_id = :id");
    $stmt->execute([':id' => $itemId]);
    $item = $stmt->fetch();

    if ($item && $item['vendor_id'] == $vendor['vendor_id']) {
        $stmt = $db->prepare("DELETE FROM menu_items WHERE item_id = :id");
        $stmt->execute([':id' => $itemId]);
        Session::flash('success', 'Menu berhasil dihapus.');
    }

    return redirect($response, '/vendor/menu');
});

// Vendor - Reviews
$app->get('/vendor/reviews', function (Request $request, Response $response) use ($db) {
    if (!Session::isLoggedIn())
        return redirect($response, '/login');

    $stmt = $db->prepare("SELECT vendor_id, vendor_name FROM vendors WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => Session::get('user_id')]);
    $vendor = $stmt->fetch();

    if (!$vendor)
        return redirect($response, '/profile');

    $reviewRepo = new App\Domain\ReviewRepository($db);
    $reviews = $reviewRepo->getReviewsByVendor($vendor['vendor_id']);
    $ratingSummary = $reviewRepo->getVendorRatingSummary($vendor['vendor_id']);

    return render($response, 'vendor_reviews', [
        'vendor' => $vendor,
        'reviews' => $reviews,
        'ratingSummary' => $ratingSummary
    ]);
});

// 404 Handler - Catch All (must be after all other routes)
// 2. Vendor Detail Route - List Menu (Moved to bottom to avoid conflict)
$app->get('/vendor/{id}', function (Request $request, Response $response, $args) use ($db) {
    $vendorId = (int) $args['id'];

    $vendorRepo = new VendorRepository($db);
    $menuRepo = new MenuRepository($db);

    $vendor = $vendorRepo->getVendorById($vendorId);

    if (!$vendor) {
        return render($response, '404')->withStatus(404);
    }

    $menuItems = $menuRepo->getItemsByVendorId($vendorId);

    $cartItemCount = 0;
    if (Session::isLoggedIn()) {
        $cartRepo = new CartRepository($db);
        $cartItemCount = $cartRepo->getCartItemCount(Session::get('user_id'));
    }

    return render($response, 'vendor_detail', [
        'vendor' => $vendor,
        'menuItems' => $menuItems,
        'cartItemCount' => $cartItemCount
    ]);
});

// 404 Handler - Catch All (must be after all other routes)
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response, $args) {
    return render($response, '404')->withStatus(404);
});

// Run App
$app->run();