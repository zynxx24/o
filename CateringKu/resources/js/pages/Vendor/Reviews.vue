<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import VendorLayout from '@/layouts/VendorLayout.vue'

const props = defineProps<{ reviews: { data: any[], links: any[] }, vendorRating: number }>()

const respondingId = ref<number | null>(null)
const responseForm = useForm({ response: '' })

function submitResponse(reviewId: number) {
    responseForm.post(`/vendor-panel/reviews/${reviewId}/respond`, {
        preserveScroll: true,
        onSuccess: () => { respondingId.value = null; responseForm.reset() },
    })
}
</script>

<template>
    <Head title="Ulasan - Vendor CateringKu" />
    <VendorLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Ulasan Pelanggan</h1>
        </template>

        <div class="max-w-4xl mx-auto">
            <!-- Rating summary -->
            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-6 mb-6 flex items-center gap-6">
                <div class="text-center">
                    <div class="text-4xl font-extrabold text-gray-900 dark:text-gray-100">{{ Number(vendorRating).toFixed(1) }}</div>
                    <div class="flex gap-0.5 mt-1 justify-center">
                        <span v-for="i in 5" :key="i" class="text-lg" :class="i <= Math.round(Number(vendorRating)) ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-600'">★</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ reviews.data.length }} ulasan</p>
                </div>
                <div class="h-12 w-px bg-gray-100 dark:bg-[#2a2c45]"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Rating rata-rata dari seluruh ulasan pelanggan Anda. Tanggapi ulasan untuk meningkatkan kepuasan pelanggan.</p>
            </div>

            <div class="space-y-4">
                <div v-for="review in reviews.data" :key="review.review_id" class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-6 hover:shadow-sm transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-ck-primary/20 to-ck-coral/20 text-ck-primary rounded-xl flex items-center justify-center font-bold text-sm">{{ review.user?.name?.charAt(0) }}</div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ review.user?.name }}</p>
                                <div class="flex gap-0.5">
                                    <span v-for="i in 5" :key="i" class="text-sm" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-600'">★</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="review.review_text" class="text-gray-600 dark:text-gray-400 text-sm mb-3 leading-relaxed">{{ review.review_text }}</p>

                    <!-- Existing Response -->
                    <div v-if="review.vendor_response" class="bg-gradient-to-r from-ck-bg-warm to-ck-primary-lighter dark:from-ck-primary/10 dark:to-ck-coral/10 rounded-xl p-4 border-l-3 border-ck-primary">
                        <p class="text-xs text-ck-primary font-bold mb-1 uppercase tracking-wider">Respon Anda</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ review.vendor_response }}</p>
                    </div>

                    <!-- Respond -->
                    <div v-else>
                        <button v-if="respondingId !== review.review_id" @click="respondingId = review.review_id" class="text-sm text-ck-primary hover:text-ck-primary-dark font-semibold mt-2 flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-ck-primary/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Balas Ulasan
                        </button>
                        <form v-else @submit.prevent="submitResponse(review.review_id)" class="mt-3 space-y-3">
                            <textarea v-model="responseForm.response" rows="2" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" placeholder="Tulis respon untuk pelanggan..."></textarea>
                            <div class="flex gap-2">
                                <button type="submit" :disabled="responseForm.processing" class="bg-ck-primary hover:bg-ck-primary-dark text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim
                                </button>
                                <button type="button" @click="respondingId = null" class="text-gray-500 dark:text-gray-400 text-sm px-4 py-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="reviews.data.length === 0" class="text-center py-16 bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45]">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-yellow-100 to-amber-200 dark:from-yellow-900/30 dark:to-amber-900/30 rounded-2xl flex items-center justify-center mb-4 text-3xl">⭐</div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-1">Belum Ada Ulasan</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Ulasan dari pelanggan akan muncul di sini.</p>
            </div>

            <!-- Pagination -->
            <div v-if="reviews.links && reviews.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in reviews.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm" :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5' : 'text-gray-300 dark:text-gray-600'" v-html="link.label" />
            </div>
        </div>
    </VendorLayout>
</template>
