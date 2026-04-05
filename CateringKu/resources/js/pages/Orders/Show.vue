<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{ order: any, canReview: boolean }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }
function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700', confirmed: 'bg-blue-100 text-blue-700',
    preparing: 'bg-purple-100 text-purple-700', delivering: 'bg-indigo-100 text-indigo-700',
    completed: 'bg-green-100 text-green-700', cancelled: 'bg-red-100 text-red-700',
}
const statusLabels: Record<string, string> = {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi', preparing: 'Dipersiapkan',
    delivering: 'Dikirim', completed: 'Selesai', cancelled: 'Dibatalkan',
}

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
</script>

<template>
    <CateringLayout>
        <Head :title="`Pesanan ${order.order_number} — CateringKu`" />
        <Link href="/orders" class="text-sm text-gray-500 hover:text-ck-primary mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Pesanan
        </Link>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Info -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-400">{{ order.order_number }}</p>
                            <h1 class="text-2xl font-bold text-gray-800">{{ order.vendor?.vendor_name }}</h1>
                        </div>
                        <span :class="statusColors[order.status]" class="px-4 py-1.5 rounded-full text-sm font-bold">{{ statusLabels[order.status] }}</span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div><span class="text-gray-400">Tanggal Acara</span><p class="font-medium">{{ formatDate(order.event_date) }}</p></div>
                        <div><span class="text-gray-400">Waktu</span><p class="font-medium">{{ order.event_time }}</p></div>
                        <div><span class="text-gray-400">Jumlah Tamu</span><p class="font-medium">{{ order.num_people }} orang</p></div>
                        <div class="col-span-2 md:col-span-3"><span class="text-gray-400">Alamat Pengiriman</span><p class="font-medium">{{ order.delivery_address }}</p></div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Item Pesanan</h3>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in order.items" :key="item.order_item_id" class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ item.item_name }}</p>
                                <p class="text-sm text-gray-400">{{ item.quantity }}x {{ formatPrice(item.unit_price) }}</p>
                            </div>
                            <p class="font-semibold">{{ formatPrice(item.subtotal) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button v-if="order.status === 'pending'" @click="cancelOrder" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-semibold hover:bg-red-100 transition-colors">Batalkan Pesanan</button>
                    <button v-if="canReview" @click="showReviewForm = true" class="btn-primary">Beri Ulasan</button>
                </div>

                <!-- Review Form -->
                <div v-if="showReviewForm" class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Beri Ulasan</h3>
                    <form @submit.prevent="submitReview" class="space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="field in [{k:'rating',l:'Overall'},{k:'food_rating',l:'Makanan'},{k:'service_rating',l:'Pelayanan'},{k:'delivery_rating',l:'Pengiriman'}]" :key="field.k">
                                <label class="block text-sm text-gray-600 mb-1">{{ field.l }}</label>
                                <select v-model.number="(reviewForm as any)[field.k]" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">
                                    <option v-for="i in 5" :key="i" :value="i">{{ i }} ★</option>
                                </select>
                            </div>
                        </div>
                        <textarea v-model="reviewForm.review_text" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm" placeholder="Tulis ulasan Anda..."></textarea>
                        <div class="flex gap-3">
                            <button type="submit" :disabled="reviewForm.processing" class="btn-primary">Kirim Ulasan</button>
                            <button type="button" @click="showReviewForm = false" class="px-6 py-3 text-gray-500 hover:text-gray-700">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Summary -->
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ formatPrice(order.subtotal) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>PPN</span><span>{{ formatPrice(order.tax) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Pengiriman</span><span>{{ formatPrice(order.delivery_fee) }}</span></div>
                        <div v-if="order.discount > 0" class="flex justify-between text-green-600"><span>Diskon</span><span>-{{ formatPrice(order.discount) }}</span></div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between font-bold text-lg"><span>Total</span><span class="text-ck-primary">{{ formatPrice(order.total_amount) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
