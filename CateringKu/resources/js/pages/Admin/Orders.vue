<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps<{ orders: { data: any[], links: any[] }, filter: string | null }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/30',
    confirmed: 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/30',
    preparing: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800/30',
    delivering: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/30',
    completed: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30',
    cancelled: 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/30',
}
const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}
const statuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled']

function updateStatus(orderId: number, status: string) {
    router.patch(`/admin/orders/${orderId}/status`, { status }, { preserveScroll: true })
}

// Auto-fetch polling every 15 seconds
let pollInterval: ReturnType<typeof setInterval> | null = null

onMounted(() => {
    pollInterval = setInterval(() => {
        router.reload({ only: ['orders'] })
    }, 15000)
})

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval)
})
</script>

<template>
    <Head title="Pesanan - Admin CateringKu" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kelola Pesanan</h1>
                <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Auto-refresh aktif
                </span>
            </div>
        </template>

        <div class="max-w-6xl mx-auto">
            <!-- Filter -->
            <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
                <Link href="/admin/orders" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all" :class="!filter ? 'bg-gradient-to-r from-ck-primary to-ck-coral text-white shadow-md shadow-ck-primary/20' : 'bg-white dark:bg-[#1f2037] text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-[#2a2c45] hover:border-ck-primary/40 hover:text-ck-primary'">Semua</Link>
                <Link v-for="s in statuses" :key="s" :href="`/admin/orders?status=${s}`" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all" :class="filter === s ? 'bg-gradient-to-r from-ck-primary to-ck-coral text-white shadow-md shadow-ck-primary/20' : 'bg-white dark:bg-[#1f2037] text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-[#2a2c45] hover:border-ck-primary/40 hover:text-ck-primary'">{{ statusLabels[s] }}</Link>
            </div>

            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-[#161729] dark:to-[#1a1b30] text-left text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">No. Pesanan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Vendor</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-[#2a2c45]">
                            <tr v-for="order in orders.data" :key="order.order_id" class="hover:bg-gradient-to-r hover:from-ck-primary/[0.02] dark:hover:from-ck-primary/[0.05] hover:to-transparent transition-colors group">
                                <td class="px-6 py-4">
                                    <Link :href="`/admin/orders/${order.order_id}`" class="font-mono text-xs px-2 py-1 bg-gray-50 dark:bg-white/5 rounded-md text-gray-600 dark:text-gray-400 group-hover:bg-ck-primary/5 group-hover:text-ck-primary transition-colors hover:underline">{{ order.order_number }}</Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ order.user?.name?.charAt(0) }}
                                        </div>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ order.user?.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ order.vendor?.vendor_name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ formatDate(order.event_date) }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800 dark:text-gray-200">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColors[order.status]" class="px-2.5 py-1 rounded-lg text-xs font-bold">{{ statusLabels[order.status] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <select @change="(e: any) => updateStatus(order.order_id, e.target.value)" :value="order.status" class="text-xs border border-gray-200 dark:border-[#2a2c45] rounded-lg px-3 py-1.5 bg-white dark:bg-[#161729] text-gray-700 dark:text-gray-300 hover:border-ck-primary/40 focus:ring-2 focus:ring-ck-primary/20 focus:border-ck-primary transition-colors cursor-pointer">
                                            <option v-for="s in statuses" :key="s" :value="s">{{ statusLabels[s] }}</option>
                                        </select>
                                        <Link :href="`/admin/orders/${order.order_id}`" class="text-xs text-ck-primary hover:underline font-medium">Detail</Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="orders.data.length === 0" class="p-12 text-center">
                    <div class="text-4xl mb-3">📦</div>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada pesanan ditemukan.</p>
                </div>
            </div>

            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in orders.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm transition-colors" :class="link.active ? 'bg-ck-primary text-white shadow-sm' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' : 'text-gray-300 dark:text-gray-600'" v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
