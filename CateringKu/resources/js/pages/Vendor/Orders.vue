<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import VendorLayout from '@/layouts/VendorLayout.vue'

const props = defineProps<{ orders: { data: any[], links: any[] }, filter: string | null }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' }) }

const statusLabels: Record<string, string> = { pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan', delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan' }
const statusColors: Record<string, string> = {
    pending: 'bg-amber-50 text-amber-700 border border-amber-200',
    confirmed: 'bg-sky-50 text-sky-700 border border-sky-200',
    preparing: 'bg-violet-50 text-violet-700 border border-violet-200',
    delivering: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
    completed: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    cancelled: 'bg-rose-50 text-rose-700 border border-rose-200',
}
const vendorStatuses = ['confirmed', 'preparing', 'delivering', 'completed']

function updateStatus(orderId: number, status: string) {
    router.patch(`/vendor-panel/orders/${orderId}/status`, { status }, { preserveScroll: true })
}
</script>

<template>
    <Head title="Pesanan - Vendor CateringKu" />
    <VendorLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900">Kelola Pesanan</h1>
        </template>

        <div class="max-w-6xl mx-auto">
            <!-- Summary cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 p-4">
                    <p class="text-xs text-gray-400 font-medium">Total</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ orders.data.length }}</p>
                </div>
                <div class="bg-white rounded-xl border border-amber-100 p-4">
                    <p class="text-xs text-amber-600 font-medium">Menunggu</p>
                    <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ orders.data.filter(o => o.status === 'pending').length }}</p>
                </div>
                <div class="bg-white rounded-xl border border-blue-100 p-4">
                    <p class="text-xs text-blue-600 font-medium">Diproses</p>
                    <p class="text-2xl font-extrabold text-blue-700 mt-1">{{ orders.data.filter(o => ['confirmed','preparing','delivering'].includes(o.status)).length }}</p>
                </div>
                <div class="bg-white rounded-xl border border-emerald-100 p-4">
                    <p class="text-xs text-emerald-600 font-medium">Selesai</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ orders.data.filter(o => o.status === 'completed').length }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100/80 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100/80">
                    <h2 class="font-bold text-gray-900">Daftar Pesanan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-slate-50 text-left text-gray-500">
                            <tr>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">No. Pesanan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider">Tanggal Event</th>
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
                                        <div>
                                            <span class="font-medium text-gray-800 block">{{ order.user?.name }}</span>
                                            <span class="text-xs text-gray-400">{{ order.user?.phone }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ formatDate(order.event_date) }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ formatPrice(order.total_amount) }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColors[order.status]" class="px-2.5 py-1 rounded-lg text-xs font-bold">{{ statusLabels[order.status] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <select v-if="order.status !== 'cancelled' && order.status !== 'completed'" @change="(e: any) => updateStatus(order.order_id, e.target.value)" :value="order.status" class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 bg-white hover:border-ck-primary/40 focus:ring-2 focus:ring-ck-primary/20 focus:border-ck-primary transition-colors cursor-pointer">
                                        <option v-for="s in vendorStatuses" :key="s" :value="s">{{ statusLabels[s] }}</option>
                                    </select>
                                    <span v-else class="text-xs text-gray-400 italic">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="orders.data.length === 0" class="p-12 text-center">
                    <div class="text-4xl mb-3">📦</div>
                    <p class="text-gray-500">Belum ada pesanan.</p>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="orders.links && orders.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in orders.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm" :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300'" v-html="link.label" />
            </div>
        </div>
    </VendorLayout>
</template>
