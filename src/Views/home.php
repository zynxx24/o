<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
?>

<!-- Hero Section with Search -->
<div
    class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 dark:from-orange-600 dark:via-red-600 dark:to-pink-600 rounded-3xl p-8 md:p-12 mb-12 text-white relative overflow-hidden shadow-2xl shadow-orange-500/20 dark:shadow-orange-900/30">
    <!-- Animated background elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 animate-pulse-slow"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-16 -mb-16 animate-pulse-slow"></div>
    <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full animate-float"></div>

    <div class="relative z-10 text-center max-w-3xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">Temukan Katering Terbaik</h1>
        <p class="text-lg text-orange-100 mb-8">
            Pesan katering untuk berbagai acara dengan mudah dan cepat. Pilihan beragam dari vendor terpercaya.
        </p>

        <!-- Search Form -->
        <form action="/search" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
            <div class="flex-grow relative">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" name="q" placeholder="Cari nama vendor atau menu..."
                    class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 dark:text-white dark:bg-gray-800/90 focus:ring-2 focus:ring-orange-300 dark:focus:ring-orange-500 focus:outline-none shadow-lg dark:placeholder-gray-400 transition-all">
            </div>
            <button type="submit"
                class="btn-glow bg-white dark:bg-gray-800 text-orange-600 dark:text-orange-400 px-8 py-4 rounded-xl font-semibold hover:bg-orange-50 dark:hover:bg-gray-700 transition-all shadow-lg">
                Cari
            </button>
        </form>
    </div>
</div>

<?php if ($success): ?>
    <div
        class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-green-600 dark:text-green-400 flex items-center backdrop-blur-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <?php echo Security::e($success); ?>
    </div>
<?php endif; ?>

<!-- Quick Categories -->
<div class="mb-12">
    <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide">
        <a href="/search?type=prasmanan"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-xl flex items-center justify-center text-xl">🍽️</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Prasmanan</span>
        </a>
        <a href="/search?type=nasi-kotak"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-xl flex items-center justify-center text-xl">🍱</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Nasi Kotak</span>
        </a>
        <a href="/search?type=snack-box"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-xl flex items-center justify-center text-xl">🥪</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Snack Box</span>
        </a>
        <a href="/search?type=tumpeng"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-xl flex items-center justify-center text-xl">🍚</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Tumpeng</span>
        </a>
        <a href="/search?type=dessert"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-pink-100 to-pink-200 dark:from-pink-900/50 dark:to-pink-800/50 rounded-xl flex items-center justify-center text-xl">🍰</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Dessert</span>
        </a>
        <a href="/search?type=wedding"
            class="flex-shrink-0 flex items-center gap-3 bg-white dark:bg-gray-800/80 px-5 py-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-orange-300 dark:hover:border-orange-600 hover:shadow-lg dark:hover:shadow-orange-900/20 transition-all card-hover backdrop-blur-sm">
            <span
                class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/50 dark:to-purple-800/50 rounded-xl flex items-center justify-center text-xl">💒</span>
            <span class="font-medium text-gray-700 dark:text-gray-200">Pernikahan</span>
        </a>
    </div>
</div>

<!-- Vendors Grid -->
<div class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
            <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
            Mitra Katering Kami
        </h2>
        <a href="/search"
            class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium flex items-center transition-colors">
            Lihat Semua
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <?php if (empty($vendors)): ?>
        <div
            class="text-center py-16 bg-white dark:bg-gray-800/50 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div
                class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Vendor</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada vendor katering yang tersedia saat ini.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($vendors as $vendor): ?>
                <a href="/vendor/<?php echo $vendor['vendor_id']; ?>"
                    class="group block bg-white dark:bg-gray-800/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl dark:hover:shadow-orange-900/10 transition-all duration-300 border border-gray-100 dark:border-gray-700 card-hover backdrop-blur-sm">
                    <!-- Image -->
                    <div
                        class="h-48 bg-gradient-to-r from-orange-400 via-red-500 to-pink-500 flex items-center justify-center text-white relative overflow-hidden">
                        <?php if (!empty($vendor['logo_url'])): ?>
                            <img src="<?php echo Security::e($vendor['logo_url']); ?>"
                                alt="<?php echo Security::e($vendor['vendor_name']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <span class="text-6xl font-bold opacity-20 group-hover:scale-110 transition-transform duration-500">
                                <?php echo strtoupper(substr($vendor['vendor_name'], 0, 1)); ?>
                            </span>
                        <?php endif; ?>

                        <div
                            class="absolute top-4 right-4 glass bg-white/90 dark:bg-gray-900/90 px-3 py-1 rounded-full text-sm font-semibold text-orange-600 dark:text-orange-400 shadow-lg flex items-center backdrop-blur-sm">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <?php echo number_format($vendor['rating'], 1); ?>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors mb-2">
                            <?php echo Security::e($vendor['vendor_name']); ?>
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 mb-4">
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
                            <span class="text-gray-400 dark:text-gray-500"><?php echo $vendor['total_reviews']; ?> ulasan</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Features Section -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center card-hover backdrop-blur-sm">
        <div
            class="w-14 h-14 bg-gradient-to-br from-green-100 to-emerald-200 dark:from-green-900/50 dark:to-emerald-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                </path>
            </svg>
        </div>
        <h3 class="font-bold text-gray-900 dark:text-white mb-2">Vendor Terpercaya</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Semua vendor telah melalui verifikasi ketat</p>
    </div>
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center card-hover backdrop-blur-sm">
        <div
            class="w-14 h-14 bg-gradient-to-br from-blue-100 to-cyan-200 dark:from-blue-900/50 dark:to-cyan-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="font-bold text-gray-900 dark:text-white mb-2">Proses Cepat</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Pesan dalam hitungan menit, terima tepat waktu</p>
    </div>
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 text-center card-hover backdrop-blur-sm">
        <div
            class="w-14 h-14 bg-gradient-to-br from-purple-100 to-violet-200 dark:from-purple-900/50 dark:to-violet-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
        </div>
        <h3 class="font-bold text-gray-900 dark:text-white mb-2">Pembayaran Mudah</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Berbagai metode pembayaran tersedia</p>
    </div>
</div>

<?php include 'layout/footer.php'; ?>