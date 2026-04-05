<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps<{ carts: any[], totalAmount: number }>()

function formatPrice(p: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p)
}

function updateQty(cartItemId: number, qty: number) {
    router.patch(`/cart/${cartItemId}`, { quantity: qty }, { preserveScroll: true })
}

function removeItem(cartItemId: number) {
    router.delete(`/cart/${cartItemId}`, { preserveScroll: true })
}

function getItemPrice(item: any) {
    if (item.menu_item) return item.menu_item.price * item.quantity
    if (item.package) return item.package.price_per_person * item.quantity
    return 0
}

function getItemName(item: any) {
    return item.menu_item?.item_name || item.package?.package_name || 'Item'
}

function getUnitPrice(item: any) {
    return item.menu_item?.price || item.package?.price_per_person || 0
}

const isEmpty = props.carts.length === 0 || props.carts.every((c: any) => c.items.length === 0)
</script>

<template>
    <CateringLayout>
        <div class="flex items-center gap-3 mb-8">
            <h1 class="text-3xl font-bold text-gray-800">🛒 Keranjang Belanja</h1>
        </div>

        <div v-if="isEmpty" class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Keranjang Kosong</h3>
            <p class="text-gray-500 mb-6">Belum ada item di keranjang. Yuk mulai pesan!</p>
            <Link href="/search" class="btn-primary">Cari Vendor</Link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-6">
                <div v-for="cart in carts" :key="cart.cart_id" class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800">{{ cart.vendor?.vendor_name }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in cart.items" :key="item.cart_item_id" class="px-6 py-4 flex items-center gap-4">
                            <div class="w-16 h-16 bg-ck-primary-light rounded-xl flex items-center justify-center text-2xl shrink-0">🍽️</div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-800 truncate">{{ getItemName(item) }}</h4>
                                <p class="text-sm text-ck-primary font-semibold">{{ formatPrice(getUnitPrice(item)) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="updateQty(item.cart_item_id, item.quantity - 1)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">−</button>
                                <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
                                <button @click="updateQty(item.cart_item_id, item.quantity + 1)" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition-colors">+</button>
                            </div>
                            <p class="font-bold text-gray-800 w-28 text-right">{{ formatPrice(getItemPrice(item)) }}</p>
                            <button @click="removeItem(item.cart_item_id)" class="text-red-400 hover:text-red-600 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>{{ formatPrice(totalAmount) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>PPN (11%)</span><span>{{ formatPrice(Math.round(totalAmount * 0.11)) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Biaya Pengiriman</span><span>{{ formatPrice(15000) }}</span></div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between font-bold text-lg text-gray-900"><span>Total</span><span class="text-ck-primary">{{ formatPrice(totalAmount + Math.round(totalAmount * 0.11) + 15000) }}</span></div>
                    </div>
                    <Link href="/checkout" class="btn-primary w-full mt-6 text-center block">Lanjut ke Checkout</Link>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
