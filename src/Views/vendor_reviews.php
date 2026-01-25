<?php
include 'layout/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ulasan Pelanggan</h1>
            <p class="text-gray-500">Lihat apa kata pelanggan tentang layanan Anda</p>
        </div>
        <div class="flex gap-2">
            <a href="/vendor-dashboard"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Rating Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center border-r border-gray-100 last:border-0">
                <p class="text-gray-500 text-sm mb-1">Total Rating</p>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span
                        class="text-4xl font-bold text-gray-900"><?php echo number_format($ratingSummary['avg_rating'] ?? 0, 1); ?></span>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">dari <?php echo $ratingSummary['total_reviews'] ?? 0; ?> ulasan</p>
            </div>

            <div class="text-center border-r border-gray-100 last:border-0">
                <p class="text-gray-500 text-sm mb-1">Makanan</p>
                <div class="text-2xl font-bold text-gray-900 mb-1">22
                    <?php echo number_format($ratingSummary['avg_food_rating'] ?? 0, 1); ?>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 max-w-[100px] mx-auto overflow-hidden">
                    <div class="bg-orange-500 h-1.5 rounded-full"
                        style="width: <?php echo (($ratingSummary['avg_food_rating'] ?? 0) / 5) * 100; ?>%"></div>
                </div>
            </div>

            <div class="text-center border-r border-gray-100 last:border-0">
                <p class="text-gray-500 text-sm mb-1">Layanan</p>
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    <?php echo number_format($ratingSummary['avg_service_rating'] ?? 0, 1); ?>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 max-w-[100px] mx-auto overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full"
                        style="width: <?php echo (($ratingSummary['avg_service_rating'] ?? 0) / 5) * 100; ?>%"></div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-gray-500 text-sm mb-1">Pengiriman</p>
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    <?php echo number_format($ratingSummary['avg_delivery_rating'] ?? 0, 1); ?>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 max-w-[100px] mx-auto overflow-hidden">
                    <div class="bg-green-500 h-1.5 rounded-full"
                        style="width: <?php echo (($ratingSummary['avg_delivery_rating'] ?? 0) / 5) * 100; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="grid grid-cols-1 gap-6">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-12 bg-white rounded-xl border border-gray-100">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada ulasan</h3>
                <p class="text-gray-500">Ulasan dari pelanggan akan muncul di sini.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-900 rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($review['user_name']); ?></h3>
                                <p class="text-sm text-gray-500">Order #<?php echo $review['order_number'] ?? '-'; ?></p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-400">
                            <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                        </span>
                    </div>

                    <div class="flex items-center gap-1 mb-3">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg class="w-5 h-5 <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-200'; ?>"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endfor; ?>
                        <span class="text-sm font-medium text-gray-900 ml-2"><?php echo $review['rating']; ?>.0</span>
                    </div>

                    <p class="text-gray-600 leading-relaxed mb-4">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </p>

                    <div class="flex gap-4 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                        <?php if ($review['food_rating']): ?>
                            <div class="flex items-center gap-1">
                                <span class="font-medium text-gray-700">Makanan:</span>
                                <span class="text-orange-600 font-bold"><?php echo $review['food_rating']; ?>/5</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($review['service_rating']): ?>
                            <div class="flex items-center gap-1">
                                <span class="font-medium text-gray-700">Layanan:</span>
                                <span class="text-blue-600 font-bold"><?php echo $review['service_rating']; ?>/5</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($review['delivery_rating']): ?>
                            <div class="flex items-center gap-1">
                                <span class="font-medium text-gray-700">Pengiriman:</span>
                                <span class="text-green-600 font-bold"><?php echo $review['delivery_rating']; ?>/5</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'layout/footer.php'; ?>