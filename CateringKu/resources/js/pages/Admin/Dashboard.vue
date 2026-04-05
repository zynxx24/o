<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'

const props = defineProps<{
    stats: {
        totalOrders: number, totalRevenue: number, totalUsers: number, totalVendors: number,
        pendingOrders: number, unreadMessages: number
    }
    recentOrders: any[]
    pendingPayments: any[]
}>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) }
function formatTime(d: string) { return new Date(d).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-amber-50 text-amber-700 border border-amber-200', confirmed: 'bg-sky-50 text-sky-700 border border-sky-200',
    preparing: 'bg-violet-50 text-violet-700 border border-violet-200', delivering: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
    completed: 'bg-emerald-50 text-emerald-700 border border-emerald-200', cancelled: 'bg-rose-50 text-rose-700 border border-rose-200',
}
const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}

const greeting = computed(() => {
    const h = new Date().getHours()
    if (h < 12) return 'Selamat Pagi'
    if (h < 15) return 'Selamat Siang'
    if (h < 18) return 'Selamat Sore'
    return 'Selamat Malam'
})

const today = computed(() => {
    return new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
})

const animatedStats = ref({ totalOrders: 0, totalRevenue: 0, totalUsers: 0, totalVendors: 0 })

function animateNumber(key: string, target: number, duration = 1200) {
    const start = 0
    const startTime = performance.now()
    function step(currentTime: number) {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)
        const eased = 1 - Math.pow(1 - progress, 3)
        ;(animatedStats.value as any)[key] = Math.floor(start + (target - start) * eased)
        if (progress < 1) requestAnimationFrame(step)
    }
    requestAnimationFrame(step)
}

onMounted(() => {
    animateNumber('totalOrders', props.stats.totalOrders)
    animateNumber('totalRevenue', props.stats.totalRevenue, 1500)
    animateNumber('totalUsers', props.stats.totalUsers)
    animateNumber('totalVendors', props.stats.totalVendors)
})

const statCards = [
    { key: 'totalOrders', label: 'Total Pesanan', icon: '📦', gradient: 'from-blue-500 via-blue-600 to-indigo-700', bg: 'bg-blue-50', ring: 'ring-blue-100' },
    { key: 'totalRevenue', label: 'Total Pendapatan', icon: '💰', gradient: 'from-emerald-500 via-green-600 to-teal-700', bg: 'bg-emerald-50', ring: 'ring-emerald-100', isCurrency: true },
    { key: 'totalUsers', label: 'Total Pelanggan', icon: '👥', gradient: 'from-violet-500 via-purple-600 to-indigo-700', bg: 'bg-violet-50', ring: 'ring-violet-100' },
    { key: 'totalVendors', label: 'Total Vendor', icon: '🏪', gradient: 'from-orange-500 via-amber-600 to-yellow-700', bg: 'bg-orange-50', ring: 'ring-orange-100' },
]

const quickActions = [
    { label: 'Konfirmasi Pesanan', icon: '✅', href: '/admin/orders?status=pending', count: props.stats.pendingOrders, color: 'from-amber-400 to-orange-500' },
    { label: 'Baca Pesan', icon: '📬', href: '/admin/messages', count: props.stats.unreadMessages, color: 'from-blue-400 to-indigo-500' },
    { label: 'Verifikasi Bayar', icon: '💳', href: '/admin/orders', count: props.pendingPayments?.length || 0, color: 'from-emerald-400 to-teal-500' },
    { label: 'Lihat Website', icon: '🌐', href: '/', count: null, color: 'from-purple-400 to-pink-500' },
]

// Mock revenue chart data from recent orders
const chartBars = computed(() => {
    const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']
    const now = new Date()
    const dayOfWeek = now.getDay()
    const values = days.map((_, i) => {
        const ordersForDay = props.recentOrders.filter(o => {
            const d = new Date(o.created_at)
            return d.getDay() === ((i + 1) % 7)
        })
        return ordersForDay.reduce((sum: number, o: any) => sum + (o.total_amount || 0), 0)
    })
    const max = Math.max(...values, 1)
    return days.map((label, i) => ({
        label,
        value: values[i],
        height: Math.max((values[i] / max) * 100, 4),
        isToday: ((i + 1) % 7) === dayOfWeek,
    }))
})
</script>

<template>
    <Head title="Admin Dashboard - CateringKu" />
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100">
        <!-- Admin Navbar -->
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-4">
                        <Link href="/" class="flex items-center gap-2.5 group">
                            <img src="/images/logo.svg" alt="CateringKu" class="h-9 w-9 rounded-xl shadow-sm group-hover:shadow-md transition-shadow" />
                            <span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span>
                        </Link>
                        <span class="px-2.5 py-1 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs rounded-lg font-bold shadow-sm shadow-red-200">ADMIN</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                        <Link href="/admin/orders" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesanan</Link>
                        <Link href="/admin/messages" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all relative">
                            Pesan
                            <span v-if="stats.unreadMessages > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full font-bold animate-pulse">{{ stats.unreadMessages }}</span>
                        </Link>
                        <div class="w-px h-6 bg-gray-200 mx-2"></div>
                        <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Greeting Header -->
            <div class="mb-8 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ greeting }}, Admin! 👋
                        </h1>
                        <p class="text-gray-500 mt-1.5 text-sm">{{ today }} — Berikut ringkasan bisnis Anda hari ini.</p>
                    </div>
                    <div class="hidden md:flex items-center gap-3">
                        <div class="px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm text-sm text-gray-600">
                            <span class="font-semibold text-gray-800">{{ stats.pendingOrders }}</span> pesanan menunggu
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div v-for="(card, index) in statCards" :key="card.key"
                     class="group relative bg-white rounded-2xl border border-gray-100/80 p-6 hover:shadow-xl hover:shadow-gray-100/50 hover:-translate-y-1 transition-all duration-300"
                     :style="{ animationDelay: `${index * 100}ms` }">
                    <div class="flex items-start justify-between mb-4">
                        <div :class="`w-14 h-14 bg-gradient-to-br ${card.gradient} rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg group-hover:scale-110 transition-transform duration-300`">
                            {{ card.icon }}
                        </div>
                        <div :class="`w-2 h-2 rounded-full bg-gradient-to-r ${card.gradient} animate-pulse`"></div>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        {{ card.isCurrency ? formatPrice(animatedStats[card.key as keyof typeof animatedStats]) : animatedStats[card.key as keyof typeof animatedStats] }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1.5 font-medium">{{ card.label }}</p>
                    <!-- Subtle gradient border on hover -->
                    <div :class="`absolute inset-0 rounded-2xl ring-2 ${card.ring} opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none`"></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <Link v-for="action in quickActions" :key="action.label" :href="action.href"
                      class="group flex items-center gap-3 bg-white rounded-2xl border border-gray-100/80 p-4 hover:shadow-lg hover:shadow-gray-100/50 hover:-translate-y-0.5 transition-all duration-200">
                    <div :class="`w-11 h-11 bg-gradient-to-br ${action.color} rounded-xl flex items-center justify-center text-white text-lg shadow-sm group-hover:scale-110 transition-transform`">
                        {{ action.icon }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ action.label }}</p>
                        <p v-if="action.count !== null" class="text-xs text-gray-400">{{ action.count }} item</p>
                    </div>
                    <svg v-if="action.count && action.count > 0" class="w-4 h-4 text-gray-300 group-hover:text-ck-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100/80 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Pendapatan Minggu Ini</h3>
                            <p class="text-sm text-gray-400 mt-0.5">Overview harian berdasarkan pesanan terbaru</p>
                        </div>
                        <div class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                            {{ formatPrice(stats.totalRevenue) }}
                        </div>
                    </div>
                    <div class="flex items-end justify-between gap-3 h-48 px-2">
                        <div v-for="bar in chartBars" :key="bar.label" class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full relative flex items-end justify-center" style="height: 160px">
                                <div :class="[
                                    'w-full max-w-[48px] rounded-xl transition-all duration-700 ease-out',
                                    bar.isToday 
                                        ? 'bg-gradient-to-t from-ck-primary to-orange-400 shadow-lg shadow-orange-200/50' 
                                        : 'bg-gradient-to-t from-gray-200 to-gray-100 hover:from-ck-primary/60 hover:to-orange-300/60'
                                ]"
                                :style="{ height: `${bar.height}%` }">
                                </div>
                            </div>
                            <span :class="['text-xs font-semibold', bar.isToday ? 'text-ck-primary' : 'text-gray-400']">{{ bar.label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Alert / Activity Column -->
                <div class="space-y-4">
                    <!-- Pending Alert -->
                    <div v-if="stats.pendingOrders > 0" class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/60 rounded-2xl p-5 animate-fade-in">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white text-lg shadow-sm">⏳</div>
                            <div>
                                <p class="font-bold text-amber-900 text-sm">{{ stats.pendingOrders }} Pesanan Menunggu</p>
                                <p class="text-xs text-amber-600">Perlu dikonfirmasi segera</p>
                            </div>
                        </div>
                        <Link href="/admin/orders?status=pending" class="inline-flex items-center gap-1 text-amber-700 hover:text-amber-900 text-xs font-bold transition-colors">
                            Lihat Semua →
                        </Link>
                    </div>

                    <!-- Payment Alert -->
                    <div v-if="pendingPayments.length > 0" class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200/60 rounded-2xl p-5 animate-fade-in">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center text-white text-lg shadow-sm">💳</div>
                            <div>
                                <p class="font-bold text-blue-900 text-sm">{{ pendingPayments.length }} Pembayaran Pending</p>
                                <p class="text-xs text-blue-600">Perlu verifikasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5">
                        <h4 class="font-bold text-gray-800 text-sm mb-4">Aktivitas Terbaru</h4>
                        <div class="space-y-4">
                            <div v-for="(order, i) in recentOrders.slice(0, 4)" :key="order.order_id" class="flex items-start gap-3">
                                <div class="relative flex flex-col items-center">
                                    <div :class="[
                                        'w-3 h-3 rounded-full ring-4 ring-white',
                                        order.status === 'pending' ? 'bg-amber-400' :
                                        order.status === 'completed' ? 'bg-emerald-400' :
                                        order.status === 'cancelled' ? 'bg-rose-400' : 'bg-blue-400'
                                    ]"></div>
                                    <div v-if="i < 3" class="w-px h-8 bg-gray-100 mt-1"></div>
                                </div>
                                <div class="flex-1 min-w-0 -mt-0.5">
                                    <p class="text-xs font-medium text-gray-800 truncate">{{ order.user?.name }}</p>
                                    <p class="text-[11px] text-gray-400">{{ formatTime(order.created_at) }} · {{ formatPrice(order.total_amount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100/80 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Pesanan Terbaru</h3>
                        <p class="text-sm text-gray-400 mt-0.5">10 pesanan terakhir yang masuk</p>
                    </div>
                    <Link href="/admin/orders" class="text-sm text-ck-primary hover:text-ck-primary-dark font-semibold flex items-center gap-1 px-4 py-2 rounded-xl hover:bg-ck-primary/5 transition-colors">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-slate-50 text-left text-gray-500">
                            <tr>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">No. Pesanan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Vendor</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in recentOrders" :key="order.order_id" class="hover:bg-gradient-to-r hover:from-ck-primary/[0.02] hover:to-transparent transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs px-2 py-1 bg-gray-50 rounded-md text-gray-600 group-hover:bg-ck-primary/5 group-hover:text-ck-primary transition-colors">{{ order.order_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ order.user?.name?.charAt(0) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ order.user?.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ order.vendor?.vendor_name }}</td>
                                <td class="px-6 py-4 text-gray-400 text-xs">{{ formatDate(order.created_at) }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColors[order.status]" class="px-2.5 py-1 rounded-lg text-xs font-bold">
                                        {{ statusLabels[order.status] }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="recentOrders.length === 0" class="p-12 text-center">
                    <div class="text-4xl mb-3">📦</div>
                    <p class="text-gray-500">Belum ada pesanan.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.6s ease-out both;
}
</style>
