<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{ message: any }>()

function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>

<template>
    <Head title="Detail Pesan - Admin CateringKu" />
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 gap-4">
                <Link href="/admin" class="flex items-center gap-2.5 group"><img src="/images/logo.svg" class="h-9 w-9 rounded-xl shadow-sm" /><span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span></Link>
                <span class="px-2.5 py-1 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs rounded-lg font-bold shadow-sm shadow-red-200">ADMIN</span>
                <div class="ml-auto flex items-center gap-1">
                    <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                    <Link href="/admin" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Dashboard</Link>
                    <Link href="/admin/messages" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesan</Link>
                    <div class="w-px h-6 bg-gray-200 mx-2"></div>
                    <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                </div>
            </div>
        </nav>

        <div class="max-w-3xl mx-auto px-4 py-8">
            <Link href="/admin/messages" class="text-sm text-gray-500 hover:text-ck-primary mb-4 inline-flex items-center gap-1">← Kembali ke Pesan</Link>

            <div class="bg-white rounded-2xl border border-gray-100 p-8 mt-4">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-ck-primary/10 text-ck-primary rounded-full flex items-center justify-center font-bold text-xl">{{ message.name?.charAt(0) }}</div>
                    <div>
                        <h1 class="font-bold text-xl text-gray-800">{{ message.name }}</h1>
                        <p class="text-sm text-gray-500">{{ message.email }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(message.created_at) }}</p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-500 mb-1">Subjek</p>
                    <p class="font-semibold text-gray-800">{{ message.subject }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Pesan</p>
                    <div class="bg-gray-50 rounded-xl p-5 text-gray-700 leading-relaxed whitespace-pre-wrap">{{ message.message }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
