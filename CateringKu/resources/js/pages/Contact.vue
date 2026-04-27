<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps<{ authUser: { name: string, email: string } }>()

const form = useForm({ subject: '', message: '' })

function submit() {
    form.post('/contact', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <CateringLayout>
        <Head title="Hubungi Kami — CateringKu" />
        <h1 class="text-3xl font-bold text-[var(--ck-text-primary)] mb-8">📨 Hubungi Kami</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form -->
            <div class="lg:col-span-2">
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 md:p-8">
                    <h2 class="font-bold text-[var(--ck-text-primary)] text-lg mb-6">Kirim Pesan</h2>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Nama</label>
                                <input :value="authUser.name" type="text" disabled class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-[var(--ck-text-muted)] text-sm cursor-not-allowed" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Email</label>
                                <input :value="authUser.email" type="email" disabled class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-[var(--ck-text-muted)] text-sm cursor-not-allowed" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Subjek *</label>
                            <select v-model="form.subject" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm text-[var(--ck-text-primary)]">
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
                            <label class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1.5">Pesan *</label>
                            <textarea v-model="form.message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm text-[var(--ck-text-primary)]" placeholder="Tulis pesan Anda..."></textarea>
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
                <div v-for="info in [{icon:'📧',title:'Email',detail:'info@cateringku.com'},{icon:'📞',title:'Telepon',detail:'(021) 1234-5678'},{icon:'📍',title:'Lokasi',detail:'Jakarta, Indonesia'},{icon:'🕒',title:'Jam Operasional',detail:'Senin-Jumat: 08:00-17:00'}]" :key="info.title" class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-5 card-hover">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center text-xl shrink-0">{{ info.icon }}</div>
                        <div>
                            <p class="font-semibold text-[var(--ck-text-primary)] text-sm">{{ info.title }}</p>
                            <p class="text-[var(--ck-text-muted)] text-sm">{{ info.detail }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
