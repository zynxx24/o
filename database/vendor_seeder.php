<?php
/**
 * Vendor Specific Seeder
 * 
 * Creates vendor accounts and populates their dashboards with data
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Middleware\Security;

echo "====================================\n";
echo "Vendor Data Seeder\n";
echo "====================================\n\n";

$database = new Database();
$db = $database->connect();
$passwordHash = Security::hashPassword('password123');

// 1. Create Vendor Users
echo "Creating vendor users...\n";

$vendors = [
    [
        'user' => ['vendor1', 'vendor1@example.com', 'Dapur Nusantara Owner', 'vendor', '081234567801', 'Jakarta Selatan'],
        'vendor' => ['Dapur Nusantara', 'Spesialis masakan Indonesia autentik.', 'Jl. Fatmawati No. 10', 'Jakarta Selatan']
    ],
    [
        'user' => ['vendor2', 'vendor2@example.com', 'Berkah Catering Owner', 'vendor', '081234567802', 'Jakarta Barat'],
        'vendor' => ['Catering Berkah', 'Katering halal dan higienis.', 'Jl. Panjang No. 5', 'Jakarta Barat']
    ],
    [
        'user' => ['vendor3', 'vendor3@example.com', 'Royal Kitchen Owner', 'vendor', '081234567803', 'Jakarta Pusat'],
        'vendor' => ['Royal Kitchen', 'Premium catering untuk event eksklusif.', 'Jl. Menteng Raya No. 1', 'Jakarta Pusat']
    ]
];

$userIds = [];

$userStmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, role, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
$vendorStmt = $db->prepare("INSERT INTO vendors (user_id, vendor_name, description, address, city, phone, email, rating, total_reviews, status) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 'active')");

foreach ($vendors as $v) {
    // Check if user exists
    $check = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$v['user'][1]]);
    $existing = $check->fetch();

    if ($existing) {
        echo "Updating existing user: {$v['user'][0]}\n";
        $userId = $existing['user_id'];
        // Update to vendor role if not
        $db->prepare("UPDATE users SET role = 'vendor' WHERE user_id = ?")->execute([$userId]);
    } else {
        echo "Creating new user: {$v['user'][0]}\n";
        $userStmt->execute([
            $v['user'][0],
            $v['user'][1],
            $passwordHash,
            $v['user'][2],
            $v['user'][3],
            $v['user'][4],
            $v['user'][5]
        ]);
        $userId = $db->lastInsertId();
    }

    // Check if vendor profile exists
    $checkVendor = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = ?");
    $checkVendor->execute([$userId]);
    $existingVendor = $checkVendor->fetch();

    if ($existingVendor) {
        $vendorId = $existingVendor['vendor_id'];
    } else {
        $vendorStmt->execute([
            $userId,
            $v['vendor'][0],
            $v['vendor'][1],
            $v['vendor'][2],
            $v['vendor'][3],
            $v['user'][4], // Use user phone
            $v['user'][1]  // Use user email
        ]);
        $vendorId = $db->lastInsertId();
    }

    $userIds[$vendorId] = $userId;
    echo "✓ Processed vendor: {$v['vendor'][0]} (ID: $vendorId)\n";

    // 2. Create Menu Items for this vendor
    echo "  > Adding menu items...\n";
    $menuItems = [
        ['Nasi Goreng Spesial', 35000, 1],
        ['Ayam Bakar Madu', 45000, 1],
        ['Sate Ayam (10 tusuk)', 50000, 2],
        ['Sop Iga Sapi', 65000, 3],
        ['Es Teler Juara', 25000, 4]
    ];

    $menuStmt = $db->prepare("INSERT INTO menu_items (vendor_id, category_id, item_name, description, price, preparation_time, is_available) VALUES (?, ?, ?, 'Menu lezat kualitas terbaik', ?, 20, 1)");

    foreach ($menuItems as $item) {
        $menuStmt->execute([$vendorId, $item[2], $item[0], $item[1]]);
    }

    // 3. Create Dummy Orders
    echo "  > Creating orders...\n";
    $orderStmt = $db->prepare("INSERT INTO orders (user_id, vendor_id, order_number, order_type, event_date, event_time, delivery_address, num_people, subtotal, tax, delivery_fee, total_amount, status, payment_status, created_at) VALUES (?, ?, ?, 'custom', ?, '12:00:00', 'Jl. Test No. 123', 50, ?, ?, 50000, ?, ?, 'paid', NOW())");

    $statuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed'];

    for ($i = 0; $i < 10; $i++) {
        $status = $statuses[array_rand($statuses)];
        $subtotal = rand(1000000, 5000000);
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax + 50000;

        // Use a random admin/existing user as customer (user_id 1 is usually admin)
        $customerId = 1;

        $orderStmt->execute([
            $customerId,
            $vendorId,
            'ORD-V' . $vendorId . '-' . rand(1000, 9999),
            date('Y-m-d', strtotime('+' . rand(1, 30) . ' days')),
            $subtotal,
            $tax,
            $total,
            $status
        ]);

        // If completed, add a review
        if ($status === 'completed') {
            $orderId = $db->lastInsertId();
            $reviewStmt = $db->prepare("INSERT INTO reviews (order_id, user_id, vendor_id, rating, food_rating, service_rating, delivery_rating, review_text, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $reviewStmt->execute([
                $orderId,
                $customerId,
                $vendorId,
                5,
                5,
                5,
                5,
                "Layanan sangat memuaskan dari " . $v['vendor'][0] . "!"
            ]);

            // Update vendor stats
            $db->prepare("UPDATE vendors SET rating = 5.0, total_reviews = total_reviews + 1 WHERE vendor_id = ?")->execute([$vendorId]);
        }
    }
}

echo "\nSeeding Complete!\n";
echo "Use these accounts to login:\n";
foreach ($vendors as $v) {
    echo "- Email: {$v['user'][1]} | Password: password123\n";
}
