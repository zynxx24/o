<?php include 'layout/header.php'; ?>

<style>
    /* Admin Dashboard Premium Theme */
    .stats-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 200%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% {
            left: -100%;
        }

        100% {
            left: 100%;
        }
    }

    .stats-card:hover {
        transform: translateY(-4px);
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

    .chart-bar {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .chart-bar:hover {
        filter: brightness(1.1);
        transform: scaleY(1.05);
        transform-origin: bottom;
    }

    .pulse-dot {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.2);
        }
    }

    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }
</style>

<main class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30 floating-icon">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                <p class="text-gray-500 dark:text-gray-400">Selamat datang! Berikut ringkasan bisnis Anda.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/admin/export"
                class="btn-glow bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-5 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center gap-2 font-medium transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </a>
            <a href="/admin/settings"
                class="btn-glow bg-gradient-to-r from-orange-500 to-red-500 text-white px-5 py-2.5 rounded-xl hover:from-orange-600 hover:to-red-600 flex items-center gap-2 shadow-lg shadow-orange-500/25 font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Settings
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Customers -->
        <div
            class="stats-card bg-white dark:bg-gray-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <span
                    class="flex items-center gap-1 text-green-600 dark:text-green-400 text-sm font-semibold bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    +12%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                <?php echo number_format($totalCustomers); ?>
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pelanggan</p>
        </div>

        <!-- Total Vendors -->
        <div
            class="stats-card bg-white dark:bg-gray-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <span
                    class="flex items-center gap-1 text-green-600 dark:text-green-400 text-sm font-semibold bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    +5%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                <?php echo number_format($totalVendors); ?>
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Vendor</p>
        </div>

        <!-- Total Revenue -->
        <div
            class="stats-card bg-white dark:bg-gray-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <span
                    class="flex items-center gap-1 text-green-600 dark:text-green-400 text-sm font-semibold bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    +18%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                Rp <?php echo number_format($totalRevenue / 1000000, 1); ?>Jt
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pendapatan</p>
        </div>

        <!-- Active Orders -->
        <div
            class="stats-card bg-white dark:bg-gray-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="stat-icon w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <span
                    class="flex items-center gap-1 text-purple-600 dark:text-purple-400 text-sm font-semibold bg-purple-50 dark:bg-purple-900/30 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-purple-500 rounded-full pulse-dot"></span>
                    Live
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                <?php echo isset($activeOrders) ? number_format($activeOrders) : count(array_filter($recentOrders ?? [], fn($o) => $o['status'] === 'pending' || $o['status'] === 'processing')); ?>
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Pesanan Aktif</p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div
        class="bg-white dark:bg-gray-800/80 rounded-2xl p-6 shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 mb-8 backdrop-blur-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Statistik Pendapatan</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Grafik pendapatan 7 hari terakhir</p>
            </div>
            <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                <button
                    class="px-4 py-2 text-sm rounded-lg bg-white dark:bg-gray-600 text-orange-600 dark:text-orange-400 font-medium shadow-sm transition-all">7
                    Hari</button>
                <button
                    class="px-4 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-600/50 transition-all">30
                    Hari</button>
                <button
                    class="px-4 py-2 text-sm rounded-lg text-gray-600 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-600/50 transition-all">1
                    Tahun</button>
            </div>
        </div>

        <!-- Chart -->
        <div class="flex items-end justify-between h-56 gap-4 px-2">
            <?php
            $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $heights = [60, 80, 45, 90, 75, 100, 85];
            $values = ['2.5', '3.2', '1.8', '4.1', '3.0', '4.5', '3.8'];
            $colors = [
                'from-orange-400 to-orange-500',
                'from-orange-400 to-red-500',
                'from-orange-300 to-orange-400',
                'from-red-400 to-red-500',
                'from-orange-400 to-orange-500',
                'from-red-500 to-pink-500',
                'from-orange-400 to-red-400'
            ];
            foreach ($days as $index => $day):
                ?>
                <div class="flex-1 flex flex-col items-center gap-2 group cursor-pointer">
                    <div
                        class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity font-medium -mb-1">
                        <?php echo $values[$index]; ?>Jt
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden relative"
                        style="height: <?php echo $heights[$index]; ?>%">
                        <div
                            class="chart-bar w-full h-full bg-gradient-to-t <?php echo $colors[$index]; ?> rounded-xl absolute bottom-0 left-0 hover:opacity-90">
                        </div>
                    </div>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400"><?php echo $day; ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center justify-center gap-8 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-gradient-to-r from-orange-400 to-red-500 rounded-full"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Pendapatan Harian</span>
            </div>
            <div class="text-sm text-gray-400 dark:text-gray-500">
                Total: <span class="font-semibold text-gray-900 dark:text-white">Rp
                    <?php echo number_format($totalRevenue / 1000000, 1); ?>Jt</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div
            class="lg:col-span-2 bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pesanan Terbaru</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Daftar pesanan yang masuk</p>
                </div>
                <a href="/orders"
                    class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium flex items-center gap-1 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-4 text-sm font-semibold text-gray-500 dark:text-gray-400">Order ID</th>
                            <th class="pb-4 text-sm font-semibold text-gray-500 dark:text-gray-400">Pelanggan</th>
                            <th
                                class="pb-4 text-sm font-semibold text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                Tanggal</th>
                            <th class="pb-4 text-sm font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="pb-4 text-sm font-semibold text-gray-500 dark:text-gray-400 text-right">Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <?php foreach ($recentOrders as $order): ?>
                            <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-4">
                                    <a href="/order/<?php echo $order['order_id']; ?>"
                                        class="text-sm font-medium text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300">
                                        #<?php echo $order['order_number']; ?>
                                    </a>
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-md">
                                            <?php echo strtoupper(substr($order['user_name'], 0, 1)); ?>
                                        </div>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($order['user_name']); ?></span>
                                    </div>
                                </td>
                                <td class="py-4 text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td class="py-4">
                                    <?php
                                    $statusStyles = [
                                        'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                                        'processing' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                        'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                                    ];
                                    $statusClass = $statusStyles[$order['status']] ?? $statusStyles['pending'];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="py-4 text-sm font-semibold text-gray-900 dark:text-white text-right">
                                    Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($recentOrders)): ?>
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada pesanan terbaru.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- System Status -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></span>
                    Status Sistem
                </h2>

                <div class="space-y-3">
                    <div
                        class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800/50">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-green-100 dark:bg-green-800/50 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">Database</p>
                                <p class="text-xs text-green-600 dark:text-green-400">Connected</p>
                            </div>
                        </div>
                        <div class="w-2.5 h-2.5 bg-green-500 rounded-full pulse-dot"></div>
                    </div>

                    <div
                        class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-blue-100 dark:bg-blue-800/50 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">Server Time</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400"><?php echo date('H:i d M Y'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="/search"
                        class="quick-action-btn p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl text-sm text-gray-600 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:border-orange-200 dark:hover:border-orange-700 hover:text-orange-600 dark:hover:text-orange-400 flex flex-col items-center gap-2 text-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Vendor
                    </a>
                    <a href="/orders"
                        class="quick-action-btn p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl text-sm text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-200 dark:hover:border-blue-700 hover:text-blue-600 dark:hover:text-blue-400 flex flex-col items-center gap-2 text-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Pesanan
                    </a>
                    <a href="/profile"
                        class="quick-action-btn p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl text-sm text-gray-600 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:border-green-200 dark:hover:border-green-700 hover:text-green-600 dark:hover:text-green-400 flex flex-col items-center gap-2 text-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil
                    </a>
                    <a href="/contact"
                        class="quick-action-btn p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl text-sm text-gray-600 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:border-purple-200 dark:hover:border-purple-700 hover:text-purple-600 dark:hover:text-purple-400 flex flex-col items-center gap-2 text-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Bantuan
                    </a>
                </div>
            </div>

            <!-- Contact Messages -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        Pesan Masuk
                    </h2>
                    <?php if (($unreadMessages ?? 0) > 0): ?>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                            <?php echo $unreadMessages; ?> baru
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($contactMessages)): ?>
                    <div class="space-y-3">
                        <?php foreach ($contactMessages as $msg): ?>
                            <a href="/admin/messages/<?php echo $msg['message_id']; ?>"
                                class="block p-3 <?php echo $msg['is_read'] ? 'bg-gray-50 dark:bg-gray-700/50' : 'bg-orange-50 dark:bg-orange-900/30 border border-orange-100 dark:border-orange-800'; ?> rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/70 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            <?php echo htmlspecialchars($msg['name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            <?php echo App\Domain\ContactRepository::getSubjectLabel($msg['subject']); ?>
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                            <?php echo date('d M', strtotime($msg['created_at'])); ?>
                                        </span>
                                        <?php if (!$msg['is_read']): ?>
                                            <span class="w-2 h-2 bg-red-500 rounded-full mt-1"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                    <?php echo htmlspecialchars(substr($msg['message'], 0, 80)); ?>        <?php echo strlen($msg['message']) > 80 ? '...' : ''; ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="/admin/messages"
                        class="block mt-4 text-center text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 font-medium transition-colors">
                        Lihat Semua Pesan →
                    </a>
                <?php else: ?>
                    <div class="text-center py-6">
                        <div
                            class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pesan masuk</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Order Status Summary -->
            <div
                class="bg-white dark:bg-gray-800/80 rounded-2xl shadow-sm dark:shadow-xl border border-gray-100 dark:border-gray-700 p-6 backdrop-blur-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Status Pesanan</h2>
                <div class="space-y-3">
                    <?php
                    $statusCount = [
                        'pending' => 0,
                        'processing' => 0,
                        'completed' => 0,
                        'cancelled' => 0
                    ];
                    foreach ($recentOrders ?? [] as $order) {
                        if (isset($statusCount[$order['status']])) {
                            $statusCount[$order['status']]++;
                        }
                    }
                    $total = array_sum($statusCount) ?: 1;
                    ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full"
                                    style="width: <?php echo ($statusCount['pending'] / $total) * 100; ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white w-6 text-right"><?php echo $statusCount['pending']; ?></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Processing</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 rounded-full"
                                    style="width: <?php echo ($statusCount['processing'] / $total) * 100; ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white w-6 text-right"><?php echo $statusCount['processing']; ?></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-green-400 rounded-full"
                                    style="width: <?php echo ($statusCount['completed'] / $total) * 100; ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white w-6 text-right"><?php echo $statusCount['completed']; ?></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cancelled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-red-400 rounded-full"
                                    style="width: <?php echo ($statusCount['cancelled'] / $total) * 100; ?>%"></div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white w-6 text-right"><?php echo $statusCount['cancelled']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>