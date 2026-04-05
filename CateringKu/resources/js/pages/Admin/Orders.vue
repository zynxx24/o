<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps<{ orders: { data: any[], links: any[] }, filter: string | null }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700', confirmed: 'bg-blue-100 text-blue-700',
    preparing: 'bg-purple-100 text-purple-700', delivering: 'bg-indigo-100 text-indigo-700',
    completed: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700',
}
const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}
const statuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled']

function updateStatus(orderId: number, status: string) {
    router.patch(`/admin/orders/${orderId}/status`, { status }, { preserveScroll: true })
}
</script>

<template>
    <Head title="Pesanan - Admin CateringKu" />
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 gap-4">
                <Link href="/admin" class="flex items-center gap-2.5 group"><img src="/images/logo.svg" class="h-9 w-9 rounded-xl shadow-sm" /><span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span></Link>
                <span class="px-2.5 py-1 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs rounded-lg font-bold shadow-sm shadow-red-200">ADMIN</span>
                <div class="ml-auto flex items-center gap-1">
                    <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                    <Link href="/admin" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Dashboard</Link>
                    <Link href="/admin/messages" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesan</Link>
                    <div class="w-px h-6 bg-gray-200 mx-2"></div>
                    <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Kelola Pesanan</h1>

            <!-- Filter -->
            <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                <Link href="/admin/orders" class="px-4 py-2 rounded-full text-sm font-medium" :class="!filter ? 'bg-ck-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary'">Semua</Link>
                <Link v-for="s in statuses" :key="s" :href="`/admin/orders?status=${s}`" class="px-4 py-2 rounded-full text-sm font-medium" :class="filter === s ? 'bg-ck-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary'">{{ statusLabels[s] }}</Link>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">No. Pesanan</th>
                                <th class="px-6 py-3 font-medium">Pelanggan</th>
                                <th class="px-6 py-3 font-medium">Vendor</th>
                                <th class="px-6 py-3 font-medium">Event</th>
                                <th class="px-6 py-3 font-medium">Total</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in orders.data" :key="order.order_id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono text-xs">{{ order.order_number }}</td>
                                <td class="px-6 py-4">{{ order.user?.name }}</td>
                                <td class="px-6 py-4">{{ order.vendor?.vendor_name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ formatDate(order.event_date) }}</td>
                                <td class="px-6 py-4 font-semibold">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-6 py-4"><span :class="statusColors[order.status]" class="px-2 py-1 rounded-full text-xs font-bold">{{ statusLabels[order.status] }}</span></td>
                                <td class="px-6 py-4">
                                    <select @change="(e: any) => updateStatus(order.order_id, e.target.value)" :value="order.status" class="text-xs border border-gray-200 rounded-lg px-2 py-1">
                                        <option v-for="s in statuses" :key="s" :value="s">{{ statusLabels[s] }}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in orders.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm" :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300'" v-html="link.label" />
            </div>
        </div>
    </div>
</template>
