<?php

use App\Middleware\Security;

include 'layout/header.php';
?>

<style>
    .stats-card {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        transition: all 0.3s ease;
    }

    .stats-card:hover .stat-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    .table-row {
        transition: all 0.2s ease;
    }

    .table-row:hover {
        background: rgba(249, 115, 22, 0.05);
    }

    .action-btn {
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }
</style>

<main class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-green-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Vendor Dashboard</h1>
                <p class="text-gray-500">
                    <?php echo htmlspecialchars($vendor['vendor_name'] ?? 'Vendor'); ?>
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/vendor/menu"
                class="action-btn bg-white text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 border border-gray-200 flex items-center gap-2 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Kelola Menu
            </a>
            <a href="/vendor/orders"
                class="action-btn bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-2.5 rounded-xl hover:from-green-600 hover:to-emerald-700 flex items-center gap-2 shadow-lg shadow-green-500/25 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                Lihat Pesanan
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Orders -->
        <div class="stats-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">
                <?php echo number_format($totalOrders ?? 0); ?>
            </h3>
            <p class="text-gray-500 text-sm">Total Pesanan</p>
        </div>

        <!-- Pending Orders -->
        <div class="stats-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <?php if (($pendingOrders ?? 0) > 0): ?>
                    <span class="bg-orange-100 text-orange-600 text-xs font-bold px-2.5 py-1 rounded-lg">
                        Perlu Diproses
                    </span>
                <?php endif; ?>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">
                <?php echo number_format($pendingOrders ?? 0); ?>
            </h3>
            <p class="text-gray-500 text-sm">Pesanan Pending</p>
        </div>

        <!-- Revenue -->
        <div class="stats-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">
                Rp
                <?php echo number_format(($totalRevenue ?? 0) / 1000000, 1); ?>Jt
            </h3>
            <p class="text-gray-500 text-sm">Total Pendapatan</p>
        </div>

        <!-- Rating -->
        <div class="stats-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 mb-1">
                <?php echo number_format($vendor['rating'] ?? 0, 1); ?>
            </h3>
            <p class="text-gray-500 text-sm">
                <?php echo $vendor['total_reviews'] ?? 0; ?> ulasan
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Pesanan Terbaru</h2>
                    <p class="text-gray-500 text-sm">Kelola pesanan masuk</p>
                </div>
                <a href="/vendor/orders"
                    class="text-green-600 hover:text-green-700 text-sm font-medium flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <?php if (!empty($recentOrders)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-100">
                                <th class="pb-4 text-sm font-semibold text-gray-500">Order</th>
                                <th class="pb-4 text-sm font-semibold text-gray-500">Pelanggan</th>
                                <th class="pb-4 text-sm font-semibold text-gray-500 hidden sm:table-cell">Event</th>
                                <th class="pb-4 text-sm font-semibold text-gray-500">Status</th>
                                <th class="pb-4 text-sm font-semibold text-gray-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($recentOrders as $order): ?>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <p class="text-sm font-medium text-gray-900">#
                                            <?php echo $order['order_number']; ?>
                                        </p>
                                        <p class="text-xs text-gray-500">Rp
                                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                                <?php echo strtoupper(substr($order['user_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <span class="text-sm text-gray-700">
                                                <?php echo htmlspecialchars($order['user_name'] ?? 'Unknown'); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm text-gray-500 hidden sm:table-cell">
                                        <?php echo date('d M Y', strtotime($order['event_date'])); ?>
                                    </td>
                                    <td class="py-4">
                                        <?php
                                        $statusStyles = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'confirmed' => 'bg-blue-100 text-blue-700',
                                            'preparing' => 'bg-purple-100 text-purple-700',
                                            'delivering' => 'bg-cyan-100 text-cyan-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700'
                                        ];
                                        $statusClass = $statusStyles[$order['status']] ?? $statusStyles['pending'];
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                                class="inline">
                                                <?php echo Security::csrfField(); ?>
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit"
                                                    class="text-xs bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600 transition">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        <?php elseif ($order['status'] === 'confirmed'): ?>
                                            <form action="/vendor/order/<?php echo $order['order_id']; ?>/status" method="POST"
                                                class="inline">
                                                <?php echo Security::csrfField(); ?>
                                                <input type="hidden" name="status" value="preparing">
                                                <button type="submit"
                                                    class="text-xs bg-purple-500 text-white px-3 py-1.5 rounded-lg hover:bg-purple-600 transition">
                                                    Proses
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="/vendor/order/<?php echo $order['order_id']; ?>"
                                                class="text-xs text-gray-600 hover:text-gray-800">
                                                Detail →
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Belum ada pesanan.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Menu Aktif</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">
                            <?php echo $totalMenuItems ?? 0; ?>
                        </p>
                        <p class="text-sm text-gray-500">Item menu</p>
                    </div>
                    <a href="/vendor/menu"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Kelola
                    </a>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Reviews Terbaru</h2>
                    <a href="/vendor/reviews" class="text-green-600 hover:text-green-700 text-sm font-medium">
                        Lihat →
                    </a>
                </div>

                <?php if (!empty($recentReviews)): ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($recentReviews, 0, 3) as $review): ?>
                            <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="flex text-yellow-400">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <svg class="w-4 h-4 <?php echo $i < $review['rating'] ? 'fill-current' : 'text-gray-300'; ?>"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('d M', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <?php echo htmlspecialchars($review['review_text'] ?? ''); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada review</p>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h2>
                <div class="space-y-2">
                    <a href="/vendor/menu" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div
                            class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Tambah Menu Baru</span>
                    </a>
                    <a href="/vendor/orders?status=pending"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div
                            class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Pesanan Pending</span>
                    </a>
                    <a href="/vendor/reviews"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                        <div
                            class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Lihat Reviews</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>