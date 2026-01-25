<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
?>

<div class="max-w-6xl mx-auto py-8">
    <!-- Search Header -->
    <div
        class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 rounded-2xl p-8 mb-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-12 -mb-12"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-bold mb-4">Cari Katering</h1>
            <form action="/search" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input type="text" name="q" value="<?php echo Security::e($query ?? ''); ?>"
                        class="w-full px-5 py-4 rounded-xl text-gray-900 dark:text-white dark:bg-gray-800/90 focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500 focus:outline-none shadow-lg dark:placeholder-gray-400 transition-all"
                        placeholder="Cari vendor atau menu...">
                </div>
                <div class="md:w-48">
                    <select name="category"
                        class="w-full px-4 py-4 rounded-xl text-gray-900 dark:text-white dark:bg-gray-800/90 focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500 focus:outline-none shadow-lg transition-all">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category ?? '') == $cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo Security::e($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:w-48">
                    <select name="city"
                        class="w-full px-4 py-4 rounded-xl text-gray-900 dark:text-white dark:bg-gray-800/90 focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500 focus:outline-none shadow-lg transition-all">
                        <option value="">Semua Kota</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo Security::e($c); ?>" <?php echo ($city ?? '') == $c ? 'selected' : ''; ?>>
                                <?php echo Security::e($c); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                    class="btn-glow px-8 py-4 bg-white dark:bg-gray-800 text-orange-600 dark:text-orange-400 rounded-xl font-semibold hover:bg-orange-50 dark:hover:bg-gray-700 transition-all flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Results Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <?php if (!empty($query) || !empty($category) || !empty($city)): ?>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Hasil Pencarian
                    <?php if (!empty($query)): ?>
                        untuk "<?php echo Security::e($query); ?>"
                    <?php endif; ?>
                </h2>
                <p class="text-gray-500 dark:text-gray-400">
                    <?php echo $totalResults ?? 0; ?> vendor ditemukan
                </p>
            <?php else: ?>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Semua Vendor</h2>
            <?php endif; ?>
        </div>

        <!-- Sort -->
        <div class="mt-4 md:mt-0">
            <select name="sort" onchange="window.location.href=this.value"
                class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800/80 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'rating'])); ?>" <?php echo ($sort ?? '') == 'rating' ? 'selected' : ''; ?>>Rating Tertinggi</option>
                <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'reviews'])); ?>" <?php echo ($sort ?? '') == 'reviews' ? 'selected' : ''; ?>>Ulasan Terbanyak</option>
                <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest'])); ?>" <?php echo ($sort ?? '') == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
            </select>
        </div>
    </div>

    <!-- Active Filters -->
    <?php if (!empty($query) || !empty($category) || !empty($city)): ?>
        <div class="flex flex-wrap gap-2 mb-6">
            <?php if (!empty($query)): ?>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-sm font-medium">
                    "<?php echo Security::e($query); ?>"
                    <a href="?<?php echo http_build_query(array_diff_key($_GET, ['q' => ''])); ?>"
                        class="ml-2 hover:text-orange-900 dark:hover:text-orange-300">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($categoryName)): ?>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-sm font-medium">
                    <?php echo Security::e($categoryName); ?>
                    <a href="?<?php echo http_build_query(array_diff_key($_GET, ['category' => ''])); ?>"
                        class="ml-2 hover:text-orange-900 dark:hover:text-orange-300">×</a>
                </span>
            <?php endif; ?>
            <?php if (!empty($city)): ?>
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-sm font-medium">
                    <?php echo Security::e($city); ?>
                    <a href="?<?php echo http_build_query(array_diff_key($_GET, ['city' => ''])); ?>"
                        class="ml-2 hover:text-orange-900 dark:hover:text-orange-300">×</a>
                </span>
            <?php endif; ?>
            <a href="/search"
                class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium transition-colors">Hapus
                semua filter</a>
        </div>
    <?php endif; ?>

    <!-- Results Grid -->
    <?php if (empty($vendors)): ?>
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center backdrop-blur-sm">
            <div class="w-24 h-24 mx-auto bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ada Hasil</h3>
            <p class="text-gray-500 dark:text-gray-400">Coba ubah kata kunci atau filter pencarian Anda</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($vendors as $vendor): ?>
                <a href="/vendor/<?php echo $vendor['vendor_id']; ?>"
                    class="group block bg-white dark:bg-gray-800/80 rounded-2xl overflow-hidden shadow-sm dark:shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 card-hover backdrop-blur-sm">
                    <!-- Image -->
                    <div
                        class="h-40 bg-gradient-to-r from-orange-400 via-red-500 to-pink-500 flex items-center justify-center text-white relative overflow-hidden">
                        <?php if (!empty($vendor['logo_url'])): ?>
                            <img src="<?php echo Security::e($vendor['logo_url']); ?>"
                                alt="<?php echo Security::e($vendor['vendor_name']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-4xl font-bold opacity-30 group-hover:scale-110 transition-transform duration-500">
                                <?php echo strtoupper(substr($vendor['vendor_name'], 0, 1)); ?>
                            </span>
                        <?php endif; ?>

                        <div
                            class="absolute top-3 right-3 glass bg-white/90 dark:bg-gray-900/90 px-2.5 py-1 rounded-full text-sm font-semibold text-orange-600 dark:text-orange-400 flex items-center backdrop-blur-sm">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <?php echo number_format($vendor['rating'], 1); ?>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3
                            class="font-bold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-1">
                            <?php echo Security::e($vendor['vendor_name']); ?>
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 mb-3">
                            <?php echo Security::e($vendor['description']); ?>
                        </p>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400 dark:text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <?php echo Security::e($vendor['city']); ?>
                            </span>
                            <span class="text-gray-400 dark:text-gray-500">
                                <?php echo $vendor['total_reviews']; ?> ulasan
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (($totalResults ?? 0) > count($vendors)): ?>
            <div class="mt-8 flex justify-center">
                <nav class="inline-flex rounded-xl shadow-sm overflow-hidden">
                    <?php if (($page ?? 1) > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => ($page ?? 1) - 1])); ?>"
                            class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if (count($vendors) >= ($perPage ?? 12)): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => ($page ?? 1) + 1])); ?>"
                            class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Selanjutnya
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>