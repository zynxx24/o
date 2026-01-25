<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
$error = Session::flash('error');
$isLoggedIn = Session::isLoggedIn();
?>

<!-- Vendor Header -->
<div
    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-8 mb-8 relative overflow-hidden backdrop-blur-sm">
    <div
        class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-orange-200 to-red-200 dark:from-orange-900/30 dark:to-red-900/30 rounded-full mix-blend-multiply dark:mix-blend-normal filter blur-3xl opacity-50 -mr-16 -mt-16">
    </div>
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
        <div
            class="w-24 h-24 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30 flex-shrink-0">
            <?php if (!empty($vendor['logo_url'])): ?>
                <img src="<?php echo Security::e($vendor['logo_url']); ?>"
                    alt="<?php echo Security::e($vendor['vendor_name']); ?>" class="w-full h-full object-cover rounded-2xl">
            <?php else: ?>
                <span class="text-4xl font-bold"><?php echo strtoupper(substr($vendor['vendor_name'], 0, 1)); ?></span>
            <?php endif; ?>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <?php echo Security::e($vendor['vendor_name']); ?></h1>
            <p class="text-gray-600 dark:text-gray-400 mb-3 max-w-2xl">
                <?php echo Security::e($vendor['description']); ?></p>
            <div class="flex items-center gap-4 text-sm">
                <span
                    class="flex items-center text-orange-600 dark:text-orange-400 font-bold bg-orange-50 dark:bg-orange-900/30 px-3 py-1.5 rounded-lg">
                    <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <?php echo number_format($vendor['rating'], 1); ?> (<?php echo $vendor['total_reviews']; ?> ulasan)
                </span>
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
            </div>
        </div>
    </div>
</div>

<?php if ($success): ?>
    <div
        class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-green-600 dark:text-green-400 flex items-center backdrop-blur-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <?php echo Security::e($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div
        class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400 backdrop-blur-sm">
        <?php echo Security::e($error); ?>
    </div>
<?php endif; ?>

<!-- Menu List -->
<div class="mb-8">
    <h2
        class="text-2xl font-bold text-gray-800 dark:text-white mb-6 pb-2 border-b border-gray-100 dark:border-gray-700 flex items-center">
        <span class="w-1 h-6 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
        Daftar Menu
    </h2>

    <?php if (empty($menuItems)): ?>
        <div
            class="text-center py-12 bg-white dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400">Belum ada menu yang tersedia.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($menuItems as $item): ?>
                <div
                    class="bg-white dark:bg-gray-800/80 p-5 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 flex gap-4 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-lg dark:hover:shadow-orange-900/10 transition-all duration-300 backdrop-blur-sm card-hover">
                    <!-- Item Image -->
                    <div class="w-28 h-28 bg-gray-100 dark:bg-gray-700 rounded-xl flex-shrink-0 overflow-hidden">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo Security::e($item['image_url']); ?>"
                                alt="<?php echo Security::e($item['item_name']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div
                                class="w-full h-full flex items-center justify-center text-gray-300 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/30 dark:to-red-900/30">
                                <svg class="w-10 h-10 text-orange-200 dark:text-orange-800" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-grow flex flex-col justify-between min-w-0">
                        <div>
                            <?php if (!empty($item['category_name'])): ?>
                                <div
                                    class="text-xs text-orange-500 dark:text-orange-400 font-semibold mb-1 uppercase tracking-wider">
                                    <?php echo Security::e($item['category_name']); ?>
                                </div>
                            <?php endif; ?>
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-1">
                                <?php echo Security::e($item['item_name']); ?></h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2">
                                <?php echo Security::e($item['description']); ?></p>
                        </div>

                        <div class="flex items-end justify-between mt-3">
                            <div>
                                <div class="text-orange-600 dark:text-orange-400 font-bold text-lg">Rp
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">Min. <?php echo $item['min_order']; ?>
                                    <?php echo $item['unit']; ?>
                                </div>
                            </div>

                            <?php if ($isLoggedIn): ?>
                                <form action="/cart/add" method="POST" class="m-0">
                                    <?php echo Security::csrfField(); ?>
                                    <input type="hidden" name="vendor_id" value="<?php echo $vendor['vendor_id']; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                        class="btn-glow flex items-center text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 px-4 py-2 rounded-xl transition-all shadow-lg shadow-orange-500/25">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Keranjang
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="/login"
                                    class="flex items-center text-sm font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 px-4 py-2 rounded-xl transition-all">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Keranjang
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="mt-8">
    <a href="/"
        class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 font-medium transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
            </path>
        </svg>
        Kembali ke Beranda
    </a>
</div>

<?php include 'layout/footer.php'; ?>