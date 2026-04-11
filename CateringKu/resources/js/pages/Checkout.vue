<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const page = usePage()

const props = defineProps<{ carts: any[], user: any }>()

// --- Toast / popup state ---
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref<'error' | 'success' | 'warning'>('error')
let toastTimer: ReturnType<typeof setTimeout> | null = null

function toast(msg: string, type: 'error' | 'success' | 'warning' = 'error', duration = 6000) {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    if (toastTimer) clearTimeout(toastTimer)
    toastTimer = setTimeout(() => { showToast.value = false }, duration)
}

// Watch for flash messages from backend
watch(() => (page.props as any).flash, (flash: any) => {
    if (flash?.error) toast(flash.error, 'error')
    if (flash?.success) toast(flash.success, 'success')
}, { immediate: true, deep: true })

// --- Min date: tomorrow ---
const minDate = computed(() => {
    const d = new Date()
    d.setDate(d.getDate() + 1)
    return d.toISOString().split('T')[0]
})

const firstCart = computed(() => props.carts?.[0] ?? null)

const form = useForm({
    vendor_id: firstCart.value?.vendor_id ?? null,
    event_date: '',
    event_time: '',
    event_type: '',
    delivery_address: props.user?.address || '',
    delivery_city: '',
    num_people: 50,
    special_request: '',
    payment_method: '',
    payment_provider: '',
})

// Watch form errors from server validation (must be AFTER form declaration)
watch(() => form.errors, (errors: any) => {
    const firstError = Object.values(errors)[0] as string
    if (firstError) toast(firstError, 'error')
}, { deep: true })

// --- Payment Method Definitions ---
const paymentCategories = [
    {
        method: 'bank_transfer',
        label: 'Transfer Bank',
        icon: '🏦',
        description: 'Transfer manual ke rekening bank',
        providers: [
            { id: 'bca', name: 'BCA', color: '#003399' },
            { id: 'bni', name: 'BNI', color: '#F05A22' },
            { id: 'bri', name: 'BRI', color: '#00529C' },
            { id: 'mandiri', name: 'Mandiri', color: '#003876' },
        ]
    },
    {
        method: 'e-wallet',
        label: 'E-Wallet',
        icon: '📱',
        description: 'Bayar lewat dompet digital',
        providers: [
            { id: 'dana', name: 'DANA', color: '#108EE9' },
            { id: 'gopay', name: 'GoPay', color: '#00AED6' },
            { id: 'ovo', name: 'OVO', color: '#4C2A86' },
            { id: 'shopeepay', name: 'ShopeePay', color: '#EE4D2D' },
        ]
    },
    {
        method: 'qris',
        label: 'QRIS',
        icon: '📲',
        description: 'Scan QR untuk pembayaran instan',
        providers: [
            { id: 'qris', name: 'QRIS', color: '#E31937' },
        ]
    },
    {
        method: 'virtual_account',
        label: 'Virtual Account',
        icon: '🏧',
        description: 'Bayar via nomor virtual account',
        providers: [
            { id: 'bca_va', name: 'BCA VA', color: '#003399' },
            { id: 'bni_va', name: 'BNI VA', color: '#F05A22' },
            { id: 'bri_va', name: 'BRI VA', color: '#00529C' },
            { id: 'mandiri_va', name: 'Mandiri VA', color: '#003876' },
        ]
    },
]

const selectedCategory = ref<string>('')

function selectPaymentCategory(method: string) {
    selectedCategory.value = method
    form.payment_method = method
    // Auto-select first provider if only one
    const cat = paymentCategories.find(c => c.method === method)
    if (cat && cat.providers.length === 1) {
        form.payment_provider = cat.providers[0].id
    } else {
        form.payment_provider = ''
    }
}

function selectProvider(providerId: string) {
    form.payment_provider = providerId
}

const activeProviders = computed(() => {
    const cat = paymentCategories.find(c => c.method === selectedCategory.value)
    return cat ? cat.providers : []
})

// --- Quantity Calculation Algorithm ---
// Each cart item quantity is multiplied by num_people
function getAdjustedItems() {
    const items: any[] = []
    for (const cart of props.carts) {
        for (const item of cart.items) {
            const unitPrice = item.menu_item ? item.menu_item.price : (item.package ? item.package.price_per_person : 0)
            const itemName = item.menu_item?.item_name || item.package?.package_name || 'Item'
            const baseQty = item.quantity
            const adjustedQty = baseQty * form.num_people
            items.push({
                name: itemName,
                baseQty,
                adjustedQty,
                unitPrice,
                subtotal: unitPrice * adjustedQty,
                unit: item.menu_item?.unit || 'porsi',
            })
        }
    }
    return items
}

const adjustedItems = computed(() => getAdjustedItems())

const subtotal = computed(() => adjustedItems.value.reduce((t, i) => t + i.subtotal, 0))
const tax = computed(() => Math.round(subtotal.value * 0.11))
const deliveryFee = 15000
const total = computed(() => subtotal.value + tax.value + deliveryFee)

function formatPrice(p: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p)
}

function submit() {
    // Frontend validation before submit
    if (!form.event_date) {
        toast('Silakan pilih tanggal acara.', 'warning')
        return
    }
    const selectedDate = new Date(form.event_date)
    const tomorrow = new Date()
    tomorrow.setHours(0, 0, 0, 0)
    tomorrow.setDate(tomorrow.getDate() + 1)
    if (selectedDate < tomorrow) {
        toast('Tanggal acara harus minimal besok. Silakan pilih tanggal yang lebih jauh.', 'warning')
        return
    }
    if (!form.event_time) {
        toast('Silakan isi waktu acara.', 'warning')
        return
    }
    if (!form.delivery_address) {
        toast('Alamat pengiriman wajib diisi.', 'warning')
        return
    }
    if (!form.payment_method || !form.payment_provider) {
        toast('Silakan pilih metode dan provider pembayaran.', 'warning')
        return
    }
    form.post('/checkout')
}

const eventTypes = ['Pernikahan', 'Ulang Tahun', 'Rapat Kantor', 'Seminar', 'Arisan', 'Gathering', 'Syukuran', 'Lainnya']
</script>

<template>
    <CateringLayout>
        <Head title="Checkout Pesanan — CateringKu" />

        <!-- Toast Popup -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            leave-active-class="transition-all duration-200 ease-in"
            enter-from-class="opacity-0 -translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 -translate-y-4 scale-95"
        >
            <div v-if="showToast" class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] max-w-md w-full px-4">
                <div :class="[
                    'rounded-2xl px-5 py-4 shadow-2xl backdrop-blur-xl border flex items-start gap-3',
                    toastType === 'error' ? 'bg-red-50/95 dark:bg-red-900/80 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200' :
                    toastType === 'warning' ? 'bg-amber-50/95 dark:bg-amber-900/80 border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200' :
                    'bg-green-50/95 dark:bg-green-900/80 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200',
                ]">
                    <span class="text-xl mt-0.5">{{ toastType === 'error' ? '❌' : toastType === 'warning' ? '⚠️' : '✅' }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ toastType === 'error' ? 'Terjadi Kesalahan' : toastType === 'warning' ? 'Perhatian' : 'Berhasil' }}</p>
                        <p class="text-sm mt-0.5 opacity-90">{{ toastMessage }}</p>
                    </div>
                    <button @click="showToast = false" class="opacity-60 hover:opacity-100 transition-opacity text-lg leading-none mt-0.5">×</button>
                </div>
            </div>
        </Transition>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[var(--ck-text-primary)] flex items-center gap-3">
                <span class="w-10 h-10 bg-ck-primary/10 rounded-xl flex items-center justify-center text-xl">📋</span>
                Checkout Pesanan
            </h1>
            <p class="text-[var(--ck-text-muted)] mt-1 ml-[52px]">Lengkapi detail acara dan pembayaran Anda</p>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Event Details -->
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 shadow-sm">
                    <h3 class="font-bold text-[var(--ck-text-primary)] mb-5 flex items-center gap-2 text-lg">
                        <span class="w-8 h-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center text-base">📅</span>
                        Detail Acara
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Tipe Acara *</label>
                            <select v-model="form.event_type" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]">
                                <option value="">Pilih tipe acara</option>
                                <option v-for="t in eventTypes" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Jumlah Tamu *</label>
                            <input v-model.number="form.num_people" type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" placeholder="Jumlah tamu undangan" />
                            <p class="text-xs text-[var(--ck-text-muted)] mt-1">Jumlah porsi akan dihitung otomatis berdasarkan tamu</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Tanggal Acara *</label>
                            <input v-model="form.event_date" type="date" :min="minDate" required class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" />
                            <p v-if="form.errors.event_date" class="text-red-500 text-xs mt-1">{{ form.errors.event_date }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Waktu Acara *</label>
                            <input v-model="form.event_time" type="time" required class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" />
                        </div>
                    </div>
                </div>

                <!-- Delivery -->
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 shadow-sm">
                    <h3 class="font-bold text-[var(--ck-text-primary)] mb-5 flex items-center gap-2 text-lg">
                        <span class="w-8 h-8 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center text-base">📍</span>
                        Alamat Pengiriman
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Alamat Lengkap *</label>
                            <textarea v-model="form.delivery_address" rows="3" required class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" placeholder="Jl. ... No. ..., RT/RW, Kelurahan, Kecamatan"></textarea>
                            <p v-if="form.errors.delivery_address" class="text-red-500 text-xs mt-1">{{ form.errors.delivery_address }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Kota</label>
                            <input v-model="form.delivery_city" type="text" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" placeholder="Nama kota" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Catatan Khusus</label>
                            <textarea v-model="form.special_request" rows="2" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm bg-[var(--ck-surface)] text-[var(--ck-text-primary)]" placeholder="Mis: alergi, pantangan, dekorasi khusus..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 shadow-sm">
                    <h3 class="font-bold text-[var(--ck-text-primary)] mb-5 flex items-center gap-2 text-lg">
                        <span class="w-8 h-8 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center text-base">💳</span>
                        Metode Pembayaran
                    </h3>

                    <!-- Payment Category Selection -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        <button
                            v-for="cat in paymentCategories" :key="cat.method"
                            type="button"
                            @click="selectPaymentCategory(cat.method)"
                            class="relative p-4 rounded-xl border-2 text-center transition-all duration-200 text-sm group"
                            :class="selectedCategory === cat.method
                                ? 'border-ck-primary bg-gradient-to-b from-ck-primary/5 to-ck-primary/10 text-ck-primary shadow-sm shadow-ck-primary/10'
                                : 'border-[var(--ck-surface-border)] text-[var(--ck-text-secondary)] hover:border-[var(--ck-text-muted)] hover:shadow-sm'"
                        >
                            <div class="text-2xl mb-1.5 transition-transform duration-200 group-hover:scale-110">{{ cat.icon }}</div>
                            <div class="font-semibold text-xs">{{ cat.label }}</div>
                            <!-- Active indicator -->
                            <div v-if="selectedCategory === cat.method" class="absolute -top-1 -right-1 w-5 h-5 bg-ck-primary rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </button>
                    </div>

                    <!-- Provider Selection -->
                    <transition name="slide">
                        <div v-if="activeProviders.length > 1" class="mt-4 p-4 bg-gray-50 dark:bg-[#1a1b2e] rounded-xl">
                            <p class="text-xs font-medium text-[var(--ck-text-muted)] mb-3 uppercase tracking-wider">Pilih Provider</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button
                                    v-for="provider in activeProviders" :key="provider.id"
                                    type="button"
                                    @click="selectProvider(provider.id)"
                                    class="px-4 py-3 rounded-lg border-2 text-center transition-all duration-200 text-sm font-semibold"
                                    :class="form.payment_provider === provider.id
                                        ? 'border-ck-primary bg-[var(--ck-surface)] text-ck-primary shadow-sm'
                                        : 'border-transparent bg-[var(--ck-surface)] text-[var(--ck-text-secondary)] hover:border-[var(--ck-surface-border)]'"
                                >
                                    <div class="w-3 h-3 rounded-full mx-auto mb-1.5" :style="{ backgroundColor: provider.color }"></div>
                                    {{ provider.name }}
                                </button>
                            </div>
                        </div>
                    </transition>

                    <p v-if="form.errors.payment_method" class="text-red-500 text-xs mt-2">{{ form.errors.payment_method }}</p>
                    <p v-if="form.errors.payment_provider" class="text-red-500 text-xs mt-1">{{ form.errors.payment_provider }}</p>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div>
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 sticky top-24 shadow-sm">
                    <h3 class="font-bold text-[var(--ck-text-primary)] mb-4 flex items-center gap-2">
                        <span class="text-lg">🧾</span>
                        Ringkasan Pesanan
                    </h3>

                    <!-- Guest count indicator -->
                    <div class="bg-ck-primary/5 border border-ck-primary/20 rounded-xl px-4 py-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">👥</span>
                            <span class="text-sm font-semibold text-ck-primary">{{ form.num_people }} tamu</span>
                        </div>
                        <p class="text-xs text-[var(--ck-text-muted)] mt-1">Porsi dihitung otomatis × jumlah tamu</p>
                    </div>

                    <!-- Adjusted Items Preview -->
                    <div class="space-y-3 mb-4">
                        <div v-for="(item, idx) in adjustedItems" :key="idx" class="flex justify-between items-start text-sm">
                            <div class="flex-1 min-w-0 pr-3">
                                <p class="font-medium text-[var(--ck-text-primary)] truncate">{{ item.name }}</p>
                                <p class="text-xs text-[var(--ck-text-muted)]">
                                    {{ item.baseQty }} jenis × {{ form.num_people }} tamu = <span class="font-semibold text-ck-primary">{{ item.adjustedQty }} {{ item.unit }}</span>
                                </p>
                                <p class="text-xs text-[var(--ck-text-muted)]">@ {{ formatPrice(item.unitPrice) }}</p>
                            </div>
                            <span class="font-semibold text-[var(--ck-text-secondary)] whitespace-nowrap">{{ formatPrice(item.subtotal) }}</span>
                        </div>
                    </div>

                    <hr class="border-[var(--ck-surface-border)] mb-3">

                    <!-- Totals -->
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>Subtotal</span><span>{{ formatPrice(subtotal) }}</span></div>
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>PPN (11%)</span><span>{{ formatPrice(tax) }}</span></div>
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>Biaya Pengiriman</span><span>{{ formatPrice(deliveryFee) }}</span></div>
                        <hr class="border-[var(--ck-surface-border)]">
                        <div class="flex justify-between font-bold text-lg text-[var(--ck-text-primary)]">
                            <span>Total</span>
                            <span class="text-ck-primary">{{ formatPrice(total) }}</span>
                        </div>
                    </div>

                    <!-- Selected Payment Info -->
                    <div v-if="form.payment_method && form.payment_provider" class="mt-4 p-3 bg-green-50 border border-green-100 rounded-xl">
                        <p class="text-xs text-green-600 font-medium">✅ Pembayaran via {{ form.payment_provider.toUpperCase().replace('_', ' ') }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing || !form.payment_method || !form.payment_provider"
                        class="btn-primary w-full mt-6 text-center disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Memproses...
                        </span>
                        <span v-else>Buat Pesanan</span>
                    </button>
                </div>
            </div>
        </form>
    </CateringLayout>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active {
    transition: all 0.3s ease;
}
.slide-enter-from, .slide-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
