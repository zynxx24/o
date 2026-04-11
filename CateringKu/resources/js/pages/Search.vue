<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
    vendors: { data: any[], links: any[] }
    categories: any[]
    cities: string[]
    filters: { q?: string, city?: string, sort?: string, type?: string }
}>()

const search = ref(props.filters.q || '')
const selectedCity = ref(props.filters.city || '')
const selectedSort = ref(props.filters.sort || 'rating')

function applyFilters() {
    router.get('/search', {
        q: search.value || undefined,
        city: selectedCity.value || undefined,
        sort: selectedSort.value || undefined,
    }, { preserveState: true, preserveScroll: true })
}

function formatRating(r: number) { return Number(r).toFixed(1) }
function vendorPhoto(id: number) { return `https://picsum.photos/seed/vendor${id}/600/400` }
</script>

<template>
    <CateringLayout>
        <Head title="Cari Vendor Katering — CateringKu" />
        <!-- Hero -->
        <div class="gradient-hero rounded-3xl p-8 md:p-10 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="relative z-10 text-center max-w-2xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-bold mb-3">Cari Katering</h1>
                <p class="text-orange-100 mb-6">Temukan vendor katering terbaik sesuai kebutuhan acara Anda</p>
                <div class="flex gap-3 max-w-lg mx-auto">
                    <div class="flex-grow relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Cari vendor..." class="w-full pl-12 pr-4 py-3.5 rounded-xl text-[var(--ck-text-primary)] bg-[var(--ck-surface)] focus:ring-2 focus:ring-orange-300 focus:outline-none shadow-lg text-sm" />
                    </div>
                    <button @click="applyFilters" class="bg-white text-ck-primary px-6 py-3.5 rounded-xl font-semibold hover:bg-orange-50 transition-all shadow-lg text-sm">Cari</button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-8">
            <select v-model="selectedCity" @change="applyFilters" class="px-4 py-2.5 rounded-xl border border-[var(--ck-surface-border)] text-sm text-[var(--ck-text-secondary)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary bg-[var(--ck-surface)]">
                <option value="">Semua Kota</option>
                <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
            </select>
            <select v-model="selectedSort" @change="applyFilters" class="px-4 py-2.5 rounded-xl border border-[var(--ck-surface-border)] text-sm text-[var(--ck-text-secondary)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary bg-[var(--ck-surface)]">
                <option value="rating">Rating Tertinggi</option>
                <option value="newest">Terbaru</option>
                <option value="name">Nama A-Z</option>
            </select>
        </div>

        <!-- Results -->
        <div v-if="vendors.data.length === 0" class="text-center py-16 bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)]">
            <div class="text-5xl mb-4">😔</div>
            <h3 class="text-xl font-bold text-[var(--ck-text-primary)] mb-2">Tidak Ditemukan</h3>
            <p class="text-[var(--ck-text-muted)]">Coba ubah kata kunci atau filter pencarian.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <Link
                v-for="vendor in vendors.data"
                :key="vendor.vendor_id"
                :href="`/vendor/${vendor.vendor_id}`"
                class="group block bg-[var(--ck-surface)] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl dark:hover:shadow-ck-primary/5 transition-all border border-[var(--ck-surface-border)] card-hover"
            >
                <div class="h-44 gradient-hero flex items-center justify-center text-white relative overflow-hidden">
                    <img :src="vendor.logo_url || vendorPhoto(vendor.vendor_id)" :alt="vendor.vendor_name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                    <div class="absolute top-3 right-3 bg-white/90 dark:bg-[var(--ck-surface)]/90 glass px-2.5 py-1 rounded-full text-xs font-bold text-ck-primary shadow flex items-center gap-1">
                        ⭐ {{ formatRating(vendor.rating) }}
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-[var(--ck-text-primary)] group-hover:text-ck-primary transition-colors mb-1.5">{{ vendor.vendor_name }}</h3>
                    <p class="text-[var(--ck-text-muted)] text-sm line-clamp-2 mb-3">{{ vendor.description }}</p>
                    <div class="flex items-center justify-between text-xs text-[var(--ck-text-muted)]">
                        <span class="flex items-center gap-1">📍 {{ vendor.city || 'Indonesia' }}</span>
                        <span>{{ vendor.total_reviews }} ulasan</span>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="vendors.links && vendors.links.length > 3" class="mt-8 flex justify-center gap-1">
            <Link
                v-for="link in vendors.links"
                :key="link.label"
                :href="link.url || ''"
                class="px-3 py-2 rounded-lg text-sm transition-colors"
                :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-[var(--ck-text-secondary)] hover:bg-[var(--ck-surface)]' : 'text-[var(--ck-text-muted)] cursor-not-allowed'"
                v-html="link.label"
            />
        </div>
    </CateringLayout>
</template>
