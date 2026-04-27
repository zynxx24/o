<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps<{ vendor: any, reviews: any[], menuCategories: any[] }>()
const page = usePage()
const activeCategory = ref<number | null>(null)

function filteredItems() {
    if (activeCategory.value === null) return props.vendor.menu_items
    return props.vendor.menu_items.filter((i: any) => i.category_id === activeCategory.value)
}

function formatPrice(p: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p)
}

// Dummy food photos for items without images
function foodPhoto(itemId: number) {
    return `https://picsum.photos/seed/food${itemId}/400/300`
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

/** Format WA number to international link (0813... → 62813...) */
const waLink = computed(() => {
    let num = props.vendor.whatsapp_number || ''
    num = num.replace(/\D/g, '')
    if (num.startsWith('0')) num = '62' + num.slice(1)
    if (!num.startsWith('62')) num = '62' + num
    return `https://wa.me/${num}`
})
</script>

<template>
    <CateringLayout>
        <Head :title="`${vendor.vendor_name} — CateringKu`" />
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
            <button @click="activeCategory = null" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors" :class="activeCategory === null ? 'bg-ck-primary text-white' : 'bg-[var(--ck-surface)] text-[var(--ck-text-secondary)] border border-[var(--ck-surface-border)] hover:border-ck-primary'">
                Semua
            </button>
            <button v-for="cat in menuCategories" :key="cat.category_id" @click="activeCategory = cat.category_id" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors" :class="activeCategory === cat.category_id ? 'bg-ck-primary text-white' : 'bg-[var(--ck-surface)] text-[var(--ck-text-secondary)] border border-[var(--ck-surface-border)] hover:border-ck-primary'">
                {{ cat.category_name }}
            </button>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <div v-for="item in filteredItems()" :key="item.item_id" class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] overflow-hidden card-hover">
                <div class="h-40 bg-gradient-to-br from-ck-primary-light to-orange-100 dark:from-ck-primary/10 dark:to-orange-900/10 flex items-center justify-center overflow-hidden">
                    <img :src="item.image_url || foodPhoto(item.item_id)" :alt="item.item_name" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" />
                </div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-[var(--ck-text-primary)]">{{ item.item_name }}</h3>
                        <span v-if="item.category" class="text-xs bg-ck-primary/10 text-ck-primary px-2 py-0.5 rounded-full">{{ item.category.category_name }}</span>
                    </div>
                    <p class="text-[var(--ck-text-muted)] text-sm mb-3 line-clamp-2">{{ item.description }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-ck-primary">{{ formatPrice(item.price) }}</span>
                            <span class="text-xs text-[var(--ck-text-muted)] ml-1">/ {{ item.unit }}</span>
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
            <h2 class="text-2xl font-bold text-[var(--ck-text-primary)] mb-6 flex items-center">
                <span class="w-1 h-8 bg-gradient-to-b from-ck-primary to-ck-coral rounded-full mr-3"></span>
                Ulasan Pelanggan
            </h2>
            <div class="space-y-4">
                <div v-for="review in reviews" :key="review.review_id" class="bg-[var(--ck-surface)] rounded-xl p-5 border border-[var(--ck-surface-border)]">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-ck-primary/10 text-ck-primary rounded-full flex items-center justify-center font-bold text-sm">{{ review.user?.name?.charAt(0) }}</div>
                            <div>
                                <p class="font-medium text-[var(--ck-text-primary)] text-sm">{{ review.user?.name }}</p>
                                <div class="flex gap-0.5 mt-0.5">
                                    <span v-for="i in 5" :key="i" class="text-sm" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-700'">★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="review.review_text" class="text-[var(--ck-text-secondary)] text-sm">{{ review.review_text }}</p>
                    <div v-if="review.vendor_response" class="mt-3 pl-4 border-l-2 border-ck-primary/30 text-sm text-[var(--ck-text-muted)] italic">
                        <span class="font-medium text-ck-primary not-italic">Respon Vendor:</span> {{ review.vendor_response }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating WhatsApp Button -->
        <a
            v-if="vendor.whatsapp_number"
            :href="waLink"
            target="_blank"
            rel="noopener noreferrer"
            class="wa-float"
            title="Chat via WhatsApp"
        >
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span class="wa-tooltip">Chat via WhatsApp</span>
        </a>
    </CateringLayout>
</template>

<style scoped>
.wa-float {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 3.75rem;
    height: 3.75rem;
    border-radius: 50%;
    background: #25d366;
    color: #fff;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.45);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: wa-pulse 2s infinite;
}
.wa-float:hover {
    transform: scale(1.12);
    box-shadow: 0 6px 28px rgba(37, 211, 102, 0.6);
    animation: none;
}
.wa-tooltip {
    position: absolute;
    right: calc(100% + 0.75rem);
    white-space: nowrap;
    background: #1f2937;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.wa-float:hover .wa-tooltip {
    opacity: 1;
}
@keyframes wa-pulse {
    0%, 100% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.45); }
    50% { box-shadow: 0 4px 30px rgba(37, 211, 102, 0.7), 0 0 0 10px rgba(37, 211, 102, 0.1); }
}
</style>
