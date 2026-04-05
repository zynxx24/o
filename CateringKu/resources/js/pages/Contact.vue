<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({ name: '', email: '', subject: '', message: '' })

function submit() {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <CateringLayout>
        <Head title="Hubungi Kami — CateringKu" />
        <h1 class="text-3xl font-bold text-gray-800 mb-8">📨 Hubungi Kami</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                    <h2 class="font-bold text-gray-800 text-lg mb-6">Kirim Pesan</h2>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama *</label>
                                <input v-model="form.name" type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" placeholder="Nama lengkap" />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                                <input v-model="form.email" type="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" placeholder="email@contoh.com" />
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Subjek *</label>
                            <select v-model="form.subject" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm">
                                <option value="">Pilih subjek</option>
                                <option value="general">Pertanyaan Umum</option>
                                <option value="order">Masalah Pesanan</option>
                                <option value="partnership">Kerjasama Bisnis</option>
                                <option value="vendor">Daftar sebagai Vendor</option>
                                <option value="feedback">Saran & Masukan</option>
                                <option value="complaint">Komplain</option>
                            </select>
                            <p v-if="form.errors.subject" class="text-red-500 text-xs mt-1">{{ form.errors.subject }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pesan *</label>
                            <textarea v-model="form.message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm" placeholder="Tulis pesan Anda..."></textarea>
                            <p v-if="form.errors.message" class="text-red-500 text-xs mt-1">{{ form.errors.message }}</p>
                        </div>
                        <button type="submit" :disabled="form.processing" class="btn-primary disabled:opacity-50">
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-6">
                <div v-for="info in [{icon:'📧',title:'Email',detail:'info@cateringku.com'},{icon:'📞',title:'Telepon',detail:'(021) 1234-5678'},{icon:'📍',title:'Lokasi',detail:'Jakarta, Indonesia'},{icon:'🕒',title:'Jam Operasional',detail:'Senin-Jumat: 08:00-17:00'}]" :key="info.title" class="bg-white rounded-2xl border border-gray-100 p-5 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center text-xl shrink-0">{{ info.icon }}</div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ info.title }}</p>
                            <p class="text-gray-500 text-sm">{{ info.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
