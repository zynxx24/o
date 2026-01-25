<?php
use App\Config\Session;
use App\Middleware\Security;

include 'layout/header.php';
$success = Session::flash('success');
?>

<div class="max-w-5xl mx-auto py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
            <span class="w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full mr-3"></span>
            Riwayat Pesanan
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Lihat status dan detail pesanan Anda</p>
    </div>

    <?php if ($success): ?>
        <div
            class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-green-600 dark:text-green-400 backdrop-blur-sm">
            <?php echo Security::e($success); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <!-- Empty State -->
        <div
            class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-12 text-center backdrop-blur-sm">
            <div
                class="w-24 h-24 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/50 dark:to-orange-800/50 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Belum Ada Pesanan</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Anda belum melakukan pemesanan apapun.</p>
            <a href="/"
                class="btn-glow inline-flex items-center bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-500/25">
                Mulai Pesan
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="space-y-4">
            <?php foreach ($orders as $order): ?>
                <?php
                $statusColors = [
                    'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                    'confirmed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                    'preparing' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                    'delivering' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                    'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                    'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                ];
                $statusLabels = [
                    'pending' => 'Menunggu Konfirmasi',
                    'confirmed' => 'Dikonfirmasi',
                    'preparing' => 'Sedang Diproses',
                    'delivering' => 'Dalam Pengiriman',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan'
                ];
                $paymentColors = [
                    'unpaid' => 'text-red-500 dark:text-red-400',
                    'partial' => 'text-yellow-500 dark:text-yellow-400',
                    'paid' => 'text-green-500 dark:text-green-400',
                    'refunded' => 'text-gray-500 dark:text-gray-400'
                ];
                ?>
                <div
                    class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-2xl transition-all card-hover backdrop-blur-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <!-- Order Info -->
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-bold text-gray-900 dark:text-white">
                                    <?php echo Security::e($order['order_number']); ?>
                                </h3>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium <?php echo $statusColors[$order['status']]; ?>">
                                    <?php echo $statusLabels[$order['status']]; ?>
                                </span>
                            </div>

                            <p class="text-gray-600 dark:text-gray-400">
                                <?php echo Security::e($order['vendor_name']); ?>
                            </p>

                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <?php echo date('d M Y', strtotime($order['event_date'])); ?>
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo date('H:i', strtotime($order['event_time'])); ?> WIB
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>
                                    </svg>
                                    <?php echo $order['num_people']; ?> orang
                                </span>
                            </div>
                        </div>

                        <!-- Price & Actions -->
                        <div class="text-right">
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                Rp
                                <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                            </p>
                            <p class="text-sm <?php echo $paymentColors[$order['payment_status']]; ?>">
                                <?php
                                $paymentLabels = [
                                    'unpaid' => 'Belum Dibayar',
                                    'partial' => 'Sebagian Dibayar',
                                    'paid' => 'Lunas',
                                    'refunded' => 'Dikembalikan'
                                ];
                                echo $paymentLabels[$order['payment_status']];
                                ?>
                            </p>
                            <a href="/order/<?php echo $order['order_id']; ?>"
                                class="inline-flex items-center mt-3 text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium text-sm transition-colors">
                                Lihat Detail
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalOrders > count($orders)): ?>
            <div class="mt-8 flex justify-center">
                <nav class="inline-flex rounded-xl shadow-sm overflow-hidden">
                    <a href="?page=<?php echo max(1, ($currentPage ?? 1) - 1); ?>"
                        class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Sebelumnya
                    </a>
                    <a href="?page=<?php echo ($currentPage ?? 1) + 1; ?>"
                        class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Selanjutnya
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>