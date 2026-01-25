<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$error = Session::flash('error');
$errors = Session::flash('errors') ?? [];
$old = Session::flash('old') ?? [];
?>

<div class="min-h-[80vh] flex items-center justify-center py-12">
    <div class="w-full max-w-lg">
        <!-- Card -->
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-xl dark:shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 px-8 py-6 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                <h1 class="text-2xl font-bold text-white relative z-10">Buat Akun Baru</h1>
                <p class="text-orange-100 mt-1 relative z-10">Bergabung dengan kami hari ini</p>
            </div>

            <!-- Form -->
            <div class="px-8 py-8">
                <?php if ($error): ?>
                    <div
                        class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400 text-sm">
                        <?php echo Security::e($error); ?>
                    </div>
                <?php endif; ?>

                <form action="/register" method="POST" class="space-y-5">
                    <?php echo Security::csrfField(); ?>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" required
                            value="<?php echo Security::e($old['full_name'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500 <?php echo isset($errors['full_name']) ? 'border-red-500' : ''; ?>"
                            placeholder="Nama lengkap Anda">
                        <?php if (isset($errors['full_name'])): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                <?php echo Security::e($errors['full_name']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                        <input type="text" id="username" name="username" required
                            value="<?php echo Security::e($old['username'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500 <?php echo isset($errors['username']) ? 'border-red-500' : ''; ?>"
                            placeholder="username">
                        <?php if (isset($errors['username'])): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                <?php echo Security::e($errors['username']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                            value="<?php echo Security::e($old['email'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500 <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>"
                            placeholder="nama@email.com">
                        <?php if (isset($errors['email'])): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                <?php echo Security::e($errors['email']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">No.
                            Telepon</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo Security::e($old['phone'] ?? ''); ?>"
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500 <?php echo isset($errors['password']) ? 'border-red-500' : ''; ?>"
                            placeholder="Minimal 8 karakter">
                        <?php if (isset($errors['password'])): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                <?php echo Security::e($errors['password']); ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal 8 karakter dengan huruf besar,
                            huruf kecil, dan angka</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi
                            Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="Ulangi password">
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required
                            class="w-4 h-4 mt-1 text-orange-600 border-gray-300 dark:border-gray-600 rounded focus:ring-orange-500 bg-white dark:bg-gray-700">
                        <label for="terms" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            Saya setuju dengan <a href="/terms"
                                class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium">Syarat
                                & Ketentuan</a>
                            dan <a href="/privacy"
                                class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium">Kebijakan
                                Privasi</a>
                        </label>
                    </div>

                    <button type="submit"
                        class="btn-glow w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-4 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all transform hover:scale-[1.02] shadow-lg shadow-orange-500/25">
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">atau</span>
                    </div>
                </div>

                <!-- Login Link -->
                <p class="text-center text-gray-600 dark:text-gray-400">
                    Sudah punya akun?
                    <a href="/login"
                        class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-semibold transition-colors">Masuk
                        di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>