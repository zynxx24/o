<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

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
        <Head title="Keranjang Belanja — CateringKu" />
        <div class="flex items-center gap-3 mb-8">
            <h1 class="text-3xl font-bold text-[var(--ck-text-primary)]">🛒 Keranjang Belanja</h1>
        </div>

        <div v-if="isEmpty" class="text-center py-20 bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)]">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-[var(--ck-text-primary)] mb-2">Keranjang Kosong</h3>
            <p class="text-[var(--ck-text-muted)] mb-6">Belum ada item di keranjang. Yuk mulai pesan!</p>
            <Link href="/search" class="btn-primary">Cari Vendor</Link>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-6">
                <div v-for="cart in carts" :key="cart.cart_id" class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[var(--ck-surface-border)] bg-gray-50 dark:bg-[#1a1b2e]">
                        <h3 class="font-bold text-[var(--ck-text-primary)]">{{ cart.vendor?.vendor_name }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-for="item in cart.items" :key="item.cart_item_id" class="px-6 py-4 flex items-center gap-4">
                            <div class="w-16 h-16 bg-ck-primary-light dark:bg-ck-primary/10 rounded-xl flex items-center justify-center text-2xl shrink-0">🍽️</div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-[var(--ck-text-primary)] truncate">{{ getItemName(item) }}</h4>
                                <p class="text-sm text-ck-primary font-semibold">{{ formatPrice(getUnitPrice(item)) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="updateQty(item.cart_item_id, item.quantity - 1)" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-[#2a2c45] hover:bg-gray-200 dark:hover:bg-[#333555] flex items-center justify-center text-[var(--ck-text-secondary)] transition-colors">−</button>
                                <span class="w-8 text-center font-medium text-[var(--ck-text-primary)]">{{ item.quantity }}</span>
                                <button @click="updateQty(item.cart_item_id, item.quantity + 1)" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-[#2a2c45] hover:bg-gray-200 dark:hover:bg-[#333555] flex items-center justify-center text-[var(--ck-text-secondary)] transition-colors">+</button>
                            </div>
                            <p class="font-bold text-[var(--ck-text-primary)] w-28 text-right">{{ formatPrice(getItemPrice(item)) }}</p>
                            <button @click="removeItem(item.cart_item_id)" class="text-red-400 hover:text-red-600 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 sticky top-24">
                    <h3 class="font-bold text-[var(--ck-text-primary)] mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>Subtotal</span><span>{{ formatPrice(totalAmount) }}</span></div>
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>PPN (11%)</span><span>{{ formatPrice(Math.round(totalAmount * 0.11)) }}</span></div>
                        <div class="flex justify-between text-[var(--ck-text-secondary)]"><span>Biaya Pengiriman</span><span>{{ formatPrice(15000) }}</span></div>
                        <hr class="border-[var(--ck-surface-border)]">
                        <div class="flex justify-between font-bold text-lg text-[var(--ck-text-primary)]"><span>Total</span><span class="text-ck-primary">{{ formatPrice(totalAmount + Math.round(totalAmount * 0.11) + 15000) }}</span></div>
                    </div>
                    <Link href="/checkout" class="btn-primary w-full mt-6 text-center block">Lanjut ke Checkout</Link>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
