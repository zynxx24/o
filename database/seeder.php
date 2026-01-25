<?php
/**
 * Katering Online - BRUTAL Database Seeder
 * 
 * Generates massive amounts of test data
 * Usage: php database/seeder.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Middleware\Security;

echo "====================================\n";
echo "Katering Online - BRUTAL Seeder\n";
echo "====================================\n\n";

$database = new Database();
$db = $database->connect();

if (!$db) {
    die("Error: Could not connect to database.\n");
}

echo "[✓] Connected to database\n\n";

// Clear existing data
echo "Clearing existing data...\n";
try {
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = ['promo_usage', 'payments', 'reviews', 'order_items', 'orders', 'cart_items', 'cart', 'menu_items', 'packages', 'categories', 'vendors', 'promos', 'users'];
    foreach ($tables as $table) {
        try {
            $db->exec("TRUNCATE TABLE $table");
        } catch (Exception $e) {
        }
    }
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "[✓] Cleared existing data\n\n";
} catch (PDOException $e) {
    echo "[!] Warning: Could not clear some tables.\n\n";
}

$passwordHash = Security::hashPassword('password123');

// ========================================
// 1. Create 50 Users
// ========================================
echo "Creating users (50)...\n";

$firstNames = ['Budi', 'Siti', 'Andi', 'Dewi', 'Rudi', 'Maya', 'Agus', 'Linda', 'Joko', 'Ratna', 'Hendra', 'Putri', 'Doni', 'Rina', 'Fajar', 'Wulan', 'Bayu', 'Tari', 'Gilang', 'Nisa', 'Rizki', 'Ayu', 'Dimas', 'Fitri', 'Yusuf', 'Indah', 'Wahyu', 'Sari', 'Arif', 'Mega', 'Taufik', 'Lestari', 'Surya', 'Wati', 'Bambang', 'Yuni', 'Eko', 'Dewanti', 'Herman', 'Kartini', 'Nur', 'Retno', 'Sigit', 'Wening', 'Tri', 'Anggi', 'Bima', 'Citra', 'Dian'];
$lastNames = ['Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Sari', 'Hidayat', 'Nugroho', 'Setiawan', 'Rahayu', 'Wibowo', 'Putra', 'Lestari', 'Utami', 'Firmansyah', 'Hartono', 'Susanto', 'Kurniawan', 'Purnama', 'Saputra', 'Permana'];
$cities = ['Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara', 'Tangerang', 'Bekasi', 'Depok', 'Bogor', 'Bandung'];

$users = [['admin', 'admin@kateringonline.com', 'Administrator', 'admin', '081234567890', 'Jakarta Pusat']];
for ($i = 0; $i < 49; $i++) {
    $fname = $firstNames[array_rand($firstNames)];
    $lname = $lastNames[array_rand($lastNames)];
    $fullName = "$fname $lname";
    $username = strtolower($fname) . rand(100, 999);
    $email = strtolower($fname) . rand(1, 999) . '@example.com';
    $phone = '08' . rand(1000000000, 9999999999);
    $city = $cities[array_rand($cities)];
    $users[] = [$username, $email, $fullName, 'customer', $phone, $city];
}

$stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, role, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
foreach ($users as $user) {
    try {
        $stmt->execute([$user[0], $user[1], $passwordHash, $user[2], $user[3], $user[4], $user[5]]);
    } catch (Exception $e) {
    }
}
echo "[✓] Created " . count($users) . " users\n";

// ========================================
// 2. Create 20 Vendors
// ========================================
echo "Creating vendors (20)...\n";

$vendorNames = [
    'Dapur Nusantara',
    'Catering Berkah',
    'Royal Kitchen',
    'Warung Makan Ibu',
    'Sajian Prima',
    'Dapur Sehat',
    'Nasi Box Express',
    'Catering Harmoni',
    'Snack Corner',
    'Bumbu Desa',
    'Pawon Jawa',
    'Masakan Padang Asli',
    'Sunda Lezat',
    'Betawi Catering',
    'Chinese Food Box',
    'Japanese Bento',
    'Western Grill',
    'Seafood Paradise',
    'Vegetarian Kitchen',
    'Premium Catering'
];
$vendorDescs = [
    'Spesialis masakan Indonesia autentik untuk berbagai acara.',
    'Katering halal berkualitas dengan harga terjangkau.',
    'Premium catering untuk event eksklusif.',
    'Masakan rumahan yang lezat untuk acara kantor.',
    'Katering modern dengan sentuhan tradisional.',
    'Menu sehat dan diet-friendly.',
    'Spesialis nasi kotak dengan pengiriman cepat.',
    'Harmoni rasa dalam setiap hidangan.',
    'Aneka snack box dan kue untuk meeting.',
    'Cita rasa masakan desa yang autentik.',
    'Masakan Jawa yang gurih dan nikmat.',
    'Rendang, gulai, dan masakan Padang lainnya.',
    'Masakan Sunda segar dan sehat.',
    'Kuliner khas Betawi yang legendaris.',
    'Chinese food autentik dan halal.',
    'Bento Jepang fresh dan berkualitas.',
    'Steak dan menu western premium.',
    'Seafood segar dari laut Indonesia.',
    'Menu vegetarian lezat dan bergizi.',
    'Katering premium untuk acara mewah.'
];

$stmtUser = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, role, phone, address, status) VALUES (?, ?, ?, ?, 'vendor', ?, ?, 'active')");
$stmtVendor = $db->prepare("INSERT INTO vendors (user_id, vendor_name, description, address, city, phone, email, rating, total_reviews, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

$createdVendors = [];

for ($i = 0; $i < 20; $i++) {
    // 1. Create User for Vendor
    $vendorName = $vendorNames[$i];
    $email = strtolower(str_replace(' ', '', $vendorName)) . '@example.com';
    $username = strtolower(str_replace(' ', '', $vendorName));
    $phone = '08' . rand(1000000000, 9999999999);
    $city = $cities[array_rand($cities)];
    $address = 'Jl. Contoh No. ' . rand(1, 100) . ', ' . $city;

    try {
        $stmtUser->execute([$username, $email, $passwordHash, $vendorName, $phone, $city]);
        $userId = $db->lastInsertId();

        // 2. Create Vendor linked to User
        $stmtVendor->execute([
            $userId,
            $vendorName,
            $vendorDescs[$i],
            $address,
            $city,
            $phone,
            $email,
            round(rand(35, 50) / 10, 1),
            rand(50, 300)
        ]);

        // Store first vendor for summary
        if ($i === 0) {
            $createdVendors[] = ['email' => $email, 'password' => 'password123'];
        }

    } catch (Exception $e) {
        echo "[!] Error creating vendor $vendorName: " . $e->getMessage() . "\n";
    }
}
echo "[✓] Created 20 vendors with user accounts\n";

// ========================================
// 3. Create Categories
// ========================================
echo "Creating categories...\n";

$categories = [
    ['Makanan Utama', 'Nasi, lauk pauk'],
    ['Appetizer', 'Hidangan pembuka'],
    ['Soup', 'Aneka sup dan soto'],
    ['Dessert', 'Hidangan penutup'],
    ['Nasi Kotak', 'Paket nasi kotak'],
    ['Prasmanan', 'Menu prasmanan'],
    ['Western', 'Menu internasional'],
    ['Indonesian', 'Menu tradisional'],
    ['Seafood', 'Hidangan laut'],
    ['Snack Box', 'Paket snack'],
    ['Paket Hemat', 'Menu ekonomis'],
    ['Paket Premium', 'Menu premium'],
    ['Diet Menu', 'Menu rendah kalori'],
    ['Vegan', 'Menu tanpa daging'],
    ['Wedding Package', 'Paket pernikahan'],
    ['Traditional', 'Menu tradisional']
];

$stmt = $db->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
foreach ($categories as $cat) {
    $stmt->execute($cat);
}
echo "[✓] Created " . count($categories) . " categories\n";

// ========================================
// 4. Create Menu Items (100+)
// ========================================
echo "Creating menu items (100+)...\n";

$menuNames = [
    'Nasi Goreng Spesial',
    'Ayam Bakar Bumbu Rujak',
    'Rendang Daging Sapi',
    'Lumpia Semarang',
    'Soto Ayam Lamongan',
    'Es Cendol',
    'Nasi Kotak Ayam Goreng',
    'Nasi Kotak Ikan Bakar',
    'Paket Prasmanan A',
    'Paket Prasmanan B',
    'Grilled Salmon',
    'Beef Steak',
    'Sate Ayam Madura',
    'Udang Goreng Mentega',
    'Snack Box Standard',
    'Snack Box Premium',
    'Paket Hemat 1',
    'Paket Premium',
    'Salad Bowl',
    'Nasi Merah Sayuran',
    'Nasi Ayam Geprek',
    'Nasi Daging Lada Hitam',
    'Wedding Silver',
    'Wedding Gold',
    'Tumpeng Nasi Kuning',
    'Brownies Box',
    'Martabak Mini',
    'Nasi Liwet Komplit',
    'Karedok Sunda',
    'Gado-Gado Jakarta',
    'Rawon Surabaya',
    'Sop Buntut',
    'Ayam Penyet',
    'Bebek Goreng Sambal',
    'Ikan Bakar Jimbaran',
    'Cumi Goreng Tepung'
];

$stmt = $db->prepare("INSERT INTO menu_items (vendor_id, category_id, item_name, description, price, unit, min_order, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
$menuCount = 0;
for ($v = 1; $v <= 20; $v++) {
    $numItems = rand(5, 8);
    for ($i = 0; $i < $numItems; $i++) {
        $name = $menuNames[array_rand($menuNames)] . ' ' . chr(65 + $i);
        $price = rand(15, 150) * 1000;
        $stmt->execute([
            $v,
            rand(1, count($categories)),
            $name,
            "Menu lezat dari vendor kami",
            $price,
            'porsi',
            rand(5, 20)
        ]);
        $menuCount++;
    }
}
echo "[✓] Created $menuCount menu items\n";

// ========================================
// 5. Create 500 Orders
// ========================================
echo "Creating orders (500)...\n";

$orderStatuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled'];
$paymentStatuses = ['unpaid', 'paid', 'partial'];
$eventTypes = ['Pernikahan', 'Ulang Tahun', 'Meeting', 'Arisan', 'Gathering', 'Corporate Event', 'Syukuran', 'Khitanan', 'Seminar', 'Workshop'];

$orderCount = 0;
$orderItemCount = 0;

for ($i = 0; $i < 500; $i++) {
    $userId = rand(2, 50);
    $vendorId = rand(1, 20);
    $orderNumber = 'ORD-2026-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
    $status = $orderStatuses[array_rand($orderStatuses)];
    $paymentStatus = $status === 'completed' ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)];
    $eventType = $eventTypes[array_rand($eventTypes)];
    $eventDate = date('Y-m-d', strtotime('+' . rand(-30, 90) . ' days'));
    $numPeople = rand(20, 500);
    $subtotal = rand(1000000, 50000000);
    $tax = $subtotal * 0.1;
    $deliveryFee = rand(0, 500000);
    $total = $subtotal + $tax + $deliveryFee;

    $stmt = $db->prepare("INSERT INTO orders (user_id, vendor_id, order_number, order_type, event_type, event_date, event_time, delivery_address, delivery_city, num_people, subtotal, tax, delivery_fee, total_amount, status, payment_status, created_at) VALUES (?, ?, ?, 'custom', ?, ?, '12:00:00', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 60) . ' days'));
    $stmt->execute([
        $userId,
        $vendorId,
        $orderNumber,
        $eventType,
        $eventDate,
        'Jl. ' . $cities[array_rand($cities)] . ' No. ' . rand(1, 200),
        $cities[array_rand($cities)],
        $numPeople,
        $subtotal,
        $tax,
        $deliveryFee,
        $total,
        $status,
        $paymentStatus,
        $createdAt
    ]);
    $orderId = $db->lastInsertId();
    $orderCount++;

    // Add 2-6 order items
    $itemCount = rand(2, 6);
    for ($j = 0; $j < $itemCount; $j++) {
        $quantity = rand(10, 100);
        $unitPrice = rand(20000, 150000);
        $stmt2 = $db->prepare("INSERT INTO order_items (order_id, item_name, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt2->execute([$orderId, $menuNames[array_rand($menuNames)], $quantity, $unitPrice, $quantity * $unitPrice]);
        $orderItemCount++;
    }
}
echo "[✓] Created $orderCount orders with $orderItemCount items\n";

// ========================================
// 6. Create 1000 Reviews
// ========================================
echo "Creating reviews (1000)...\n";

$reviewComments = [
    'Makanan enak sekali! Pelayanan juga ramah dan profesional.',
    'Porsi besar, rasa mantap. Pasti pesan lagi untuk acara selanjutnya.',
    'Pengiriman tepat waktu, makanan masih hangat dan fresh.',
    'Sangat memuaskan untuk acara kantor kami. Recommended!',
    'Harga sesuai dengan kualitas. Worth it banget!',
    'Bumbu meresap, rasa autentik Indonesia yang khas.',
    'Paket prasmanan lengkap dan variatif, tamu puas semua.',
    'Tamu undangan puas dengan makanannya, pelayanan juga bagus.',
    'Pelayanan profesional, makanan lezat, pasti repeat order.',
    'Akan pesan lagi untuk acara berikutnya. Top markotop!',
    'Rasanya konsisten dari pesanan sebelumnya. Mantap!',
    'Packaging rapi dan higienis, tidak ada yang tumpah.',
    'Menu beragam dan semuanya enak. 5 stars!',
    'Harga bersaing dengan kualitas premium.',
    'Tim katering sangat kooperatif dan helpful.',
    'Sesuai ekspektasi, bahkan melebihi harapan kami.',
    'Makanan segar dan tidak menggunakan MSG berlebihan.',
    'Dekorasi makanan cantik, instagramable banget!',
    'Fast response dan sangat informatif.',
    'Pilihan menu halal dan enak semua.'
];

$reviewCount = 0;
$stmt = $db->prepare("INSERT INTO reviews (order_id, user_id, vendor_id, rating, food_rating, service_rating, delivery_rating, review_text, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

for ($i = 0; $i < 1000; $i++) {
    $orderId = rand(1, 500);
    $userId = rand(2, 50);
    $vendorId = rand(1, 20);
    $rating = rand(3, 5);
    $foodRating = rand(3, 5);
    $serviceRating = rand(3, 5);
    $deliveryRating = rand(3, 5);
    $comment = $reviewComments[array_rand($reviewComments)];
    $createdAt = date('Y-m-d H:i:s', strtotime('-' . rand(0, 60) . ' days'));

    try {
        $stmt->execute([$orderId, $userId, $vendorId, $rating, $foodRating, $serviceRating, $deliveryRating, $comment, $createdAt]);
        $reviewCount++;
    } catch (Exception $e) {
    }
}
echo "[✓] Created $reviewCount reviews\n";

// ========================================
// 7. Create 500 Payments
// ========================================
echo "Creating payments (500)...\n";

$paymentMethods = ['transfer', 'credit_card', 'e-wallet', 'cash', 'cod'];
$paymentStatuses = ['pending', 'verified', 'failed'];

$paymentCount = 0;
$stmt = $db->prepare("INSERT INTO payments (order_id, payment_method, amount, payment_status, payment_date) VALUES (?, ?, ?, ?, ?)");

for ($i = 0; $i < 500; $i++) {
    $orderId = rand(1, 500);
    $method = $paymentMethods[array_rand($paymentMethods)];
    $amount = rand(500000, 50000000);
    $status = $paymentStatuses[array_rand($paymentStatuses)];
    $paymentDate = date('Y-m-d H:i:s', strtotime('-' . rand(0, 60) . ' days'));

    try {
        $stmt->execute([$orderId, $method, $amount, $status, $paymentDate]);
        $paymentCount++;
    } catch (Exception $e) {
    }
}
echo "[✓] Created $paymentCount payments\n";

// ========================================
// 8. Create Promos
// ========================================
echo "Creating promos (10)...\n";

$promos = [
    ['WELCOME50', 'Diskon Member Baru', 'fixed', 50000, 500000, 50000],
    ['DISKON10', 'Diskon 10%', 'percentage', 10, 300000, 100000],
    ['HEMAT20', 'Hemat 20%', 'percentage', 20, 500000, 200000],
    ['NEWUSER', 'Promo User Baru', 'fixed', 100000, 1000000, 100000],
    ['WEEKEND15', 'Weekend Special', 'percentage', 15, 400000, 150000],
    ['CORPORATE', 'Corporate Discount', 'percentage', 25, 2000000, 500000],
    ['WEDDING', 'Wedding Special', 'fixed', 500000, 10000000, 500000],
    ['RAMADAN', 'Promo Ramadan', 'percentage', 15, 300000, 100000],
    ['LEBARAN', 'Promo Lebaran', 'fixed', 200000, 1500000, 200000],
    ['ENDYEAR', 'Year End Sale', 'percentage', 30, 1000000, 300000]
];

$stmt = $db->prepare("INSERT INTO promos (promo_code, promo_name, discount_type, discount_value, min_order, max_discount, valid_from, valid_until, usage_limit, is_active) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 365 DAY), 1000, 1)");
foreach ($promos as $promo) {
    try {
        $stmt->execute($promo);
    } catch (Exception $e) {
    }
}
echo "[✓] Created " . count($promos) . " promos\n";

// ========================================
// Summary
// ========================================
echo "\n====================================\n";
echo "BRUTAL Seeding Complete!\n";
echo "====================================\n\n";
echo "Summary:\n";
echo "- Users: " . count($users) . "\n";
echo "- Vendors: 20\n";
echo "- Categories: " . count($categories) . "\n";
echo "- Menu Items: $menuCount\n";
echo "- Orders: $orderCount\n";
echo "- Order Items: $orderItemCount\n";
echo "- Reviews: $reviewCount\n";
echo "- Payments: $paymentCount\n";
echo "- Promos: " . count($promos) . "\n";
echo "\nLogin Credentials:\n";
echo "- Admin: admin@kateringonline.com / password123\n";
echo "- Any customer email with password123\n\n";
