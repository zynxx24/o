<script setup lang="ts">
defineProps<{
    subtotal: number
    serviceFee?: number
    deliveryFee?: number
    discount?: number
    total: number
    promoCode?: string | null
}>()

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)
}
</script>

<template>
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-ck-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
            Ringkasan Pesanan
        </h3>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-medium text-gray-900">{{ formatPrice(subtotal) }}</span>
            </div>

            <div v-if="serviceFee" class="flex justify-between">
                <span class="text-gray-500">Biaya Layanan</span>
                <span class="font-medium text-gray-900">{{ formatPrice(serviceFee) }}</span>
            </div>

            <div v-if="deliveryFee !== undefined" class="flex justify-between">
                <span class="text-gray-500">Biaya Pengiriman</span>
                <span class="font-medium text-gray-900">{{ deliveryFee === 0 ? 'Gratis' : formatPrice(deliveryFee) }}</span>
            </div>

            <div v-if="discount && discount > 0" class="flex justify-between text-green-600">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    Diskon
                    <span v-if="promoCode" class="text-xs bg-green-100 px-1.5 py-0.5 rounded-full font-semibold">{{ promoCode }}</span>
                </span>
                <span class="font-medium">-{{ formatPrice(discount) }}</span>
            </div>

            <hr class="border-gray-100" />

            <div class="flex justify-between text-base pt-1">
                <span class="font-bold text-gray-900">Total</span>
                <span class="font-bold text-ck-primary text-lg">{{ formatPrice(total) }}</span>
            </div>
        </div>
    </div>
</template>
