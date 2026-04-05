<script setup lang="ts">
import { router } from '@inertiajs/vue3'

interface MenuItem {
    menu_item_id: number
    item_name: string
    description: string
    price: number
    unit: string
    image_url: string | null
    is_available: boolean
}

defineProps<{
    item: MenuItem
    showAddButton?: boolean
}>()

const emit = defineEmits<{
    addToCart: [menuItemId: number]
}>()

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)
}

function addToCart(menuItemId: number) {
    router.post('/cart/add', { menu_item_id: menuItemId, quantity: 1 }, {
        preserveScroll: true,
    })
    emit('addToCart', menuItemId)
}
</script>

<template>
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 card-hover group">
        <!-- Image -->
        <div class="h-36 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center relative overflow-hidden">
            <img v-if="item.image_url" :src="item.image_url" :alt="item.item_name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <span v-else class="text-4xl">🍽️</span>

            <div v-if="!item.is_available" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <span class="text-white font-semibold text-sm bg-red-500 px-3 py-1 rounded-full">Habis</span>
            </div>
        </div>

        <div class="p-4">
            <h4 class="font-bold text-gray-900 mb-1 truncate">{{ item.item_name }}</h4>
            <p class="text-gray-500 text-xs line-clamp-2 mb-3 min-h-[2rem]">{{ item.description }}</p>

            <div class="flex items-center justify-between">
                <div>
                    <span class="text-ck-primary font-bold text-lg">{{ formatPrice(item.price) }}</span>
                    <span class="text-gray-400 text-xs ml-1">/ {{ item.unit }}</span>
                </div>

                <button
                    v-if="showAddButton && item.is_available"
                    @click.prevent="addToCart(item.menu_item_id)"
                    class="w-9 h-9 bg-ck-primary hover:bg-ck-primary-dark text-white rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </button>
            </div>
        </div>
    </div>
</template>
