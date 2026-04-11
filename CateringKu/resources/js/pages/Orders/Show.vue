<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps<{ order: any, canReview: boolean }>()

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

const paymentMethodLabels: Record<string, string> = {
    bank_transfer: 'Transfer Bank', 'e-wallet': 'E-Wallet', qris: 'QRIS',
    virtual_account: 'Virtual Account', transfer: 'Transfer Bank',
    credit_card: 'Kartu Kredit', cod: 'Bayar di Tempat', cash: 'Tunai',
}

const payment = computed(() => props.order.payments?.[0])

const showReviewForm = ref(false)
const reviewForm = useForm({
    rating: 5, food_rating: 5, service_rating: 5, delivery_rating: 5, review_text: '',
})

function submitReview() {
    reviewForm.post(`/orders/${props.order.order_id}/review`, { preserveScroll: true, onSuccess: () => { showReviewForm.value = false } })
}

function cancelOrder() {
    if (confirm('Yakin ingin membatalkan pesanan ini?')) {
        router.post(`/orders/${props.order.order_id}/cancel`)
    }
}

function printReceipt() {
    window.open(`/orders/${props.order.order_id}/receipt`, '_blank')
}
</script>

<template>
    <CateringLayout>
        <Head :title="`Pesanan ${order.order_number} — CateringKu`" />
        <Link href="/orders" class="text-sm text-gray-500 dark:text-gray-400 hover:text-ck-primary mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Pesanan
        </Link>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Info -->
                <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ order.order_number }}</p>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ order.vendor?.vendor_name }}</h1>
                        </div>
                        <span :class="statusColors[order.status]" class="px-4 py-1.5 rounded-full text-sm font-bold">{{ statusLabels[order.status] }}</span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="text-gray-400 dark:text-gray-500">Tanggal Acara</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ formatDate(order.event_date) }}</p></div>
                        <div><span class="text-gray-400 dark:text-gray-500">Waktu</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ order.event_time }}</p></div>
                        <div><span class="text-gray-400 dark:text-gray-500">Jumlah Tamu</span><p class="font-medium text-ck-primary font-bold">{{ order.num_people }} orang</p></div>
                        <div class="col-span-2 md:col-span-3"><span class="text-gray-400 dark:text-gray-500">Alamat Pengiriman</span><p class="font-medium text-gray-800 dark:text-gray-200">{{ order.delivery_address }}</p></div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Item Pesanan</h3>
                    <div class="divide-y divide-gray-50 dark:divide-[#2a2c45]">
                        <div v-for="item in order.items" :key="item.order_item_id" class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-200">{{ item.item_name }}</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500">{{ item.quantity }}x @ {{ formatPrice(item.unit_price) }}</p>
                            </div>
                            <p class="font-semibold text-gray-800 dark:text-gray-200">{{ formatPrice(item.subtotal) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-3">
                    <Link :href="`/orders/${order.order_id}/receipt`" class="bg-ck-primary/10 dark:bg-ck-primary/15 text-ck-primary px-6 py-3 rounded-xl font-semibold hover:bg-ck-primary/20 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Lihat Nota
                    </Link>
                    <button @click="printReceipt" class="bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-white/10 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Cetak Nota
                    </button>
                    <button v-if="order.status === 'pending'" @click="cancelOrder" class="bg-red-50 dark:bg-red-900/15 text-red-600 dark:text-red-400 px-6 py-3 rounded-xl font-semibold hover:bg-red-100 dark:hover:bg-red-900/25 transition-colors">Batalkan Pesanan</button>
                    <button v-if="canReview" @click="showReviewForm = true" class="btn-primary">Beri Ulasan</button>
                </div>

                <!-- Review Form -->
                <div v-if="showReviewForm" class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Beri Ulasan</h3>
                    <form @submit.prevent="submitReview" class="space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="field in [{k:'rating',l:'Overall'},{k:'food_rating',l:'Makanan'},{k:'service_rating',l:'Pelayanan'},{k:'delivery_rating',l:'Pengiriman'}]" :key="field.k">
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">{{ field.l }}</label>
                                <select v-model.number="(reviewForm as any)[field.k]" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm">
                                    <option v-for="i in 5" :key="i" :value="i">{{ i }} ★</option>
                                </select>
                            </div>
                        </div>
                        <textarea v-model="reviewForm.review_text" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm" placeholder="Tulis ulasan Anda..."></textarea>
                        <div class="flex gap-3">
                            <button type="submit" :disabled="reviewForm.processing" class="btn-primary">Kirim Ulasan</button>
                            <button type="button" @click="showReviewForm = false" class="px-6 py-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100 dark:border-[#2a2c45] p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Ringkasan Pembayaran</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Subtotal</span><span>{{ formatPrice(order.subtotal) }}</span></div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>PPN</span><span>{{ formatPrice(order.tax) }}</span></div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400"><span>Pengiriman</span><span>{{ formatPrice(order.delivery_fee) }}</span></div>
                        <div v-if="order.discount > 0" class="flex justify-between text-green-600 dark:text-green-400"><span>Diskon</span><span>-{{ formatPrice(order.discount) }}</span></div>
                        <hr class="border-gray-100 dark:border-[#2a2c45]">
                        <div class="flex justify-between font-bold text-lg"><span class="text-gray-800 dark:text-gray-200">Total</span><span class="text-ck-primary">{{ formatPrice(order.total_amount) }}</span></div>
                    </div>

                    <!-- Payment Info -->
                    <div v-if="payment" class="mt-5 pt-5 border-t border-gray-100 dark:border-[#2a2c45] space-y-2 text-sm">
                        <h4 class="font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wider mb-3">Info Pembayaran</h4>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Metode</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ paymentMethodLabels[payment.payment_method] || payment.payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Provider</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 uppercase">{{ payment.payment_provider?.replace('_', ' ') || '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold" :class="payment.payment_status === 'verified' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : payment.payment_status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700/30 text-gray-600 dark:text-gray-400'">
                                {{ payment.payment_status === 'pending' ? 'Menunggu' : payment.payment_status === 'verified' ? 'Terverifikasi' : payment.payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
