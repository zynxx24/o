<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: null as any })

const props = defineProps<{ order: any }>()

function formatPrice(p: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p)
}
function formatDate(d: string) {
    return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}
function formatDateTime(d: string) {
    return new Date(d).toLocaleString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const payment = computed(() => props.order.payments?.[0])

const paymentMethodLabel: Record<string, string> = {
    bank_transfer: 'Transfer Bank',
    'e-wallet': 'E-Wallet',
    qris: 'QRIS',
    virtual_account: 'Virtual Account',
    transfer: 'Transfer Bank',
    credit_card: 'Kartu Kredit',
    cod: 'Bayar di Tempat',
    cash: 'Tunai',
}

const paymentStatusColors: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700',
    verified: 'bg-green-100 text-green-700',
    failed: 'bg-red-100 text-red-700',
    refunded: 'bg-gray-100 text-gray-600',
}

const statusLabels: Record<string, string> = {
    pending: 'Menunggu Konfirmasi',
    confirmed: 'Dikonfirmasi',
    preparing: 'Sedang Disiapkan',
    delivering: 'Dalam Pengiriman',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
}

function printReceipt() {
    window.print()
}
</script>

<template>
    <Head :title="`Nota #${order.order_number} — CateringKu`" />

    <!-- Print-optimized layout: no app layout wrapper -->
    <div class="min-h-screen bg-gray-50 print:bg-white">
        <!-- Top bar (hidden on print) -->
        <div class="print:hidden bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
                <Link href="/orders" class="text-sm text-gray-500 hover:text-ck-primary flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Kembali ke Pesanan
                </Link>
                <div class="flex gap-2">
                    <Link :href="`/orders/${order.order_id}`" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                        Detail Pesanan
                    </Link>
                    <button @click="printReceipt" class="px-4 py-2 text-sm text-white bg-ck-primary hover:bg-ck-primary-dark rounded-lg transition-colors font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Nota
                    </button>
                </div>
            </div>
        </div>

        <!-- Receipt Card -->
        <div class="max-w-3xl mx-auto px-4 py-8 print:py-0 print:px-0">
            <div class="bg-white rounded-2xl print:rounded-none border border-gray-100 print:border-none overflow-hidden shadow-sm print:shadow-none">

                <!-- Header -->
                <div class="bg-gradient-to-r from-ck-primary to-ck-coral p-8 print:p-6 text-white print:text-black print:bg-none print:border-b-2 print:border-gray-900">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold print:text-black">🍽️ CateringKu</h1>
                            <p class="text-white/80 print:text-gray-600 text-sm mt-1">Platform Catering Terpercaya</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-white/70 print:text-gray-500 uppercase tracking-wider font-semibold">Nota Digital</p>
                            <p class="text-lg font-bold font-mono mt-1 print:text-black">{{ order.order_number }}</p>
                            <p class="text-xs text-white/70 print:text-gray-500 mt-1">{{ formatDateTime(order.created_at) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 print:p-6 space-y-6">

                    <!-- Customer & Vendor Info -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pelanggan</h3>
                            <p class="font-semibold text-gray-800">{{ order.user?.name }}</p>
                            <p class="text-sm text-gray-500">{{ order.user?.email }}</p>
                            <p v-if="order.user?.phone" class="text-sm text-gray-500">{{ order.user?.phone }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Vendor</h3>
                            <p class="font-semibold text-gray-800">{{ order.vendor?.vendor_name }}</p>
                            <p v-if="order.vendor?.phone" class="text-sm text-gray-500">{{ order.vendor?.phone }}</p>
                            <p v-if="order.vendor?.city" class="text-sm text-gray-500">{{ order.vendor?.city }}</p>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="bg-gray-50 print:bg-transparent print:border print:border-gray-300 rounded-xl p-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Detail Acara</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-400 text-xs">Tipe Acara</span>
                                <p class="font-medium text-gray-800">{{ order.event_type || '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Tanggal</span>
                                <p class="font-medium text-gray-800">{{ formatDate(order.event_date) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Waktu</span>
                                <p class="font-medium text-gray-800">{{ order.event_time }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Jumlah Tamu</span>
                                <p class="font-medium text-gray-800">{{ order.num_people }} orang</p>
                            </div>
                        </div>
                        <div class="mt-3 text-sm">
                            <span class="text-gray-400 text-xs">Alamat Pengiriman</span>
                            <p class="font-medium text-gray-800">{{ order.delivery_address }}</p>
                        </div>
                        <div v-if="order.special_request" class="mt-3 text-sm">
                            <span class="text-gray-400 text-xs">Catatan Khusus</span>
                            <p class="font-medium text-gray-800">{{ order.special_request }}</p>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Rincian Pesanan</h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="text-left py-2.5 font-semibold text-gray-600">Item</th>
                                    <th class="text-center py-2.5 font-semibold text-gray-600">Qty</th>
                                    <th class="text-right py-2.5 font-semibold text-gray-600">Harga Satuan</th>
                                    <th class="text-right py-2.5 font-semibold text-gray-600">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in order.items" :key="item.order_item_id" class="group">
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-gray-800">{{ item.item_name }}</p>
                                        <p v-if="item.notes" class="text-xs text-gray-400 mt-0.5">{{ item.notes }}</p>
                                    </td>
                                    <td class="py-3 text-center font-mono text-gray-700">{{ item.quantity }}x</td>
                                    <td class="py-3 text-right text-gray-600">{{ formatPrice(item.unit_price) }}</td>
                                    <td class="py-3 text-right font-semibold text-gray-800">{{ formatPrice(item.subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="border-t-2 border-gray-200 pt-4">
                        <div class="flex flex-col items-end space-y-1.5 text-sm">
                            <div class="flex justify-between w-64">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="text-gray-700 font-medium">{{ formatPrice(order.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between w-64">
                                <span class="text-gray-500">PPN</span>
                                <span class="text-gray-700 font-medium">{{ formatPrice(order.tax) }}</span>
                            </div>
                            <div class="flex justify-between w-64">
                                <span class="text-gray-500">Biaya Pengiriman</span>
                                <span class="text-gray-700 font-medium">{{ formatPrice(order.delivery_fee) }}</span>
                            </div>
                            <div v-if="Number(order.discount) > 0" class="flex justify-between w-64">
                                <span class="text-green-600">Diskon</span>
                                <span class="text-green-600 font-medium">-{{ formatPrice(order.discount) }}</span>
                            </div>
                            <div class="flex justify-between w-64 border-t border-gray-200 pt-2 mt-1">
                                <span class="text-gray-900 font-bold text-base">Total Pembayaran</span>
                                <span class="text-ck-primary font-bold text-lg print:text-black">{{ formatPrice(order.total_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-gray-50 print:bg-transparent print:border print:border-gray-300 rounded-xl p-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Pembayaran</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-400 text-xs">Metode</span>
                                <p class="font-medium text-gray-800">{{ paymentMethodLabel[payment?.payment_method] || payment?.payment_method || '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Provider</span>
                                <p class="font-medium text-gray-800 uppercase">{{ payment?.payment_provider?.replace('_', ' ') || '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Jumlah</span>
                                <p class="font-medium text-gray-800">{{ formatPrice(payment?.amount || 0) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs">Status</span>
                                <span v-if="payment" :class="paymentStatusColors[payment.payment_status]" class="inline-block px-2 py-0.5 rounded-full text-xs font-bold mt-0.5">
                                    {{ payment.payment_status === 'pending' ? 'Menunggu' : payment.payment_status === 'verified' ? 'Terverifikasi' : payment.payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center justify-between py-3 px-4 bg-ck-primary/5 print:bg-transparent print:border print:border-gray-300 rounded-xl">
                        <div class="text-sm">
                            <span class="text-gray-500">Status Pesanan:</span>
                            <span class="font-bold text-ck-primary print:text-black ml-2">{{ statusLabels[order.status] || order.status }}</span>
                        </div>
                    </div>

                    <!-- Distribusi Dana — muncul jika komisi sudah terdistribusi -->
                    <div v-if="order.commission" class="bg-gray-50 print:bg-transparent print:border print:border-gray-300 rounded-xl p-4">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">📊 Distribusi Dana</h3>
                        <p class="text-xs text-gray-400 mb-3">Dana otomatis terdistribusi setelah pembayaran terverifikasi</p>
                        <div class="space-y-2 text-sm">
                            <!-- Gross -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Total Pembayaran (Gross)</span>
                                <span class="font-semibold text-gray-700">{{ formatPrice(order.commission.gross_amount) }}</span>
                            </div>
                            <!-- Pajak Negara -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <span class="flex items-center gap-2 text-gray-500">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block flex-shrink-0"></span>
                                    <span>🏛️ Pajak Negara (PPN)</span>
                                </span>
                                <span class="font-semibold text-red-600">{{ formatPrice(order.commission.tax_amount) }}</span>
                            </div>
                            <!-- Ongkir -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <span class="flex items-center gap-2 text-gray-500">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-400 inline-block flex-shrink-0"></span>
                                    🚚 Biaya Pengiriman (Operasional)
                                </span>
                                <span class="font-semibold text-gray-600">{{ formatPrice(order.delivery_fee) }}</span>
                            </div>
                            <!-- Platform/Admin -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <span class="flex items-center gap-2 text-gray-500">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block flex-shrink-0"></span>
                                    <span>🏢 Fee Admin/Platform</span>
                                </span>
                                <span class="font-semibold text-blue-600">{{ formatPrice(order.commission.platform_amount) }}</span>
                            </div>
                            <!-- Vendor -->
                            <div class="flex justify-between items-center py-1.5">
                                <span class="flex items-center gap-2 text-gray-500">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 inline-block flex-shrink-0"></span>
                                    <span>🏪 Payout Vendor</span>
                                </span>
                                <span class="font-bold text-green-600 text-base">{{ formatPrice(order.commission.vendor_amount) }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-300 mt-3 text-center">✅ Dana telah didistribusikan secara otomatis</p>
                    </div>

                    <!-- Estimasi Distribusi Dana — muncul jika belum ada komisi (belum diverifikasi) -->
                    <div v-else class="bg-amber-50 print:bg-transparent print:border print:border-gray-300 rounded-xl p-4 border border-amber-100">
                        <h3 class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-1">📊 Estimasi Distribusi Dana</h3>
                        <p class="text-xs text-amber-400 mb-3">Distribusi aktual dilakukan setelah pembayaran diverifikasi admin</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center py-1 border-b border-amber-100">
                                <span class="text-gray-500 font-medium">Total Pembayaran</span>
                                <span class="font-semibold text-gray-700">{{ formatPrice(order.total_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-amber-100">
                                <span class="flex items-center gap-2 text-gray-400">
                                    <span class="w-2 h-2 rounded-full bg-red-300 inline-block flex-shrink-0"></span>
                                    🏛️ Pajak Negara (PPN)
                                </span>
                                <span class="text-red-500">{{ formatPrice(order.tax) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-amber-100">
                                <span class="flex items-center gap-2 text-gray-400">
                                    <span class="w-2 h-2 rounded-full bg-gray-300 inline-block flex-shrink-0"></span>
                                    🚚 Biaya Pengiriman
                                </span>
                                <span class="text-gray-500">{{ formatPrice(order.delivery_fee) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-amber-100">
                                <span class="flex items-center gap-2 text-gray-400">
                                    <span class="w-2 h-2 rounded-full bg-blue-300 inline-block flex-shrink-0"></span>
                                    🏢 Fee Admin/Platform
                                </span>
                                <span class="text-blue-500">{{ formatPrice(Math.round(order.subtotal * 0.1)) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="flex items-center gap-2 text-gray-400">
                                    <span class="w-2 h-2 rounded-full bg-green-300 inline-block flex-shrink-0"></span>
                                    🏪 Estimasi Payout Vendor
                                </span>
                                <span class="font-semibold text-green-500">{{ formatPrice(Math.round(order.subtotal * 0.9)) }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-amber-300 mt-3 text-center">⏳ Menunggu verifikasi pembayaran oleh admin</p>
                    </div>

                    <!-- Footer -->
                    <div class="text-center pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Terima kasih telah memesan melalui CateringKu</p>
                        <p class="text-xs text-gray-300 mt-1">Nota ini merupakan bukti transaksi yang sah</p>
                        <p class="text-xs text-gray-300 mt-0.5">Dicetak pada: {{ new Date().toLocaleString('id-ID') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        margin: 10mm 12mm;
        size: A4;
    }
    .print\:hidden {
        display: none !important;
    }
    /* Force all content into a single page */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    /* Compact spacing for print */
    .p-8 { padding: 16px !important; }
    .p-6 { padding: 12px !important; }
    .space-y-6 > * + * { margin-top: 12px !important; }
    .py-3 { padding-top: 6px !important; padding-bottom: 6px !important; }
    .py-2\.5 { padding-top: 5px !important; padding-bottom: 5px !important; }
    .py-1\.5 { padding-top: 3px !important; padding-bottom: 3px !important; }
    .mb-3 { margin-bottom: 8px !important; }
    .mt-3 { margin-top: 8px !important; }
    .gap-6 { gap: 12px !important; }
    .gap-3 { gap: 8px !important; }
    .pt-4 { padding-top: 10px !important; }
    /* Prevent page breaks inside receipt sections */
    .rounded-xl, table, .space-y-2 {
        break-inside: avoid;
    }
    /* Ensure receipt card takes full width */
    .max-w-3xl { max-width: 100% !important; }
    /* Proper text sizing */
    .text-2xl { font-size: 18px !important; }
    .text-lg { font-size: 15px !important; }
    .text-base { font-size: 13px !important; }
    .text-sm { font-size: 12px !important; }
    .text-xs { font-size: 10px !important; }
}
</style>
