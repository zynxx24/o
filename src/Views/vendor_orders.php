<?php
include 'layout/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Pesanan</h1>
            <p class="text-gray-500">Pantau dan update status pesanan masuk</p>
        </div>
        <div class="flex gap-2">
            <a href="/vendor-dashboard"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form action="/vendor/orders" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Order ID / Nama</label>
                <input type="text" name="q" value="<?php echo htmlspecialchars($filters['q'] ?? ''); ?>"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                    placeholder="Contoh: ORD-2024-001">
            </div>
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>
                        >Pending</option>
                    <option value="confirmed" <?php echo ($filters['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="preparing" <?php echo ($filters['status'] ?? '') === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="delivering" <?php echo ($filters['status'] ?? '') === 'delivering' ? 'selected' : ''; ?>>Delivering</option>
                    <option value="completed" <?php echo ($filters['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($filters['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($filters['date'] ?? ''); ?>"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>
            <button type="submit"
                class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                Filter
            </button>
            <?php if (!empty($filters)): ?>
                <a href="/vendor/orders" class="px-4 py-2.5 text-gray-500 hover:text-gray-700 font-medium">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <?php if (empty($orders)): ?>
            <div class="text-center py-12 bg-white rounded-xl border border-gray-100">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Tidak ada pesanan ditemukan</h3>
                <p class="text-gray-500">Coba ubah filter pencarian Anda.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="p-6">
                        <div
                            class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 mb-4 border-b border-gray-50 pb-4">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">#
                                        <?php echo $order['order_number']; ?>
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        <?php
                                        echo match ($order['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'confirmed' => 'bg-blue-100 text-blue-700',
                                            'preparing' => 'bg-purple-100 text-purple-700',
                                            'delivering' => 'bg-cyan-100 text-cyan-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">
                                    Dipesan oleh <span class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($order['user_name']); ?>
                                    </span>
                                    •
                                    <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">Rp
                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?php echo $order['num_people']; ?> orang •
                                    <?php echo ucfirst($order['order_type']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Event Details -->
                            <div class="lg:col-span-1 space-y-3">
                                <h4 class="font-medium text-gray-900">Detail Acara</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>
                                            <?php echo date('d F Y', strtotime($order['event_date'])); ?>,
                                            <?php echo substr($order['event_time'], 0, 5); ?>
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-gray-600">
                                            <?php echo htmlspecialchars($order['delivery_address']); ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($order['special_request'])): ?>
                                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                            <p class="text-xs font-bold text-yellow-800 uppercase mb-1">Catatan Khusus</p>
                                            <p class="text-sm text-yellow-700">
                                                <?php echo nl2br(htmlspecialchars($order['special_request'])); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="lg:col-span-2">
                                <h4 class="font-medium text-gray-900 mb-2">Item Pesanan</h4>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                    <!-- Items would be loaded via relationship or passed formatted -->
                                    <div class="flex justify-between text-sm">
                                        <span>
                                            <?php echo $order['order_type'] == 'package' ? 'Paket Katering' : 'Custom Order'; ?>
                                        </span>
                                        <span class="font-medium">Rp
                                            <?php echo number_format($order['subtotal'], 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                    <!-- Add detailed items loop here if data available -->
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 flex flex-wrap gap-2 justify-end">
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                            class="inline">
                                            <?php echo App\Middleware\Security::csrfField(); ?>
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" onclick="return confirm('Yakin tolak pesanan ini?')"
                                                class="px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-sm font-medium">
                                                Tolak
                                            </button>
                                        </form>
                                        <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                            class="inline">
                                            <?php echo App\Middleware\Security::csrfField(); ?>
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                                                Konfirmasi Pesanan
                                            </button>
                                        </form>
                                    <?php elseif ($order['status'] === 'confirmed'): ?>
                                        <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                            class="inline">
                                            <?php echo App\Middleware\Security::csrfField(); ?>
                                            <input type="hidden" name="status" value="preparing">
                                            <button type="submit"
                                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-sm">
                                                Mulai Masak/Siapkan
                                            </button>
                                        </form>
                                    <?php elseif ($order['status'] === 'preparing'): ?>
                                        <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                            class="inline">
                                            <?php echo App\Middleware\Security::csrfField(); ?>
                                            <input type="hidden" name="status" value="delivering">
                                            <button type="submit"
                                                class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 text-sm font-medium shadow-sm">
                                                Mulai Pengiriman
                                            </button>
                                        </form>

                                    <?php elseif ($order['status'] === 'delivering'): ?>
                                        <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                            class="inline">
                                            <?php echo App\Middleware\Security::csrfField(); ?>
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium shadow-sm">
                                                Selesaikan Pesanan
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="/order/<?php echo $order['order_id']; ?>"
                                        class="px-4 py-2 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'layout/footer.php'; ?>