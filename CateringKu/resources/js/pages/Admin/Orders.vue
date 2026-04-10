<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps<{ orders: { data: any[], links: any[] }, filter: string | null }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-amber-50 text-amber-700 border border-amber-200',
    confirmed: 'bg-sky-50 text-sky-700 border border-sky-200',
    preparing: 'bg-violet-50 text-violet-700 border border-violet-200',
    delivering: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
    completed: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    cancelled: 'bg-rose-50 text-rose-700 border border-rose-200',
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
    <AdminLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900">Kelola Pesanan</h1>
        </template>

        <div class="max-w-6xl mx-auto">
            <!-- Filter -->
            <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
                <Link href="/admin/orders" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all" :class="!filter ? 'bg-gradient-to-r from-ck-primary to-ck-coral text-white shadow-md shadow-ck-primary/20' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary/40 hover:text-ck-primary'">Semua</Link>
                <Link v-for="s in statuses" :key="s" :href="`/admin/orders?status=${s}`" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all" :class="filter === s ? 'bg-gradient-to-r from-ck-primary to-ck-coral text-white shadow-md shadow-ck-primary/20' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary/40 hover:text-ck-primary'">{{ statusLabels[s] }}</Link>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-slate-50 text-left text-gray-500">
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
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="order in orders.data" :key="order.order_id" class="hover:bg-gradient-to-r hover:from-ck-primary/[0.02] hover:to-transparent transition-colors group">
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
                                <td class="px-6 py-4 text-gray-500">{{ formatDate(order.event_date) }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColors[order.status]" class="px-2.5 py-1 rounded-lg text-xs font-bold">{{ statusLabels[order.status] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <select @change="(e: any) => updateStatus(order.order_id, e.target.value)" :value="order.status" class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 bg-white hover:border-ck-primary/40 focus:ring-2 focus:ring-ck-primary/20 focus:border-ck-primary transition-colors cursor-pointer">
                                        <option v-for="s in statuses" :key="s" :value="s">{{ statusLabels[s] }}</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="orders.data.length === 0" class="p-12 text-center">
                    <div class="text-4xl mb-3">📦</div>
                    <p class="text-gray-500">Tidak ada pesanan ditemukan.</p>
                </div>
            </div>

            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in orders.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm transition-colors" :class="link.active ? 'bg-ck-primary text-white shadow-sm' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300'" v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
