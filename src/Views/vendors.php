<?php
use App\Config\Session;
use App\Middleware\Security;

include __DIR__ . '/layout/header.php';

$successMessage = Session::flash('success');
$errorMessage = Session::flash('error');
?>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
            <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
            Daftar Vendor Katering
        </h1>
        <p class="text-gray-600 dark:text-gray-400">Temukan vendor katering terbaik untuk kebutuhan acara Anda</p>
    </div>

    <?php if ($successMessage): ?>
        <div
            class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl backdrop-blur-sm">
            <?php echo Security::e($successMessage); ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl p-6 mb-8 border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Vendor</label>
                <input type="text" name="q" placeholder="Nama vendor..."
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-500 transition-all">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kota</label>
                <select name="city"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 transition-all">
                    <option value="">Semua Kota</option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?php echo Security::e($c); ?>">
                            <?php echo Security::e($c); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit"
                class="btn-glow px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl hover:from-orange-600 hover:to-red-600 transition-all font-medium shadow-lg shadow-orange-500/25">
                Cari
            </button>
        </form>
    </div>

    <!-- Vendors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($vendors)): ?>
            <div
                class="col-span-full text-center py-12 bg-white dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada vendor ditemukan</p>
            </div>
        <?php else: ?>
            <?php foreach ($vendors as $vendor): ?>
                <a href="/vendor/<?php echo $vendor['vendor_id']; ?>"
                    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl overflow-hidden hover:shadow-xl dark:hover:shadow-2xl transition-all duration-300 group border border-gray-100 dark:border-gray-700 card-hover backdrop-blur-sm">
                    <div
                        class="h-40 bg-gradient-to-r from-orange-400 via-red-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                        <?php if (!empty($vendor['logo_url'])): ?>
                            <img src="<?php echo Security::e($vendor['logo_url']); ?>"
                                alt="<?php echo Security::e($vendor['vendor_name']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-4xl font-bold text-white group-hover:scale-110 transition-transform duration-500">
                                <?php echo strtoupper(substr($vendor['vendor_name'], 0, 2)); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-5">
                        <h3
                            class="font-semibold text-lg text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-2">
                            <?php echo Security::e($vendor['vendor_name']); ?>
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-3">
                            <?php echo Security::e($vendor['description'] ?? 'Vendor katering terpercaya'); ?>
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <?php echo Security::e($vendor['city'] ?? 'Indonesia'); ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <?php echo number_format($vendor['rating'], 1); ?>
                                </span>
                                <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">
                                    (<?php echo $vendor['total_reviews']; ?>)
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>