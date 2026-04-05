<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface Vendor {
    vendor_id: number
    vendor_name: string
    description: string
    city: string
    rating: number
    total_reviews: number
    logo_url: string | null
}

defineProps<{
    vendor: Vendor
}>()

function formatRating(r: number) {
    return Number(r).toFixed(1)
}
</script>

<template>
    <Link
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
</template>
