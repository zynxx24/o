<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
$error = Session::flash('error');
?>

<div class="max-w-4xl mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
                Keranjang Belanja
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                <?php if (!empty($cartItems)): ?>
                    <?php echo $cartItemCount; ?> item dalam keranjang
                <?php else: ?>
                    Keranjang Anda kosong
                <?php endif; ?>
            </p>
        </div>
        <a href="/"
            class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Lanjut Belanja
        </a>
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

    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center backdrop-blur-sm">
            <div
                class="w-24 h-24 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Keranjang Anda Kosong</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Sepertinya Anda belum menambahkan menu apapun ke keranjang.</p>
            <a href="/"
                class="btn-glow inline-flex items-center bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-500/25">
                Mulai Belanja
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <?php
                $groupedItems = [];
                foreach ($cartItems as $item) {
                    $vendorId = $item['vendor_id'];
                    if (!isset($groupedItems[$vendorId])) {
                        $groupedItems[$vendorId] = [
                            'vendor_name' => $item['vendor_name'],
                            'items' => []
                        ];
                    }
                    if ($item['cart_item_id']) {
                        $groupedItems[$vendorId]['items'][] = $item;
                    }
                }
                ?>

                <?php foreach ($groupedItems as $vendorId => $vendor): ?>
                    <div
                        class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden backdrop-blur-sm">
                        <!-- Vendor Header -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <?php echo Security::e($vendor['vendor_name']); ?>
                            </h3>
                        </div>

                        <!-- Items -->
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php foreach ($vendor['items'] as $item): ?>
                                <div class="p-6 flex gap-4">
                                    <!-- Item Image -->
                                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-xl flex-shrink-0 overflow-hidden">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?php echo Security::e($item['image_url']); ?>"
                                                alt="<?php echo Security::e($item['item_name']); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div
                                                class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Item Details -->
                                    <div class="flex-grow">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            <?php echo Security::e($item['item_name'] ?? $item['package_name']); ?>
                                        </h4>
                                        <p class="text-orange-600 dark:text-orange-400 font-bold mt-1">
                                            Rp
                                            <?php echo number_format($item['price'] ?? $item['price_per_person'], 0, ',', '.'); ?>
                                            <span class="text-gray-400 dark:text-gray-500 font-normal text-sm">/
                                                <?php echo $item['unit'] ?? 'orang'; ?>
                                            </span>
                                        </p>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center justify-between mt-3">
                                            <div
                                                class="flex items-center border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                                                <form action="/cart/update" method="POST" class="m-0">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="cart_item_id"
                                                        value="<?php echo $item['cart_item_id']; ?>">
                                                    <input type="hidden" name="quantity"
                                                        value="<?php echo max(1, $item['quantity'] - 1); ?>">
                                                    <button type="submit"
                                                        class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <span
                                                    class="px-4 py-2 font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50">
                                                    <?php echo $item['quantity']; ?>
                                                </span>
                                                <form action="/cart/update" method="POST" class="m-0">
                                                    <?php echo Security::csrfField(); ?>
                                                    <input type="hidden" name="cart_item_id"
                                                        value="<?php echo $item['cart_item_id']; ?>">
                                                    <input type="hidden" name="quantity"
                                                        value="<?php echo $item['quantity'] + 1; ?>">
                                                    <button type="submit"
                                                        class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            <form action="/cart/remove" method="POST" class="m-0">
                                                <?php echo Security::csrfField(); ?>
                                                <input type="hidden" name="cart_item_id"
                                                    value="<?php echo $item['cart_item_id']; ?>">
                                                <button type="submit"
                                                    class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium flex items-center transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            Rp
                                            <?php echo number_format(($item['price'] ?? $item['price_per_person']) * $item['quantity'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 sticky top-24 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal (
                                <?php echo $cartItemCount; ?> item)
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white">Rp
                                <?php echo number_format($cartTotal, 0, ',', '.'); ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Biaya Pengiriman</span>
                            <span class="text-gray-500 dark:text-gray-500">Hitung saat checkout</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 mt-4 pt-4">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900 dark:text-white">Total</span>
                            <span class="text-orange-600 dark:text-orange-400">Rp
                                <?php echo number_format($cartTotal, 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                    <a href="/checkout"
                        class="btn-glow w-full mt-6 bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-4 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all flex items-center justify-center shadow-lg shadow-orange-500/25">
                        Lanjut ke Checkout
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>

                    <!-- Promo Code -->
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kode Promo</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Masukkan kode"
                                class="flex-grow px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm placeholder-gray-400 dark:placeholder-gray-500">
                            <button
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>