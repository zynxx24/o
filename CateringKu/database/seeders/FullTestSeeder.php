<?php

namespace Database\Seeders;

use App\Models\{User, Vendor, Category, MenuItem, Order, OrderItem, Payment, Review, Cart, CartItem, Promo, ContactMessage};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FullTestSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        ContactMessage::truncate();
        DB::table('promo_usage')->truncate();
        Promo::truncate();
        DB::table('cart_items')->truncate();
        Cart::truncate();
        Review::truncate();
        Payment::truncate();
        OrderItem::truncate();
        Order::truncate();
        MenuItem::truncate();
        DB::table('packages')->truncate();
        Category::truncate();
        Vendor::truncate();
        User::truncate();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command->info('🧹 Database cleaned.');

        // ══════════════════════════════════════════════
        // 1. ADMIN
        // ══════════════════════════════════════════════
        $admin = User::create([
            'username' => 'admin',
            'name' => 'Super Admin CateringKu',
            'email' => 'admin@cateringku.com',
            'password' => bcrypt('admin123'),
            'phone' => '081200000001',
            'address' => 'Kantor CateringKu, Jl. Sudirman No. 1, Jakarta Pusat',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $this->command->info('👤 Admin created.');

        // ══════════════════════════════════════════════
        // 2. CATEGORIES (8)
        // ══════════════════════════════════════════════
        $catData = [
            ['category_name' => 'Nasi Kotak',    'description' => 'Paket nasi kotak siap saji'],
            ['category_name' => 'Prasmanan',     'description' => 'Menu buffet/prasmanan lengkap'],
            ['category_name' => 'Snack Box',     'description' => 'Aneka snack dan kue kotak'],
            ['category_name' => 'Dessert',       'description' => 'Kue, puding, dan jajanan manis'],
            ['category_name' => 'Minuman',       'description' => 'Berbagai jenis minuman'],
            ['category_name' => 'Tumpeng',       'description' => 'Tumpeng untuk acara spesial'],
            ['category_name' => 'Seafood',       'description' => 'Hidangan laut segar'],
            ['category_name' => 'Tradisional',   'description' => 'Masakan khas Nusantara'],
        ];
        $categories = [];
        foreach ($catData as $c) {
            $categories[] = Category::create($c);
        }
        $this->command->info('📂 8 categories created.');

        // ══════════════════════════════════════════════
        // 3. VENDORS (5) — each with 15 menus
        // ══════════════════════════════════════════════
        $vendorProfiles = [
            [
                'name' => 'Dapur Nusantara Catering',  'user' => 'vendor_nusantara',
                'email' => 'nusantara@cateringku.com',  'city' => 'Jakarta',
                'desc' => 'Spesialis masakan Nusantara autentik dengan cita rasa rumahan. Melayani prasmanan, nasi kotak, dan tumpeng untuk segala acara.',
                'phone' => '081300000001',
            ],
            [
                'name' => 'Sari Rasa Katering',        'user' => 'vendor_sarirasa',
                'email' => 'sarirasa@cateringku.com',   'city' => 'Bandung',
                'desc' => 'Katering premium Bandung dengan menu Sunda modern. Prasmanan elegan dan nasi kotak berkualitas tinggi.',
                'phone' => '081300000002',
            ],
            [
                'name' => 'Berkah Jaya Catering',       'user' => 'vendor_berkah',
                'email' => 'berkah@cateringku.com',     'city' => 'Surabaya',
                'desc' => 'Katering terpercaya Surabaya sejak 2010. Spesialis rawon, rujak cingur, dan masakan Jawa Timur.',
                'phone' => '081300000003',
            ],
            [
                'name' => 'Royal Feast Catering',       'user' => 'vendor_royal',
                'email' => 'royal@cateringku.com',      'city' => 'Jakarta',
                'desc' => 'Premium wedding & corporate catering. Menu Western dan fusion dengan presentasi mewah.',
                'phone' => '081300000004',
            ],
            [
                'name' => 'Padang Bundo Catering',      'user' => 'vendor_padang',
                'email' => 'padang@cateringku.com',     'city' => 'Medan',
                'desc' => 'Masakan Padang asli dengan bumbu rempah pilihan. Rendang juara, ayam pop legendaris.',
                'phone' => '081300000005',
            ],
        ];

        // 15 menus per vendor, varied by vendor theme
        $menuTemplates = [
            // vendor 0: Nusantara
            [
                ['Nasi Goreng Kampung',25000,'porsi',0,'sedang'],['Ayam Bakar Madu',35000,'porsi',0,'tidak pedas'],
                ['Rendang Sapi',45000,'porsi',0,'pedas'],['Soto Betawi',30000,'porsi',0,'tidak pedas'],
                ['Nasi Uduk Komplit',28000,'porsi',0,'tidak pedas'],['Gado-Gado Jakarta',22000,'porsi',0,'tidak pedas'],
                ['Sate Ayam Madura',30000,'porsi',7,'sedang'],['Nasi Tumpeng Mini',85000,'porsi',5,'tidak pedas'],
                ['Es Teh Manis',8000,'gelas',4,'tidak pedas'],['Kue Lapis Legit',15000,'potong',3,'tidak pedas'],
                ['Snack Box Ekonomis',18000,'box',2,'tidak pedas'],['Rawon Surabaya',32000,'porsi',0,'tidak pedas'],
                ['Ikan Bakar Jimbaran',40000,'porsi',6,'sedang'],['Bakso Urat Spesial',25000,'porsi',0,'sedang'],
                ['Puding Coklat',12000,'cup',3,'tidak pedas'],
            ],
            // vendor 1: Sari Rasa (Sunda)
            [
                ['Nasi Timbel Komplit',30000,'porsi',0,'tidak pedas'],['Ayam Goreng Sunda',32000,'porsi',0,'tidak pedas'],
                ['Karedok Segar',20000,'porsi',0,'pedas'],['Empal Gentong',35000,'porsi',0,'sedang'],
                ['Pepes Ikan Mas',28000,'porsi',0,'sedang'],['Sayur Asem Segar',18000,'porsi',0,'tidak pedas'],
                ['Sop Buntut Goreng',55000,'porsi',0,'tidak pedas'],['Nasi Liwet Sunda',25000,'porsi',0,'tidak pedas'],
                ['Bandrek Hangat',12000,'gelas',4,'tidak pedas'],['Surabi Manis',10000,'pcs',3,'tidak pedas'],
                ['Snack Box Premium',25000,'box',2,'tidak pedas'],['Tahu Sumedang',15000,'porsi',2,'pedas'],
                ['Lotek Bandung',22000,'porsi',0,'sedang'],['Batagor Kuah',20000,'porsi',0,'pedas'],
                ['Es Kelapa Muda',15000,'gelas',4,'tidak pedas'],
            ],
            // vendor 2: Berkah Jaya (Jawa Timur)
            [
                ['Rawon Spesial',32000,'porsi',0,'tidak pedas'],['Rujak Cingur',28000,'porsi',0,'pedas'],
                ['Lontong Balap',22000,'porsi',0,'sedang'],['Sate Kambing',45000,'porsi',7,'tidak pedas'],
                ['Nasi Campur Surabaya',30000,'porsi',0,'sedang'],['Tahu Campur',25000,'porsi',0,'sedang'],
                ['Pecel Madiun',20000,'porsi',0,'pedas'],['Bebek Goreng',38000,'porsi',0,'tidak pedas'],
                ['Es Daluman',10000,'gelas',4,'tidak pedas'],['Klepon',8000,'porsi',3,'tidak pedas'],
                ['Snack Box Jumbo',22000,'box',2,'tidak pedas'],['Nasi Krawu',25000,'porsi',0,'sedang'],
                ['Soto Lamongan',28000,'porsi',0,'tidak pedas'],['Lontong Kupang',20000,'porsi',0,'tidak pedas'],
                ['Onde-Onde',10000,'porsi',3,'tidak pedas'],
            ],
            // vendor 3: Royal Feast (Western/Fusion)
            [
                ['Beef Steak Medium',75000,'porsi',0,'tidak pedas'],['Chicken Cordon Bleu',55000,'porsi',0,'tidak pedas'],
                ['Caesar Salad',35000,'porsi',0,'tidak pedas'],['Cream of Mushroom Soup',28000,'porsi',0,'tidak pedas'],
                ['Salmon Grilled',85000,'porsi',6,'tidak pedas'],['Pasta Carbonara',45000,'porsi',0,'tidak pedas'],
                ['Lamb Rack Rosemary',95000,'porsi',0,'tidak pedas'],['Risotto Truffle',65000,'porsi',0,'tidak pedas'],
                ['Mocktail Sunrise',20000,'gelas',4,'tidak pedas'],['Tiramisu Cake',30000,'slice',3,'tidak pedas'],
                ['Canape Box Premium',40000,'box',2,'tidak pedas'],['Fish & Chips',42000,'porsi',0,'tidak pedas'],
                ['Bruschetta Trio',35000,'porsi',0,'tidak pedas'],['Chocolate Lava',28000,'porsi',3,'tidak pedas'],
                ['Fruit Infused Water',15000,'liter',4,'tidak pedas'],
            ],
            // vendor 4: Padang Bundo
            [
                ['Rendang Daging',42000,'porsi',0,'pedas'],['Ayam Pop',35000,'porsi',0,'tidak pedas'],
                ['Dendeng Balado',38000,'porsi',0,'sangat pedas'],['Gulai Otak',30000,'porsi',0,'sedang'],
                ['Gulai Ikan Kakap',40000,'porsi',6,'sedang'],['Ayam Bakar Padang',35000,'porsi',0,'pedas'],
                ['Sambal Goreng Kentang',20000,'porsi',0,'pedas'],['Nasi Padang Komplit',35000,'porsi',0,'pedas'],
                ['Teh Talua',15000,'gelas',4,'tidak pedas'],['Lamang Tapai',12000,'potong',3,'tidak pedas'],
                ['Snack Padang Box',20000,'box',2,'tidak pedas'],['Gulai Tunjang',35000,'porsi',0,'sedang'],
                ['Ikan Bilih Goreng',25000,'porsi',6,'tidak pedas'],['Kalio Ayam',32000,'porsi',0,'sedang'],
                ['Kolak Durian',18000,'porsi',3,'tidak pedas'],
            ],
        ];

        // Category mapping by index: 0=NasiKotak,1=Prasmanan,2=SnackBox,3=Dessert,4=Minuman,5=Tumpeng,6=Seafood,7=Tradisional
        $catMap = [
            [1,7,7,7,0,7,7,5,4,3,2,7,6,7,3],
            [0,7,7,7,6,1,1,0,4,3,2,7,7,7,4],
            [7,7,7,7,0,7,7,7,4,3,2,0,7,7,3],
            [1,1,1,1,6,1,1,1,4,3,2,1,1,3,4],
            [7,7,7,7,6,7,7,0,4,3,2,7,6,7,3],
        ];

        $vendors = [];
        $allMenuItems = [];

        foreach ($vendorProfiles as $vi => $vp) {
            $user = User::create([
                'username' => $vp['user'], 'name' => $vp['name'],
                'email' => $vp['email'], 'password' => bcrypt('vendor123'),
                'phone' => $vp['phone'], 'role' => 'vendor', 'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $vendor = Vendor::create([
                'user_id' => $user->id, 'vendor_name' => $vp['name'],
                'description' => $vp['desc'], 'address' => 'Jl. Katering No. '.($vi+1).', '.$vp['city'],
                'city' => $vp['city'], 'province' => 'Indonesia',
                'phone' => $vp['phone'], 'email' => $vp['email'],
                'status' => 'active', 'rating' => 0, 'total_reviews' => 0,
            ]);
            $vendors[] = $vendor;

            foreach ($menuTemplates[$vi] as $mi => $m) {
                $item = MenuItem::create([
                    'vendor_id' => $vendor->vendor_id,
                    'category_id' => $categories[$catMap[$vi][$mi]]->category_id,
                    'item_name' => $m[0], 'price' => $m[1], 'unit' => $m[2],
                    'description' => 'Menu '.$m[0].' dari '.$vp['name'].'. Disiapkan fresh setiap hari.',
                    'min_order' => 1, 'is_available' => true,
                    'spicy_level' => $m[4],
                ]);
                $allMenuItems[$vendor->vendor_id][] = $item;
            }
        }
        $this->command->info('🏪 5 vendors × 15 menus = 75 menu items created.');

        // ══════════════════════════════════════════════
        // 4. CUSTOMERS (15)
        // ══════════════════════════════════════════════
        $customerNames = [
            ['Ahmad Fauzi','ahmad_fauzi'],['Siti Nurhaliza','siti_nur'],['Budi Santoso','budi_s'],
            ['Dewi Kartika','dewi_k'],['Eko Prasetyo','eko_pras'],['Fitri Handayani','fitri_h'],
            ['Gunawan Wibowo','gunawan_w'],['Hana Pertiwi','hana_p'],['Irfan Hakim','irfan_h'],
            ['Joko Widodo','joko_w'],['Kartini Putri','kartini_p'],['Lukman Hakim','lukman_h'],
            ['Maya Sari','maya_sari'],['Nanda Pratama','nanda_p'],['Olivia Wijaya','olivia_w'],
        ];
        $cities = ['Jakarta','Bandung','Surabaya','Medan','Yogyakarta','Semarang','Malang'];
        $customers = [];

        foreach ($customerNames as $i => $cn) {
            $customers[] = User::create([
                'username' => $cn[1], 'name' => $cn[0],
                'email' => $cn[1].'@gmail.com', 'password' => bcrypt('user123'),
                'phone' => '0812'.str_pad($i+10, 8, '0', STR_PAD_LEFT),
                'address' => 'Jl. Pelanggan No. '.($i+1).', '.$cities[$i % count($cities)],
                'role' => 'customer', 'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
        $this->command->info('🙋 15 customers created.');

        // ══════════════════════════════════════════════
        // 5. ORDERS + ORDER_ITEMS + PAYMENTS + REVIEWS
        // ══════════════════════════════════════════════
        $eventTypes = ['Pernikahan','Ulang Tahun','Rapat Kantor','Seminar','Arisan','Pesta Keluarga'];
        $statuses = ['completed','completed','completed','completed','confirmed','preparing','delivering','pending','cancelled'];
        $reviewTexts = [
            'Makanan enak banget! Pasti pesan lagi.',
            'Pelayanan ramah, pengiriman tepat waktu. Recommended!',
            'Porsi mantap, rasa top. Cocok untuk acara kantor.',
            'Enak sih tapi pengemasannya bisa lebih rapih.',
            'Menu variatif, bumbu meresap. Worth the price!',
            'Pengiriman agak telat tapi makanan tetap hangat.',
            'Harga terjangkau untuk kualitas segini. Mantap!',
            'Sudah 3x pesan di sini, selalu konsisten.',
            'Menu dessertnya enak banget, pasti repeat order!',
            null, // sometimes no text
        ];
        $vendorResponses = [
            'Terima kasih atas ulasannya! Kami senang Anda puas 🙏',
            'Terima kasih sudah mempercayakan katering kepada kami!',
            'Noted untuk perbaikan kemasan. Terima kasih masukannya!',
            null, null, // sometimes no response
        ];

        $orderCount = 0;
        $reviewCount = 0;

        foreach ($customers as $ci => $customer) {
            // Each customer orders from 2-4 random vendors
            $vendorPicks = array_rand($vendors, rand(2, min(4, count($vendors))));
            if (!is_array($vendorPicks)) $vendorPicks = [$vendorPicks];

            foreach ($vendorPicks as $vIdx) {
                $vendor = $vendors[$vIdx];
                $menus = $allMenuItems[$vendor->vendor_id];
                $status = $statuses[array_rand($statuses)];
                $eventDate = Carbon::now()->addDays(rand(-60, 30));
                $numPeople = rand(10, 200);

                // Pick 2-5 random menu items
                $pickedMenus = array_values(array_intersect_key($menus, array_flip(array_rand($menus, min(rand(2,5), count($menus))))));

                $subtotal = 0;
                $orderItems = [];
                foreach ($pickedMenus as $menu) {
                    $qty = rand(1, 5) * $numPeople > 50 ? rand(5, 20) : rand(1, 5);
                    $sub = $menu->price * $qty;
                    $subtotal += $sub;
                    $orderItems[] = [
                        'item_id' => $menu->item_id,
                        'item_name' => $menu->item_name,
                        'quantity' => $qty,
                        'unit_price' => $menu->price,
                        'subtotal' => $sub,
                    ];
                }

                $tax = round($subtotal * 0.11);
                $deliveryFee = 15000;
                $discount = rand(0, 3) === 0 ? round($subtotal * 0.05) : 0;
                $total = $subtotal + $tax + $deliveryFee - $discount;

                $orderNumber = 'CK-'.strtoupper(Str::random(3)).'-'.str_pad(++$orderCount, 5, '0', STR_PAD_LEFT);

                $order = Order::create([
                    'user_id' => $customer->id,
                    'vendor_id' => $vendor->vendor_id,
                    'order_number' => $orderNumber,
                    'order_type' => 'custom',
                    'event_type' => $eventTypes[array_rand($eventTypes)],
                    'event_date' => $eventDate->format('Y-m-d'),
                    'event_time' => sprintf('%02d:00', rand(8, 18)),
                    'delivery_address' => $customer->address ?? 'Jl. Test No. 1',
                    'delivery_city' => $cities[$ci % count($cities)],
                    'num_people' => $numPeople,
                    'subtotal' => $subtotal, 'tax' => $tax,
                    'delivery_fee' => $deliveryFee, 'discount' => $discount,
                    'total_amount' => $total,
                    'status' => $status,
                    'payment_status' => in_array($status, ['completed','confirmed','preparing','delivering']) ? 'paid' : ($status === 'pending' ? 'unpaid' : 'refunded'),
                    'special_request' => rand(0, 2) === 0 ? 'Mohon tidak terlalu pedas. Terima kasih.' : null,
                    'created_at' => $eventDate->copy()->subDays(rand(3, 14)),
                ]);

                foreach ($orderItems as $oi) {
                    OrderItem::create(array_merge($oi, ['order_id' => $order->order_id]));
                }

                // Payment for non-cancelled
                if ($status !== 'cancelled') {
                    Payment::create([
                        'order_id' => $order->order_id,
                        'payment_method' => ['transfer','e-wallet','credit_card','cod'][array_rand(['transfer','e-wallet','credit_card','cod'])],
                        'amount' => $total,
                        'payment_status' => $order->payment_status === 'paid' ? 'verified' : 'pending',
                        'verified_by' => $order->payment_status === 'paid' ? $admin->id : null,
                        'verified_at' => $order->payment_status === 'paid' ? now() : null,
                    ]);
                }

                // Review for completed orders (random 1-4 rating)
                if ($status === 'completed') {
                    $rating = rand(1, 4);
                    Review::create([
                        'order_id' => $order->order_id,
                        'user_id' => $customer->id,
                        'vendor_id' => $vendor->vendor_id,
                        'rating' => $rating,
                        'food_rating' => max(1, $rating + rand(-1, 1)),
                        'service_rating' => max(1, $rating + rand(-1, 1)),
                        'delivery_rating' => max(1, $rating + rand(-1, 1)),
                        'review_text' => $reviewTexts[array_rand($reviewTexts)],
                        'vendor_response' => rand(0, 2) > 0 ? $vendorResponses[array_rand($vendorResponses)] : null,
                        'response_date' => rand(0, 1) ? now() : null,
                    ]);
                    $reviewCount++;
                }
            }
        }
        $this->command->info("📦 {$orderCount} orders created.");
        $this->command->info("⭐ {$reviewCount} reviews created.");

        // Update vendor ratings
        foreach ($vendors as $vendor) {
            $reviews = Review::where('vendor_id', $vendor->vendor_id)->get();
            if ($reviews->count() > 0) {
                $vendor->update([
                    'rating' => round($reviews->avg('rating'), 2),
                    'total_reviews' => $reviews->count(),
                ]);
            }
        }
        $this->command->info('📊 Vendor ratings updated.');

        // ══════════════════════════════════════════════
        // 6. PROMOS (6)
        // ══════════════════════════════════════════════
        $promos = [
            ['WELCOME10','Diskon Member Baru','Diskon 10% untuk pesanan pertama','percentage',10,100000,50000,null],
            ['HEMAT20K','Potongan 20K','Potongan Rp20.000 untuk min order 200K','fixed',20000,200000,20000,null],
            ['NUSANTARA15','Promo Nusantara','Diskon 15% khusus vendor Dapur Nusantara','percentage',15,150000,75000,0],
            ['ROYAL25','Royal Discount','Diskon 25% untuk Royal Feast Catering','percentage',25,300000,150000,3],
            ['PADANG10','Promo Padang','Diskon 10% Padang Bundo Catering','percentage',10,100000,50000,4],
            ['WEEKEND30','Weekend Special','Potongan 30K untuk pesanan weekend','fixed',30000,250000,30000,null],
        ];
        foreach ($promos as $p) {
            Promo::create([
                'promo_code' => $p[0], 'promo_name' => $p[1], 'description' => $p[2],
                'discount_type' => $p[3], 'discount_value' => $p[4],
                'min_order' => $p[5], 'max_discount' => $p[6],
                'vendor_id' => $p[7] !== null ? $vendors[$p[7]]->vendor_id : null,
                'valid_from' => now()->subDays(7), 'valid_until' => now()->addMonths(3),
                'usage_limit' => rand(50, 200), 'used_count' => rand(0, 15), 'is_active' => true,
            ]);
        }
        $this->command->info('🎫 6 promos created.');

        // ══════════════════════════════════════════════
        // 7. CARTS (3 active carts)
        // ══════════════════════════════════════════════
        for ($i = 0; $i < 3; $i++) {
            $cust = $customers[$i];
            $vendor = $vendors[rand(0, 4)];
            $menus = $allMenuItems[$vendor->vendor_id];
            $cart = Cart::create(['user_id' => $cust->id, 'vendor_id' => $vendor->vendor_id]);
            $picks = array_rand($menus, min(rand(1, 3), count($menus)));
            if (!is_array($picks)) $picks = [$picks];
            foreach ($picks as $pk) {
                CartItem::create([
                    'cart_id' => $cart->cart_id, 'item_id' => $menus[$pk]->item_id,
                    'quantity' => rand(1, 5),
                ]);
            }
        }
        $this->command->info('🛒 3 active carts created.');

        // ══════════════════════════════════════════════
        // 8. CONTACT MESSAGES (8)
        // ══════════════════════════════════════════════
        $messages = [
            ['Rina Marlina','rina@gmail.com','general','Halo, apakah CateringKu melayani area Tangerang Selatan? Terima kasih.',true],
            ['Doni Firmansyah','doni@yahoo.com','order','Pesanan saya CK-ABC-00012 belum dikirim padahal sudah lewat jadwal. Mohon follow up.',true],
            ['Lisa Permata','lisa@gmail.com','partnership','Saya ingin mendaftarkan usaha katering saya di CateringKu. Bagaimana caranya?',false],
            ['Wahyu Hidayat','wahyu@gmail.com','vendor','Saya chef berpengalaman 10 tahun, ingin bergabung sebagai vendor. Mohon info.',false],
            ['Anita Susanti','anita@gmail.com','feedback','Website-nya bagus dan mudah digunakan. Semoga ada fitur tracking pesanan realtime!',true],
            ['Rudi Hartono','rudi@outlook.com','complaint','Menu yang dikirim tidak sesuai pesanan. Mohon tanggung jawab vendor.',false],
            ['Putri Ayu','putri@gmail.com','general','Apakah bisa pesan untuk acara 500 orang? Vendor mana yang recommended?',false],
            ['Bayu Pratama','bayu@gmail.com','feedback','Fitur pembayarannya lengkap, saran tambah QRIS juga dong!',true],
        ];
        foreach ($messages as $m) {
            ContactMessage::create([
                'name' => $m[0], 'email' => $m[1], 'subject' => $m[2],
                'message' => $m[3], 'is_read' => $m[4],
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }
        $this->command->info('📬 8 contact messages created.');

        // ══════════════════════════════════════════════
        // SUMMARY
        // ══════════════════════════════════════════════
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ FULL TEST SEEDER SELESAI!');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('👤 1 Admin:    admin@cateringku.com / admin123');
        $this->command->info('🏪 5 Vendors:  vendor_*@cateringku.com / vendor123');
        $this->command->info('🙋 15 Users:   *@gmail.com / user123');
        $this->command->info("🍽️  75 Menu items (15 per vendor)");
        $this->command->info("📦 {$orderCount} Orders + payments");
        $this->command->info("⭐ {$reviewCount} Reviews (rating 1-4)");
        $this->command->info('🎫 6 Promos');
        $this->command->info('🛒 3 Active carts');
        $this->command->info('📬 8 Contact messages');
        $this->command->info('═══════════════════════════════════════');
    }
}
