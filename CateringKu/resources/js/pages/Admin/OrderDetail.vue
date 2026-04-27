<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps<{ order: any }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }
function formatDateTime(d: string) { return new Date(d).toLocaleString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }

const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}
const statusColors: Record<string, string> = {
    pending: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/30',
    confirmed: 'bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/30',
    preparing: 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-800/30',
    delivering: 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/30',
    completed: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30',
    cancelled: 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/30',
}
const allStatuses = ['pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled']

const paymentMethodLabels: Record<string, string> = {
    bank_transfer: 'Transfer Bank', 'e-wallet': 'E-Wallet', qris: 'QRIS',
    virtual_account: 'Virtual Account', transfer: 'Transfer Bank',
    credit_card: 'Kartu Kredit', cod: 'Bayar di Tempat', cash: 'Tunai',
}

const payment = computed(() => props.order.payments?.[0])

function updateStatus(status: string) {
    router.patch(`/admin/orders/${props.order.order_id}/status`, { status }, { preserveScroll: true })
}

function verifyPayment() {
    if (payment.value && confirm('Verifikasi pembayaran ini?')) {
        router.patch(`/admin/payments/${payment.value.payment_id}/verify`, {}, { preserveScroll: true })
    }
}

function printReceipt() {
    window.open(`/orders/${props.order.order_id}/receipt`, '_blank')
}
</script>

<template>
    <Head :title="`Pesanan ${order.order_number} - Admin CateringKu`" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link href="/admin/orders" class="text-gray-400 dark:text-gray-500 hover:text-ck-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Pesanan</h1>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header Card -->
            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="font-mono text-xs px-2 py-1 bg-gray-50 dark:bg-white/5 rounded-md text-gray-500 dark:text-gray-400">{{ order.order_number }}</span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Dibuat: {{ formatDateTime(order.created_at) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span :class="statusColors[order.status]" class="px-3 py-1.5 rounded-lg text-xs font-bold">{{ statusLabels[order.status] }}</span>
                        <select @change="(e: any) => updateStatus(e.target.value)" :value="order.status" class="text-xs border border-gray-200 dark:border-[#2a2c45] rounded-lg px-3 py-2 bg-white dark:bg-[#161729] text-gray-700 dark:text-gray-300">
                            <option v-for="s in allStatuses" :key="s" :value="s">{{ statusLabels[s] }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer & Vendor -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-5 shadow-sm">
                            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Pelanggan</h3>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ order.user?.name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ order.user?.email }}</p>
                            <p v-if="order.user?.phone" class="text-sm text-gray-500 dark:text-gray-400">{{ order.user?.phone }}</p>
                        </div>
                        <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-5 shadow-sm">
                            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Vendor</h3>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ order.vendor?.vendor_name }}</p>
                            <p v-if="order.vendor?.phone" class="text-sm text-gray-500 dark:text-gray-400">{{ order.vendor?.phone }}</p>
                            <p v-if="order.vendor?.city" class="text-sm text-gray-500 dark:text-gray-400">{{ order.vendor?.city }}</p>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-sm">📅</span>
                            Detail Acara
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Tipe Acara</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ order.event_type || '-' }}</p></div>
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Tanggal</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ formatDate(order.event_date) }}</p></div>
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Waktu</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ order.event_time }}</p></div>
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Jumlah Tamu</span><p class="font-medium text-ck-primary font-bold">{{ order.num_people }} orang</p></div>
                            <div class="col-span-2 sm:col-span-4"><span class="text-gray-400 dark:text-gray-500 text-xs">Alamat Pengiriman</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ order.delivery_address }}{{ order.delivery_city ? ', ' + order.delivery_city : '' }}</p></div>
                            <div v-if="order.special_request" class="col-span-2 sm:col-span-4"><span class="text-gray-400 dark:text-gray-500 text-xs">Catatan Khusus</span><p class="font-medium text-gray-800 dark:text-gray-200 italic">{{ order.special_request }}</p></div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center text-sm">🍽️</span>
                            Item Pesanan
                        </h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-[#2a2c45]">
                                    <th class="text-left py-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Item</th>
                                    <th class="text-center py-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Qty</th>
                                    <th class="text-right py-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Harga</th>
                                    <th class="text-right py-2 font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-[#2a2c45]">
                                <tr v-for="item in order.items" :key="item.order_item_id">
                                    <td class="py-3"><p class="font-medium text-gray-800 dark:text-gray-200">{{ item.item_name }}</p><p v-if="item.notes" class="text-xs text-gray-400 dark:text-gray-500">{{ item.notes }}</p></td>
                                    <td class="py-3 text-center font-mono text-gray-700 dark:text-gray-300">{{ item.quantity }}x</td>
                                    <td class="py-3 text-right text-gray-600 dark:text-gray-400">{{ formatPrice(item.unit_price) }}</td>
                                    <td class="py-3 text-right font-semibold text-gray-800 dark:text-gray-200">{{ formatPrice(item.subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Payment Summary -->
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm">Ringkasan Pembayaran</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Subtotal</span><span>{{ formatPrice(order.subtotal) }}</span></div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>PPN</span><span>{{ formatPrice(order.tax) }}</span></div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Pengiriman</span><span>{{ formatPrice(order.delivery_fee) }}</span></div>
                            <div v-if="Number(order.discount) > 0" class="flex justify-between text-green-600 dark:text-green-400"><span>Diskon</span><span>-{{ formatPrice(order.discount) }}</span></div>
                            <hr class="border-gray-100 dark:border-[#2a2c45]">
                            <div class="flex justify-between font-bold text-base"><span class="text-gray-800 dark:text-gray-200">Total</span><span class="text-ck-primary">{{ formatPrice(order.total_amount) }}</span></div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm">Info Pembayaran</h3>
                        <div class="space-y-3 text-sm">
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Metode</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ paymentMethodLabels[payment?.payment_method] || payment?.payment_method || '-' }}</p></div>
                            <div><span class="text-gray-400 dark:text-gray-500 text-xs">Provider</span><p class="font-medium text-gray-800 dark:text-gray-200 uppercase">{{ payment?.payment_provider?.replace('_', ' ') || '-' }}</p></div>
                            <div>
                                <span class="text-gray-400 dark:text-gray-500 text-xs">Status Bayar</span>
                                <p>
                                    <span v-if="payment" class="inline-block px-2 py-0.5 rounded-full text-xs font-bold" :class="payment.payment_status === 'verified' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : payment.payment_status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700/30 text-gray-600 dark:text-gray-400'">
                                        {{ payment.payment_status === 'pending' ? 'Menunggu' : payment.payment_status === 'verified' ? 'Terverifikasi' : payment.payment_status }}
                                    </span>
                                </p>
                            </div>
                            <!-- Verify Payment Button -->
                            <button v-if="payment && payment.payment_status === 'pending'" @click="verifyPayment" class="w-full mt-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Verifikasi Pembayaran
                            </button>
                        </div>
                    </div>

                    <!-- Commission Breakdown -->
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm">Distribusi Dana</h3>
                        <div v-if="order.commission" class="space-y-2.5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>Gross
                                </span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ formatPrice(order.commission.gross_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                    🏛️ Pajak Negara (PPN)
                                </span>
                                <span class="font-medium text-red-500">{{ formatPrice(order.commission.tax_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                                    🚚 Ongkir
                                </span>
                                <span class="font-medium text-gray-500 dark:text-gray-400">{{ formatPrice(order.delivery_fee) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 dark:text-gray-500 text-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                                    🏢 Fee Admin/Platform
                                </span>
                                <span class="font-semibold text-blue-500">{{ formatPrice(order.commission.platform_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-100 dark:border-[#2a2c45] pt-2 mt-1">
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                                    🏪 Payout Vendor
                                </span>
                                <span class="font-bold text-green-500 text-base">{{ formatPrice(order.commission.vendor_amount) }}</span>
                            </div>
                            <div class="flex items-center justify-end mt-1">
                                <span class="text-xs px-2 py-0.5 rounded-full" :class="order.commission.status === 'distributed' ? 'bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'">
                                    {{ order.commission.status === 'distributed' ? '✅ Terdistribusi' : '⏳ Menunggu' }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                            <p>⏳ Belum ada distribusi</p>
                            <p class="text-xs mt-1">Verifikasi pembayaran untuk mendistribusikan dana</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <button @click="printReceipt" class="w-full px-4 py-3 bg-white dark:bg-[#1f2037] border border-gray-200 dark:border-[#2a2c45] rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:border-ck-primary/40 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Nota
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
