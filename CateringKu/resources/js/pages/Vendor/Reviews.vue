<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

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
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 gap-4">
                <Link href="/vendor-panel" class="flex items-center gap-2.5 group"><img src="/images/logo.svg" class="h-9 w-9 rounded-xl shadow-sm" /><span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span></Link>
                <span class="px-2.5 py-1 bg-gradient-to-r from-ck-primary to-orange-500 text-white text-xs rounded-lg font-bold shadow-sm shadow-orange-200">VENDOR</span>
                <div class="ml-auto flex items-center gap-1">
                    <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                    <Link href="/vendor-panel" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Dashboard</Link>
                    <Link href="/vendor-panel/orders" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesanan</Link>
                    <Link href="/vendor-panel/menu" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Menu</Link>
                    <div class="w-px h-6 bg-gray-200 mx-2"></div>
                    <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                </div>
            </div>
        </nav>

        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">⭐ Ulasan Pelanggan</h1>
                <div class="bg-ck-primary/10 text-ck-primary px-4 py-2 rounded-xl font-bold">Rating: {{ Number(vendorRating).toFixed(1) }} ⭐</div>
            </div>

            <div class="space-y-4">
                <div v-for="review in reviews.data" :key="review.review_id" class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-ck-primary/10 text-ck-primary rounded-full flex items-center justify-center font-bold text-sm">{{ review.user?.name?.charAt(0) }}</div>
                            <div>
                                <p class="font-medium text-gray-800">{{ review.user?.name }}</p>
                                <div class="flex gap-0.5"><span v-for="i in 5" :key="i" class="text-sm" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-200'">★</span></div>
                            </div>
                        </div>
                    </div>
                    <p v-if="review.review_text" class="text-gray-600 text-sm mb-3">{{ review.review_text }}</p>

                    <!-- Existing Response -->
                    <div v-if="review.vendor_response" class="bg-ck-bg-warm rounded-xl p-4 border-l-3 border-ck-primary">
                        <p class="text-sm text-ck-primary font-semibold mb-1">Respon Anda:</p>
                        <p class="text-sm text-gray-600">{{ review.vendor_response }}</p>
                    </div>

                    <!-- Respond -->
                    <div v-else>
                        <button v-if="respondingId !== review.review_id" @click="respondingId = review.review_id" class="text-sm text-ck-primary hover:text-ck-primary-dark font-medium mt-2">Balas Ulasan</button>
                        <form v-else @submit.prevent="submitResponse(review.review_id)" class="mt-3 space-y-3">
                            <textarea v-model="responseForm.response" rows="2" required class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-ck-primary" placeholder="Tulis respon..."></textarea>
                            <div class="flex gap-2">
                                <button type="submit" :disabled="responseForm.processing" class="bg-ck-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Kirim</button>
                                <button type="button" @click="respondingId = null" class="text-gray-500 text-sm px-3">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="reviews.data.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                <div class="text-5xl mb-3">⭐</div>
                <p class="text-gray-500">Belum ada ulasan dari pelanggan.</p>
            </div>
        </div>
    </div>
</template>
