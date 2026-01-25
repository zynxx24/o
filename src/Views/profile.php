<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
$error = Session::flash('error');

// Safely get user data with defaults
$userName = $user['full_name'] ?? 'User';
$userUsername = $user['username'] ?? '';
$userEmail = $user['email'] ?? '';
$userPhone = $user['phone'] ?? '';
$userAddress = $user['address'] ?? '';
$userRole = $user['role'] ?? 'customer';
$userCreatedAt = $user['created_at'] ?? null;
$isAdmin = ($userRole === 'admin');
$isVendor = ($userRole === 'vendor');
?>

<div class="max-w-4xl mx-auto py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
            <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
            Profil Saya
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Kelola informasi profil Anda</p>
    </div>

    <?php if ($success): ?>
        <div
            class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-green-600 dark:text-green-400 backdrop-blur-sm">
            <?php echo Security::e($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div
            class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400 backdrop-blur-sm">
            <?php echo Security::e($error); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 text-center backdrop-blur-sm">
                <!-- Avatar -->
                <div
                    class="w-24 h-24 mx-auto bg-gradient-to-r from-orange-400 to-red-500 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-lg shadow-orange-500/30">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <?php echo Security::e($userName); ?>
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">@<?php echo Security::e($userUsername); ?></p>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $isAdmin ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : ($isVendor ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400'); ?>">
                        <?php echo ucfirst($userRole); ?>
                    </span>
                </div>

                <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                    <p>Bergabung sejak</p>
                    <p class="font-medium text-gray-700 dark:text-gray-300">
                        <?php echo $userCreatedAt ? date('d M Y', strtotime($userCreatedAt)) : '-'; ?>
                    </p>
                </div>
            </div>

            <!-- Quick Links -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 mt-6 backdrop-blur-sm">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Menu Cepat</h3>
                <nav class="space-y-2">
                    <?php if ($isAdmin): ?>
                        <a href="/admin"
                            class="flex items-center px-4 py-3 rounded-xl text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 transition font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Admin Dashboard
                        </a>
                    <?php endif; ?>
                    <?php if ($isVendor): ?>
                        <a href="/vendor/dashboard"
                            class="flex items-center px-4 py-3 rounded-xl text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Vendor Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="/orders"
                        class="flex items-center px-4 py-3 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Riwayat Pesanan
                    </a>
                    <a href="/cart"
                        class="flex items-center px-4 py-3 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Keranjang
                    </a>
                    <form action="/logout" method="POST" class="m-0">
                        <?php echo Security::csrfField(); ?>
                        <button type="submit"
                            class="w-full flex items-center px-4 py-3 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </nav>
            </div>
        </div>


        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Form -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Informasi Pribadi</h3>

                <form action="/profile" method="POST" class="space-y-5">
                    <?php echo Security::csrfField(); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama
                                Lengkap</label>
                            <input type="text" name="full_name" value="<?php echo Security::e($userName); ?>" required
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" value="<?php echo Security::e($userUsername); ?>" disabled
                                class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/30 text-gray-500 dark:text-gray-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" value="<?php echo Security::e($userEmail); ?>" disabled
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/30 text-gray-500 dark:text-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">No.
                            Telepon</label>
                        <input type="tel" name="phone" value="<?php echo Security::e($userPhone); ?>"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="Alamat lengkap Anda"><?php echo Security::e($userAddress); ?></textarea>
                    </div>


                    <div class="pt-4">
                        <button type="submit"
                            class="btn-glow bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-500/25">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Ubah Password</h3>

                <form action="/profile/password" method="POST" class="space-y-5">
                    <?php echo Security::csrfField(); ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Saat
                            Ini</label>
                        <input type="password" name="current_password" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password
                            Baru</label>
                        <input type="password" name="new_password" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal 8 karakter dengan huruf besar,
                            huruf kecil, dan angka</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi
                            Password Baru</label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="bg-gray-800 dark:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-900 dark:hover:bg-gray-500 transition-all">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>