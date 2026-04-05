<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * Quick seeder for testing — creates 1 admin, 1 vendor (with menus), and 1 customer.
 *
 * Usage:  php artisan db:seed --class=TestAccountsSeeder
 */
class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════
        // 1. ADMIN — full access to admin panel
        // ══════════════════════════════════════════
        User::updateOrCreate(
            ['email' => 'admin@cateringku.com'],
            [
                'username' => 'admin',
                'name' => 'Admin CateringKu',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081200000001',
            ]
        );
        $this->command->info('✅ Admin  → email: admin@cateringku.com / password: admin123');

        // ══════════════════════════════════════════
        // 2. VENDOR — owns "Dapur Nusantara" + menus
        // ══════════════════════════════════════════
        $vendorUser = User::updateOrCreate(
            ['email' => 'vendor@cateringku.com'],
            [
                'username' => 'vendor_dapur',
                'name' => 'Pemilik Dapur Nusantara',
                'password' => bcrypt('vendor123'),
                'role' => 'vendor',
                'status' => 'active',
                'phone' => '081200000002',
            ]
        );

        $vendor = Vendor::updateOrCreate(
            ['user_id' => $vendorUser->id],
            [
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
            ]
        );
        $this->command->info('✅ Vendor → email: vendor@cateringku.com / password: vendor123');

        // Categories (create if missing)
        $categories = [
            ['category_name' => 'Nasi & Lauk', 'description' => 'Menu nasi dengan berbagai lauk pauk'],
            ['category_name' => 'Prasmanan', 'description' => 'Paket prasmanan lengkap'],
            ['category_name' => 'Nasi Kotak', 'description' => 'Nasi kotak siap saji'],
            ['category_name' => 'Snack Box', 'description' => 'Kue dan jajanan dalam kotak'],
            ['category_name' => 'Minuman', 'description' => 'Berbagai pilihan minuman'],
            ['category_name' => 'Dessert', 'description' => 'Hidangan penutup dan kue'],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['category_name' => $cat['category_name']], $cat);
        }

        // Menus for the vendor
        $menus = [
            ['category_id' => 1, 'item_name' => 'Nasi Goreng Spesial', 'price' => 25000, 'unit' => 'porsi', 'description' => 'Nasi goreng dengan telur, ayam, dan udang'],
            ['category_id' => 1, 'item_name' => 'Ayam Bakar Madu', 'price' => 30000, 'unit' => 'porsi', 'description' => 'Ayam bakar dengan saus madu khas'],
            ['category_id' => 1, 'item_name' => 'Rendang Daging Sapi', 'price' => 35000, 'unit' => 'porsi', 'description' => 'Rendang daging sapi asli Padang'],
            ['category_id' => 3, 'item_name' => 'Nasi Kotak Ayam Geprek', 'price' => 28000, 'unit' => 'kotak', 'description' => 'Nasi kotak dengan ayam geprek sambal bawang'],
            ['category_id' => 4, 'item_name' => 'Snack Box Premium', 'price' => 20000, 'unit' => 'kotak', 'description' => '4 jenis snack + air mineral'],
            ['category_id' => 5, 'item_name' => 'Es Teh Manis', 'price' => 5000, 'unit' => 'gelas', 'description' => 'Es teh manis segar'],
        ];
        foreach ($menus as $menu) {
            MenuItem::firstOrCreate(
                ['vendor_id' => $vendor->vendor_id, 'item_name' => $menu['item_name']],
                array_merge($menu, ['vendor_id' => $vendor->vendor_id, 'is_available' => true])
            );
        }

        // ══════════════════════════════════════════
        // 3. CUSTOMER — regular user for ordering
        // ══════════════════════════════════════════
        User::updateOrCreate(
            ['email' => 'user@cateringku.com'],
            [
                'username' => 'pelanggan',
                'name' => 'Ahmad Pelanggan',
                'password' => bcrypt('user123'),
                'role' => 'customer',
                'status' => 'active',
                'phone' => '081200000003',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
            ]
        );
        $this->command->info('✅ User   → email: user@cateringku.com / password: user123');

        $this->command->newLine();
        $this->command->info('🎉 Semua akun test berhasil dibuat!');
    }
}
