<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{ vendor: any, reviews: any[], menuCategories: any[] }>()
const page = usePage()
const activeCategory = ref<number | null>(null)
const quantity = ref(1)

function filteredItems() {
    if (activeCategory.value === null) return props.vendor.menu_items
    return props.vendor.menu_items.filter((i: any) => i.category_id === activeCategory.value)
}

function formatPrice(p: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p)
}

function addToCart(itemId: number) {
    if (!page.props.auth?.user) {
        router.visit('/login')
        return
    }
    router.post('/cart/add', {
        vendor_id: props.vendor.vendor_id,
        item_id: itemId,
        quantity: 1,
    }, { preserveScroll: true })
}
</script>

<template>
    <CateringLayout>
        <!-- Banner -->
        <div class="gradient-hero rounded-3xl p-8 md:p-12 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ vendor.vendor_name }}</h1>
                <p class="text-orange-100 max-w-2xl mb-4">{{ vendor.description }}</p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="flex items-center gap-1.5 bg-white/20 glass px-3 py-1.5 rounded-full">⭐ {{ Number(vendor.rating).toFixed(1) }} ({{ vendor.total_reviews }} ulasan)</span>
                    <span class="flex items-center gap-1.5 bg-white/20 glass px-3 py-1.5 rounded-full">📍 {{ vendor.city }}</span>
                    <span v-if="vendor.phone" class="flex items-center gap-1.5 bg-white/20 glass px-3 py-1.5 rounded-full">📞 {{ vendor.phone }}</span>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="flex overflow-x-auto gap-2 mb-6 pb-2">
            <button @click="activeCategory = null" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors" :class="activeCategory === null ? 'bg-ck-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary'">
                Semua
            </button>
            <button v-for="cat in menuCategories" :key="cat.category_id" @click="activeCategory = cat.category_id" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors" :class="activeCategory === cat.category_id ? 'bg-ck-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-ck-primary'">
                {{ cat.category_name }}
            </button>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <div v-for="item in filteredItems()" :key="item.item_id" class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-hover">
                <div class="h-40 bg-gradient-to-br from-ck-primary-light to-orange-100 flex items-center justify-center">
                    <img v-if="item.image_url" :src="item.image_url" :alt="item.item_name" class="w-full h-full object-cover" />
                    <span v-else class="text-4xl">🍽️</span>
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-900">{{ item.item_name }}</h3>
                        <span v-if="item.category" class="text-xs bg-ck-primary/10 text-ck-primary px-2 py-0.5 rounded-full">{{ item.category.category_name }}</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ item.description }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-ck-primary">{{ formatPrice(item.price) }}</span>
                            <span class="text-xs text-gray-400 ml-1">/ {{ item.unit }}</span>
                        </div>
                        <button @click="addToCart(item.item_id)" class="bg-ck-primary hover:bg-ck-primary-dark text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div v-if="reviews.length > 0" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-1 h-8 bg-gradient-to-b from-ck-primary to-ck-coral rounded-full mr-3"></span>
                Ulasan Pelanggan
            </h2>
            <div class="space-y-4">
                <div v-for="review in reviews" :key="review.review_id" class="bg-white rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-ck-primary/10 text-ck-primary rounded-full flex items-center justify-center font-bold text-sm">{{ review.user?.name?.charAt(0) }}</div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ review.user?.name }}</p>
                                <div class="flex gap-0.5 mt-0.5">
                                    <span v-for="i in 5" :key="i" class="text-sm" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'">★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="review.review_text" class="text-gray-600 text-sm">{{ review.review_text }}</p>
                    <div v-if="review.vendor_response" class="mt-3 pl-4 border-l-2 border-ck-primary/30 text-sm text-gray-500 italic">
                        <span class="font-medium text-ck-primary not-italic">Respon Vendor:</span> {{ review.vendor_response }}
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
