<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
$error = Session::flash('error');

$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    'confirmed' => 'bg-blue-100 text-blue-700 border-blue-200',
    'preparing' => 'bg-purple-100 text-purple-700 border-purple-200',
    'delivering' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
    'completed' => 'bg-green-100 text-green-700 border-green-200',
    'cancelled' => 'bg-red-100 text-red-700 border-red-200'
];
$statusLabels = [
    'pending' => 'Menunggu Konfirmasi',
    'confirmed' => 'Dikonfirmasi',
    'preparing' => 'Sedang Diproses',
    'delivering' => 'Dalam Pengiriman',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];
?>

<div class="max-w-4xl mx-auto py-8">
    <!-- Back Button -->
    <a href="/orders" class="inline-flex items-center text-gray-600 hover:text-orange-600 font-medium mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
            </path>
        </svg>
        Kembali ke Riwayat Pesanan
    </a>

    <?php if ($success): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-600">
            <?php echo Security::e($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600">
            <?php echo Security::e($error); ?>
        </div>
    <?php endif; ?>

    <!-- Order Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo Security::e($order['order_number']); ?>
                    </h1>
                    <span
                        class="px-4 py-1.5 rounded-full text-sm font-medium border <?php echo $statusColors[$order['status']]; ?>">
                        <?php echo $statusLabels[$order['status']]; ?>
                    </span>
                </div>
                <p class="text-gray-500">Dipesan pada
                    <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?> WIB
                </p>
            </div>

            <?php if ($order['status'] === 'pending'): ?>
                <form action="/order/<?php echo $order['order_id']; ?>/cancel" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    <?php echo Security::csrfField(); ?>
                    <button type="submit"
                        class="px-4 py-2 text-red-600 border border-red-200 rounded-lg hover:bg-red-50 font-medium transition">
                        Batalkan Pesanan
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Progress (if not cancelled) -->
            <?php if ($order['status'] !== 'cancelled'): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">Status Pesanan</h3>

                    <?php
                    $steps = ['pending', 'confirmed', 'preparing', 'delivering', 'completed'];
                    $currentIndex = array_search($order['status'], $steps);
                    ?>

                    <div class="relative">
                        <div class="flex justify-between">
                            <?php foreach ($steps as $index => $step): ?>
                                <?php
                                $isCompleted = $index < $currentIndex;
                                $isCurrent = $index === $currentIndex;
                                ?>
                                <div class="flex flex-col items-center flex-1 relative">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center 
                                        <?php echo $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-400'); ?>">
                                        <?php if ($isCompleted): ?>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        <?php else: ?>
                                            <?php echo $index + 1; ?>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="mt-2 text-xs text-center <?php echo $isCurrent ? 'font-semibold text-orange-600' : 'text-gray-500'; ?>">
                                        <?php echo $statusLabels[$step]; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Progress Line -->
                        <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200 -z-10" style="margin: 0 5%;">
                            <div class="h-full bg-green-500"
                                style="width: <?php echo min(100, ($currentIndex / (count($steps) - 1)) * 100); ?>%;"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Detail Pesanan</h3>

                <div class="divide-y divide-gray-100">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="py-4 flex gap-4 first:pt-0 last:pb-0">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo Security::e($item['image_url']); ?>"
                                        alt="<?php echo Security::e($item['item_name']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow">
                                <h4 class="font-medium text-gray-900">
                                    <?php echo Security::e($item['item_name']); ?>
                                </h4>
                                <p class="text-sm text-gray-500">
                                    <?php echo $item['quantity']; ?> × Rp
                                    <?php echo number_format($item['unit_price'], 0, ',', '.'); ?>
                                </p>
                            </div>
                            <div class="text-right font-semibold">
                                Rp
                                <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Vendor Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Vendor</h3>
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-500 rounded-lg flex items-center justify-center text-white font-bold">
                        <?php echo strtoupper(substr($order['vendor_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">
                            <?php echo Security::e($order['vendor_name']); ?>
                        </h4>
                        <p class="text-sm text-gray-500">
                            <?php echo Security::e($order['vendor_phone'] ?? '-'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Rincian Pembayaran</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp
                            <?php echo number_format($order['subtotal'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">PPN</span>
                        <span>Rp
                            <?php echo number_format($order['tax'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span>Rp
                            <?php echo number_format($order['delivery_fee'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                        <div class="flex justify-between text-green-600">
                            <span>Diskon</span>
                            <span>- Rp
                                <?php echo number_format($order['discount'], 0, ',', '.'); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t border-gray-100 mt-4 pt-4">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-orange-600">Rp
                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <?php
                    $paymentStatusColors = [
                        'unpaid' => 'bg-red-100 text-red-700',
                        'partial' => 'bg-yellow-100 text-yellow-700',
                        'paid' => 'bg-green-100 text-green-700',
                        'refunded' => 'bg-gray-100 text-gray-700'
                    ];
                    $paymentLabels = [
                        'unpaid' => 'Belum Dibayar',
                        'partial' => 'Sebagian Dibayar',
                        'paid' => 'Lunas',
                        'refunded' => 'Dikembalikan'
                    ];
                    ?>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $paymentStatusColors[$order['payment_status']]; ?>">
                        <?php echo $paymentLabels[$order['payment_status']]; ?>
                    </span>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Detail Acara</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <div>
                            <p class="text-gray-500">Tanggal & Waktu</p>
                            <p class="font-medium text-gray-900">
                                <?php echo date('d M Y', strtotime($order['event_date'])); ?>,
                                <?php echo date('H:i', strtotime($order['event_time'])); ?> WIB
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <div>
                            <p class="text-gray-500">Jumlah Tamu</p>
                            <p class="font-medium text-gray-900">
                                <?php echo $order['num_people']; ?> orang
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <div>
                            <p class="text-gray-500">Jenis Acara</p>
                            <p class="font-medium text-gray-900">
                                <?php echo Security::e($order['event_type'] ?? '-'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-gray-500">Alamat Pengiriman</p>
                            <p class="font-medium text-gray-900">
                                <?php echo Security::e($order['delivery_address']); ?>
                            </p>
                            <?php if ($order['delivery_city']): ?>
                                <p class="text-gray-500">
                                    <?php echo Security::e($order['delivery_city']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Request -->
            <?php if (!empty($order['special_request'])): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Catatan Khusus</h3>
                    <p class="text-gray-600 text-sm">
                        <?php echo nl2br(Security::e($order['special_request'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Review Form (for completed orders) -->
            <?php if ($order['status'] === 'completed' && !$hasReview): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Berikan Ulasan</h3>

                    <form action="/order/<?php echo $order['order_id']; ?>/review" method="POST" id="reviewForm">
                        <?php echo Security::csrfField(); ?>

                        <!-- Main Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating Keseluruhan *</label>
                            <div class="flex items-center gap-1" id="mainRating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" onclick="setRating('rating', <?php echo $i; ?>)"
                                        class="star-btn p-1 hover:scale-110 transition" data-rating="<?php echo $i; ?>">
                                        <svg class="w-8 h-8 text-gray-300 star-icon" data-star="<?php echo $i; ?>"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    </button>
                                <?php endfor; ?>
                                <span class="ml-2 text-sm text-gray-500" id="ratingText">Pilih rating</span>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="" required>
                        </div>

                        <!-- Sub Ratings -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Food Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Makanan</label>
                                <div class="flex items-center gap-0.5" id="foodRating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" onclick="setRating('food_rating', <?php echo $i; ?>)"
                                            class="p-0.5">
                                            <svg class="w-5 h-5 text-gray-300 food-star" data-star="<?php echo $i; ?>"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="food_rating" id="food_ratingInput" value="">
                            </div>

                            <!-- Service Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pelayanan</label>
                                <div class="flex items-center gap-0.5" id="serviceRating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" onclick="setRating('service_rating', <?php echo $i; ?>)"
                                            class="p-0.5">
                                            <svg class="w-5 h-5 text-gray-300 service-star" data-star="<?php echo $i; ?>"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="service_rating" id="service_ratingInput" value="">
                            </div>

                            <!-- Delivery Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pengiriman</label>
                                <div class="flex items-center gap-0.5" id="deliveryRating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" onclick="setRating('delivery_rating', <?php echo $i; ?>)"
                                            class="p-0.5">
                                            <svg class="w-5 h-5 text-gray-300 delivery-star" data-star="<?php echo $i; ?>"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="delivery_rating" id="delivery_ratingInput" value="">
                            </div>
                        </div>

                        <!-- Review Text -->
                        <div class="mb-6">
                            <label for="review_text" class="block text-sm font-medium text-gray-700 mb-2">Ulasan
                                Anda</label>
                            <textarea name="review_text" id="review_text" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                                placeholder="Ceritakan pengalaman Anda dengan katering ini..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitReview"
                            class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled>
                            Kirim Ulasan
                        </button>
                    </form>
                </div>

                <script>
                    const ratingTexts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];

                    function setRating(field, value) {
                        document.getElementById(field + 'Input').value = value;

                        // Update stars visual
                        let starClass = '';
                        if (field === 'rating') starClass = '.star-icon';
                        else if (field === 'food_rating') starClass = '.food-star';
                        else if (field === 'service_rating') starClass = '.service-star';
                        else if (field === 'delivery_rating') starClass = '.delivery-star';

                        document.querySelectorAll(starClass).forEach(star => {
                            const starValue = parseInt(star.dataset.star);
                            if (starValue <= value) {
                                star.classList.remove('text-gray-300');
                                star.classList.add('text-yellow-400');
                            } else {
                                star.classList.remove('text-yellow-400');
                                star.classList.add('text-gray-300');
                            }
                        });

                        // Update text for main rating
                        if (field === 'rating') {
                            document.getElementById('ratingText').textContent = ratingTexts[value];
                            document.getElementById('submitReview').disabled = false;
                        }
                    }
                </script>
            <?php elseif ($order['status'] === 'completed' && $hasReview): ?>
                <div class="bg-green-50 rounded-2xl border border-green-200 p-6 text-center">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700 font-medium">Anda sudah memberikan ulasan untuk pesanan ini</p>
                    <a href="/vendor/<?php echo $order['vendor_id']; ?>/reviews"
                        class="text-green-600 text-sm hover:underline mt-2 inline-block">
                        Lihat semua ulasan →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>