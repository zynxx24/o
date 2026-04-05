<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{ carts: any[], user: any }>()

const firstCart = computed(() => props.carts[0])

const form = useForm({
    vendor_id: firstCart.value?.vendor_id,
    event_date: '',
    event_time: '',
    event_type: '',
    delivery_address: props.user.address || '',
    delivery_city: '',
    num_people: 10,
    special_request: '',
    payment_method: 'transfer',
})

function subtotal() {
    let total = 0
    for (const cart of props.carts) {
        for (const item of cart.items) {
            if (item.menu_item) total += item.menu_item.price * item.quantity
            else if (item.package) total += item.package.price_per_person * item.quantity
        }
    }
    return total
}

const tax = computed(() => Math.round(subtotal() * 0.11))
const deliveryFee = 15000
const total = computed(() => subtotal() + tax.value + deliveryFee)

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }

function submit() {
    form.post('/checkout')
}

const paymentMethods = [
    { value: 'transfer', label: 'Transfer Bank', icon: '🏦' },
    { value: 'e-wallet', label: 'E-Wallet', icon: '📱' },
    { value: 'credit_card', label: 'Kartu Kredit', icon: '💳' },
    { value: 'cod', label: 'Bayar di Tempat', icon: '💵' },
]

const eventTypes = ['Pernikahan', 'Ulang Tahun', 'Rapat Kantor', 'Seminar', 'Arisan', 'Lainnya']
</script>

<template>
    <CateringLayout>
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📋 Checkout</h1>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Event Details -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">📅 Detail Acara</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Acara</label>
                            <select v-model="form.event_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm">
                                <option value="">Pilih tipe acara</option>
                                <option v-for="t in eventTypes" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Tamu</label>
                            <input v-model.number="form.num_people" type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Acara *</label>
                            <input v-model="form.event_date" type="date" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" />
                            <p v-if="form.errors.event_date" class="text-red-500 text-xs mt-1">{{ form.errors.event_date }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Acara *</label>
                            <input v-model="form.event_time" type="time" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" />
                        </div>
                    </div>
                </div>

                <!-- Delivery -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">📍 Alamat Pengiriman</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap *</label>
                            <textarea v-model="form.delivery_address" rows="3" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" placeholder="Jl. ... No. ..., RT/RW, Kelurahan, Kecamatan"></textarea>
                            <p v-if="form.errors.delivery_address" class="text-red-500 text-xs mt-1">{{ form.errors.delivery_address }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kota</label>
                            <input v-model="form.delivery_city" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Khusus</label>
                            <textarea v-model="form.special_request" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" placeholder="Mis: alergi, pantangan, dekorasi khusus..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">💳 Metode Pembayaran</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button
                            v-for="pm in paymentMethods" :key="pm.value"
                            type="button"
                            @click="form.payment_method = pm.value"
                            class="p-4 rounded-xl border-2 text-center transition-all text-sm"
                            :class="form.payment_method === pm.value ? 'border-ck-primary bg-ck-primary/5 text-ck-primary' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        >
                            <div class="text-2xl mb-1">{{ pm.icon }}</div>
                            <div class="font-medium">{{ pm.label }}</div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ formatPrice(subtotal()) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>PPN (11%)</span><span>{{ formatPrice(tax) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Biaya Pengiriman</span><span>{{ formatPrice(deliveryFee) }}</span></div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between font-bold text-lg text-gray-900"><span>Total</span><span class="text-ck-primary">{{ formatPrice(total) }}</span></div>
                    </div>
                    <button type="submit" :disabled="form.processing" class="btn-primary w-full mt-6 text-center disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Memproses...' : 'Buat Pesanan' }}
                    </button>
                </div>
            </div>
        </form>
    </CateringLayout>
</template>
