<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps<{ message: any }>()

function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>

<template>
    <Head title="Detail Pesan - Admin CateringKu" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link href="/admin/messages" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Pesan</h1>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-[#2a2c45]">
                    <div class="w-14 h-14 bg-gradient-to-br from-ck-primary/20 to-ck-coral/20 text-ck-primary rounded-2xl flex items-center justify-center font-bold text-xl">{{ message.name?.charAt(0) }}</div>
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">{{ message.name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ message.email }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ formatDate(message.created_at) }}</p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-1.5 uppercase tracking-wider">Subjek</p>
                    <p class="font-semibold text-gray-800 dark:text-gray-200 text-lg">{{ message.subject }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wider">Pesan</p>
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 dark:from-[#161729] dark:to-[#1a1b30] rounded-xl p-6 text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap border border-gray-100/80 dark:border-[#2a2c45]">{{ message.message }}</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
