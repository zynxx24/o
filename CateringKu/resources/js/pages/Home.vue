<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

interface Vendor {
    vendor_id: number
    vendor_name: string
    description: string
    city: string
    rating: number
    total_reviews: number
    logo_url: string | null
}

interface Category {
    category_id: number
    category_name: string
    menu_items_count: number
}

defineProps<{
    vendors: Vendor[]
    categories: Category[]
}>()

const quickCategories = [
    { name: 'Prasmanan', icon: '🍽️', type: 'prasmanan' },
    { name: 'Nasi Kotak', icon: '🍱', type: 'nasi-kotak' },
    { name: 'Snack Box', icon: '🥪', type: 'snack-box' },
    { name: 'Tumpeng', icon: '🍚', type: 'tumpeng' },
    { name: 'Dessert', icon: '🍰', type: 'dessert' },
    { name: 'Pernikahan', icon: '💒', type: 'wedding' },
]

const features = [
    { title: 'Vendor Terpercaya', desc: 'Semua vendor telah melalui verifikasi ketat', icon: '🛡️', color: 'from-green-100 to-emerald-200' },
    { title: 'Proses Cepat', desc: 'Pesan dalam hitungan menit, terima tepat waktu', icon: '⚡', color: 'from-blue-100 to-cyan-200' },
    { title: 'Pembayaran Mudah', desc: 'Berbagai metode pembayaran tersedia', icon: '💳', color: 'from-purple-100 to-violet-200' },
]

function formatRating(r: number) {
    return Number(r).toFixed(1)
}
</script>

<template>
    <CateringLayout>
        <Head title="Beranda — Temukan Katering Terbaik" />
        <!-- Hero Section -->
        <div class="gradient-hero rounded-3xl p-8 md:p-12 mb-12 text-white relative overflow-hidden shadow-2xl">
            <!-- Background decorations -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 animate-float"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-16 -mb-16"></div>

            <div class="relative z-10 text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">Temukan Katering Terbaik</h1>
                <p class="text-lg text-orange-100 mb-8">
                    Pesan katering untuk berbagai acara dengan mudah dan cepat. Pilihan beragam dari vendor terpercaya.
                </p>

                <form action="/search" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-xl mx-auto">
                    <div class="flex-grow relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            type="text" name="q"
                            placeholder="Cari nama vendor atau menu..."
                            class="w-full pl-12 pr-4 py-4 rounded-xl text-gray-900 focus:ring-2 focus:ring-orange-300 focus:outline-none shadow-lg"
                        />
                    </div>
                    <button type="submit" class="bg-white text-ck-primary px-8 py-4 rounded-xl font-semibold hover:bg-orange-50 transition-all shadow-lg">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Categories -->
        <div class="mb-12">
            <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide">
                <Link
                    v-for="cat in quickCategories"
                    :key="cat.type"
                    :href="`/search?type=${cat.type}`"
                    class="flex-shrink-0 flex items-center gap-3 bg-white px-5 py-3 rounded-xl border border-gray-100 hover:border-ck-primary/30 hover:shadow-lg transition-all card-hover"
                >
                    <span class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center text-xl">{{ cat.icon }}</span>
                    <span class="font-medium text-gray-700">{{ cat.name }}</span>
                </Link>
            </div>
        </div>

        <!-- Vendors Grid -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="w-1 h-8 bg-gradient-to-b from-ck-primary to-ck-coral rounded-full mr-3"></span>
                    Mitra Katering Kami
                </h2>
                <Link href="/search" class="text-ck-primary hover:text-ck-primary-dark font-medium flex items-center transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </Link>
            </div>

            <div v-if="vendors.length === 0" class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center mb-4 text-4xl">🏪</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Vendor</h3>
                <p class="text-gray-500">Belum ada vendor katering yang tersedia saat ini.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <Link
                    v-for="vendor in vendors"
                    :key="vendor.vendor_id"
                    :href="`/vendor/${vendor.vendor_id}`"
                    class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 card-hover"
                >
                    <!-- Image -->
                    <div class="h-48 gradient-hero flex items-center justify-center text-white relative overflow-hidden">
                        <img v-if="vendor.logo_url" :src="vendor.logo_url" :alt="vendor.vendor_name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        <span v-else class="text-6xl font-bold opacity-20 group-hover:scale-110 transition-transform duration-500">
                            {{ vendor.vendor_name.charAt(0) }}
                        </span>

                        <div class="absolute top-4 right-4 bg-white/90 glass px-3 py-1 rounded-full text-sm font-semibold text-ck-primary shadow-lg flex items-center">
                            <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            {{ formatRating(vendor.rating) }}
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-ck-primary transition-colors mb-2">
                            {{ vendor.vendor_name }}
                        </h3>
                        <p class="text-gray-500 text-sm line-clamp-2 mb-4">{{ vendor.description }}</p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ vendor.city || 'Indonesia' }}
                            </span>
                            <span class="text-gray-400">{{ vendor.total_reviews }} ulasan</span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div v-for="feature in features" :key="feature.title" class="bg-white rounded-2xl p-6 border border-gray-100 text-center card-hover">
                <div :class="`w-14 h-14 bg-gradient-to-br ${feature.color} rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl`">
                    {{ feature.icon }}
                </div>
                <h3 class="font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
                <p class="text-gray-500 text-sm">{{ feature.desc }}</p>
            </div>
        </div>
    </CateringLayout>
</template>
