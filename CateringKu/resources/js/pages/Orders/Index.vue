<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps<{ orders: { data: any[], links: any[] } }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400',
    confirmed: 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400',
    preparing: 'bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400',
    delivering: 'bg-indigo-100 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400',
    completed: 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400',
    cancelled: 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400',
}
const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}
</script>

<template>
    <CateringLayout>
        <Head title="Pesanan Saya — CateringKu" />
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-8">📦 Pesanan Saya</h1>

        <div v-if="orders.data.length === 0" class="text-center py-20 bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45]">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Belum Ada Pesanan</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Anda belum memiliki pesanan.</p>
            <Link href="/search" class="btn-primary">Cari Vendor</Link>
        </div>

        <div v-else class="space-y-4">
            <Link
                v-for="order in orders.data" :key="order.order_id"
                :href="`/orders/${order.order_id}`"
                class="block bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 hover:shadow-lg dark:hover:shadow-black/20 transition-shadow card-hover"
            >
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-1">{{ order.order_number }}</p>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">{{ order.vendor?.vendor_name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(order.event_date) }}</p>
                    </div>
                    <div class="text-right">
                        <span :class="statusColors[order.status]" class="px-3 py-1 rounded-full text-xs font-bold">{{ statusLabels[order.status] }}</span>
                        <p class="text-lg font-bold text-ck-primary mt-2">{{ formatPrice(order.total_amount) }}</p>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="orders.links && orders.links.length > 3" class="mt-8 flex justify-center gap-1">
            <Link v-for="link in orders.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm" :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' : 'text-gray-300 dark:text-gray-600'" v-html="link.label" />
        </div>
    </CateringLayout>
</template>
