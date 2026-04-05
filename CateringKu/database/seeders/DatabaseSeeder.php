<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Promo;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@cateringku.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
        ]);

        // Vendor users & vendors
        $vendorUser1 = User::create([
            'username' => 'dapur_nusantara',
            'name' => 'Pemilik Dapur Nusantara',
            'email' => 'dapur@cateringku.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'status' => 'active',
            'phone' => '081234567891',
        ]);

        $vendorUser2 = User::create([
            'username' => 'sari_rasa',
            'name' => 'Pemilik Sari Rasa',
            'email' => 'sarirasa@cateringku.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'status' => 'active',
            'phone' => '081234567892',
        ]);

        $vendorUser3 = User::create([
            'username' => 'berkah_catering',
            'name' => 'Pemilik Berkah Catering',
            'email' => 'berkah@cateringku.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'status' => 'active',
            'phone' => '081234567893',
        ]);

        // Customer user
        User::create([
            'username' => 'pelanggan',
            'name' => 'Ahmad Pelanggan',
            'email' => 'pelanggan@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
            'phone' => '081234567899',
            'address' => 'Jl. Merdeka No. 123, Jakarta',
        ]);

        // Vendors
        $vendor1 = Vendor::create([
            'user_id' => $vendorUser1->id,
            'vendor_name' => 'Dapur Nusantara Catering',
            'description' => 'Menyediakan berbagai menu masakan khas Nusantara untuk segala acara. Berpengalaman lebih dari 10 tahun melayani ribuan pelanggan.',
            'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'phone' => '021-5551234',
            'email' => 'info@dapurnusantara.com',
            'rating' => 4.8,
            'total_reviews' => 156,
            'status' => 'active',
        ]);

        $vendor2 = Vendor::create([
            'user_id' => $vendorUser2->id,
            'vendor_name' => 'Sari Rasa Katering',
            'description' => 'Katering premium dengan cita rasa otentik Indonesia. Spesialis prasmanan pernikahan dan acara korporat.',
            'address' => 'Jl. Gatot Subroto No. 78, Bandung',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'phone' => '022-7771234',
            'email' => 'order@sarirasa.com',
            'rating' => 4.6,
            'total_reviews' => 98,
            'status' => 'active',
        ]);

        $vendor3 = Vendor::create([
            'user_id' => $vendorUser3->id,
            'vendor_name' => 'Berkah Catering',
            'description' => 'Katering halal dan higienis untuk segala kebutuhan. Mulai dari nasi kotak, snack box, hingga prasmanan lengkap.',
            'address' => 'Jl. Ahmad Yani No. 12, Surabaya',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'phone' => '031-8881234',
            'email' => 'berkahcatering@gmail.com',
            'rating' => 4.5,
            'total_reviews' => 72,
            'status' => 'active',
        ]);

        // Categories
        $categories = [
            ['category_name' => 'Nasi & Lauk', 'description' => 'Menu nasi dengan berbagai lauk pauk'],
            ['category_name' => 'Prasmanan', 'description' => 'Paket prasmanan lengkap'],
            ['category_name' => 'Nasi Kotak', 'description' => 'Nasi kotak siap saji'],
            ['category_name' => 'Snack Box', 'description' => 'Kue dan jajanan dalam kotak'],
            ['category_name' => 'Minuman', 'description' => 'Berbagai pilihan minuman'],
            ['category_name' => 'Dessert', 'description' => 'Hidangan penutup dan kue'],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Menu Items for Vendor 1
        $menus = [
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 1, 'item_name' => 'Nasi Goreng Spesial', 'price' => 25000, 'unit' => 'porsi', 'description' => 'Nasi goreng dengan telur, ayam, dan udang'],
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 1, 'item_name' => 'Ayam Bakar Madu', 'price' => 30000, 'unit' => 'porsi', 'description' => 'Ayam bakar dengan saus madu khas'],
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 1, 'item_name' => 'Rendang Daging Sapi', 'price' => 35000, 'unit' => 'porsi', 'description' => 'Rendang daging sapi asli Padang'],
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 3, 'item_name' => 'Nasi Kotak Ayam Geprek', 'price' => 28000, 'unit' => 'kotak', 'description' => 'Nasi kotak dengan ayam geprek sambal bawang'],
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 4, 'item_name' => 'Snack Box Premium', 'price' => 20000, 'unit' => 'kotak', 'description' => '4 jenis snack + air mineral'],
            ['vendor_id' => $vendor1->vendor_id, 'category_id' => 5, 'item_name' => 'Es Teh Manis', 'price' => 5000, 'unit' => 'gelas', 'description' => 'Es teh manis segar'],
            ['vendor_id' => $vendor2->vendor_id, 'category_id' => 1, 'item_name' => 'Nasi Liwet Komplit', 'price' => 30000, 'unit' => 'porsi', 'description' => 'Nasi liwet dengan lauk lengkap'],
            ['vendor_id' => $vendor2->vendor_id, 'category_id' => 1, 'item_name' => 'Ikan Bakar Bumbu Rujak', 'price' => 32000, 'unit' => 'porsi', 'description' => 'Ikan gurame bakar bumbu rujak'],
            ['vendor_id' => $vendor2->vendor_id, 'category_id' => 6, 'item_name' => 'Kue Lapis Legit', 'price' => 15000, 'unit' => 'potong', 'description' => 'Kue lapis legit premium'],
            ['vendor_id' => $vendor3->vendor_id, 'category_id' => 3, 'item_name' => 'Nasi Kotak Komplit', 'price' => 25000, 'unit' => 'kotak', 'description' => 'Nasi + ayam + sayur + kerupuk'],
            ['vendor_id' => $vendor3->vendor_id, 'category_id' => 4, 'item_name' => 'Snack Box Ekonomis', 'price' => 15000, 'unit' => 'kotak', 'description' => '3 jenis snack + air mineral'],
            ['vendor_id' => $vendor3->vendor_id, 'category_id' => 1, 'item_name' => 'Soto Ayam', 'price' => 20000, 'unit' => 'porsi', 'description' => 'Soto ayam khas Surabaya'],
        ];
        foreach ($menus as $menu) {
            MenuItem::create(array_merge($menu, ['is_available' => true]));
        }

        // Promos
        Promo::create([
            'promo_code' => 'WELCOME20',
            'promo_name' => 'Diskon Pelanggan Baru',
            'description' => 'Diskon 20% untuk pelanggan baru',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_order' => 100000,
            'max_discount' => 50000,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Promo::create([
            'promo_code' => 'HEMAT15K',
            'promo_name' => 'Potongan Rp15.000',
            'description' => 'Potongan langsung Rp15.000',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'min_order' => 75000,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(1),
            'is_active' => true,
        ]);
    }
}
