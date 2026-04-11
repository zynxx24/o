<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'
import VendorLayout from '@/layouts/VendorLayout.vue'

const props = defineProps<{ vendor: any, stats: any, recentOrders: any[] }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }) }

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

const animatedStats = ref({ totalOrders: 0, totalRevenue: 0, pendingOrders: 0, totalMenuItems: 0 })

function animateNumber(key: string, target: number, duration = 1200) {
    const startTime = performance.now()
    function step(currentTime: number) {
        const elapsed = currentTime - startTime
        const progress = Math.min(elapsed / duration, 1)
        const eased = 1 - Math.pow(1 - progress, 3)
        ;(animatedStats.value as any)[key] = Math.floor(target * eased)
        if (progress < 1) requestAnimationFrame(step)
    }
    requestAnimationFrame(step)
}

onMounted(() => {
    animateNumber('totalOrders', props.stats.totalOrders)
    animateNumber('totalRevenue', props.stats.totalRevenue, 1500)
    animateNumber('pendingOrders', props.stats.pendingOrders, 800)
    animateNumber('totalMenuItems', props.stats.totalMenuItems, 800)
})

// Auto-fetch polling every 30 seconds
let pollInterval: ReturnType<typeof setInterval> | null = null
onMounted(() => {
    pollInterval = setInterval(() => {
        router.reload({ only: ['stats', 'recentOrders'] })
    }, 30000)
})
onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval)
})

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

const statCards = [
    { key: 'totalOrders', label: 'Total Pesanan', icon: '📦', gradient: 'from-blue-500 via-blue-600 to-indigo-700', ring: 'ring-blue-100', iconBg: 'bg-blue-500/10' },
    { key: 'pendingOrders', label: 'Menunggu', icon: '⏳', gradient: 'from-amber-500 via-orange-500 to-red-500', ring: 'ring-amber-100', iconBg: 'bg-amber-500/10' },
    { key: 'totalRevenue', label: 'Pendapatan', icon: '💰', gradient: 'from-emerald-500 via-green-600 to-teal-700', ring: 'ring-emerald-100', iconBg: 'bg-emerald-500/10', isCurrency: true },
    { key: 'totalMenuItems', label: 'Menu Aktif', icon: '🍽️', gradient: 'from-violet-500 via-purple-600 to-pink-600', ring: 'ring-violet-100', iconBg: 'bg-violet-500/10' },
]

const quickActions = [
    { label: 'Tambah Menu', icon: '➕', href: '/vendor-panel/menu', color: 'from-emerald-400 to-teal-500' },
    { label: 'Lihat Pesanan', icon: '📋', href: '/vendor-panel/orders', color: 'from-blue-400 to-indigo-500' },
    { label: 'Respon Ulasan', icon: '💬', href: '/vendor-panel/reviews', color: 'from-amber-400 to-orange-500' },
    { label: 'Lihat Website', icon: '🌐', href: '/', color: 'from-purple-400 to-pink-500' },
]
</script>

<template>
    <Head title="Vendor Dashboard - CateringKu" />
    <VendorLayout>
        <template #header>
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
                <p class="text-xs text-gray-400 dark:text-gray-500 hidden sm:block">{{ today }}</p>
            </div>
        </template>

        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Welcome Banner -->
            <div class="relative overflow-hidden bg-gradient-to-r from-ck-primary via-orange-500 to-amber-500 rounded-2xl p-6 md:p-8 shadow-xl shadow-orange-200/30 dark:shadow-orange-900/20 animate-fade-in">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 blur-2xl"></div>
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                                {{ greeting }}, {{ vendor.vendor_name }}! 👋
                            </h1>
                            <p class="text-orange-100 mt-1.5 text-sm">Kelola pesanan dan menu katering Anda dari sini.</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white/15 backdrop-blur-sm rounded-2xl px-5 py-3 border border-white/20">
                            <div class="text-2xl">⭐</div>
                            <div>
                                <div class="flex gap-0.5 mb-0.5">
                                    <span v-for="i in 5" :key="i" class="text-sm" :class="i <= Math.round(Number(stats.avgRating)) ? 'text-yellow-300' : 'text-white/30'">★</span>
                                </div>
                                <p class="text-white font-bold text-lg leading-tight">{{ Number(stats.avgRating).toFixed(1) }}</p>
                                <p class="text-orange-100 text-xs">Rating Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(card, index) in statCards" :key="card.key"
                     class="group relative bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-5 hover:shadow-xl hover:shadow-gray-100/50 dark:hover:shadow-black/20 hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-start justify-between mb-3">
                        <div :class="`w-11 h-11 bg-gradient-to-br ${card.gradient} rounded-xl flex items-center justify-center text-white text-lg shadow-lg group-hover:scale-110 transition-transform duration-300`">
                            {{ card.icon }}
                        </div>
                        <div :class="`w-2 h-2 rounded-full bg-gradient-to-r ${card.gradient} animate-pulse`"></div>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
                        {{ card.isCurrency ? formatPrice(animatedStats[card.key as keyof typeof animatedStats]) : animatedStats[card.key as keyof typeof animatedStats] }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">{{ card.label }}</p>
                    <div :class="`absolute inset-0 rounded-2xl ring-2 ${card.ring} opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none`"></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <Link v-for="action in quickActions" :key="action.label" :href="action.href"
                      class="group flex items-center gap-3 bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-4 hover:shadow-lg hover:shadow-gray-100/50 dark:hover:shadow-black/20 hover:-translate-y-0.5 transition-all duration-200">
                    <div :class="`w-10 h-10 bg-gradient-to-br ${action.color} rounded-xl flex items-center justify-center text-white text-lg shadow-sm group-hover:scale-110 transition-transform`">
                        {{ action.icon }}
                    </div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ action.label }}</p>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-ck-primary ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Revenue Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg">Pendapatan Mingguan</h3>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Berdasarkan pesanan terbaru</p>
                        </div>
                        <div class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                            {{ formatPrice(stats.totalRevenue) }}
                        </div>
                    </div>
                    <div class="flex items-end justify-between gap-3 h-40 px-2">
                        <div v-for="bar in chartBars" :key="bar.label" class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full relative flex items-end justify-center" style="height: 128px">
                                <div :class="[
                                    'w-full max-w-[44px] rounded-xl transition-all duration-700 ease-out',
                                    bar.isToday 
                                        ? 'bg-gradient-to-t from-ck-primary to-orange-400 shadow-lg shadow-orange-200/50'
                                        : 'bg-gradient-to-t from-gray-200 to-gray-100 dark:from-gray-700 dark:to-gray-600 hover:from-ck-primary/60 hover:to-orange-300/60'
                                ]"
                                :style="{ height: `${bar.height}%` }">
                                </div>
                            </div>
                            <span :class="['text-xs font-semibold', bar.isToday ? 'text-ck-primary' : 'text-gray-400']">{{ bar.label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders List -->
                <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100/80 dark:border-[#2a2c45] flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-gray-100">Pesanan Terbaru</h3>
                        <Link href="/vendor-panel/orders" class="text-xs text-ck-primary font-semibold hover:text-ck-primary-dark transition-colors">Semua →</Link>
                    </div>
                    <div v-if="recentOrders.length === 0" class="p-8 text-center text-gray-400 text-sm">Belum ada pesanan.</div>
                    <div v-else class="divide-y divide-gray-50">
                        <div v-for="order in recentOrders" :key="order.order_id" class="px-5 py-4 hover:bg-gradient-to-r hover:from-ck-primary/[0.02] dark:hover:from-ck-primary/[0.05] hover:to-transparent transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                        {{ order.user?.name?.charAt(0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ order.user?.name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ formatDate(order.event_date) }} · {{ order.num_people }} pax</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-3">
                                    <span :class="statusColors[order.status]" class="px-2 py-0.5 rounded-md text-[10px] font-bold">{{ statusLabels[order.status] }}</span>
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">{{ formatPrice(order.total_amount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
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
