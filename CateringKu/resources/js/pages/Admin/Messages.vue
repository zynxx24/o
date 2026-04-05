<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{ messages: { data: any[], links: any[] } }>()

function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>

<template>
    <Head title="Pesan - Admin CateringKu" />
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 gap-4">
                <Link href="/admin" class="flex items-center gap-2.5 group"><img src="/images/logo.svg" class="h-9 w-9 rounded-xl shadow-sm" /><span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span></Link>
                <span class="px-2.5 py-1 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs rounded-lg font-bold shadow-sm shadow-red-200">ADMIN</span>
                <div class="ml-auto flex items-center gap-1">
                    <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                    <Link href="/admin" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Dashboard</Link>
                    <Link href="/admin/orders" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesanan</Link>
                    <div class="w-px h-6 bg-gray-200 mx-2"></div>
                    <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">📬 Pesan Masuk</h1>

            <div class="space-y-4">
                <Link v-for="msg in messages.data" :key="msg.message_id" :href="`/admin/messages/${msg.message_id}`" class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg transition-shadow card-hover" :class="!msg.is_read ? 'border-l-4 border-l-ck-primary' : ''">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm" :class="msg.is_read ? 'bg-gray-100 text-gray-500' : 'bg-ck-primary/10 text-ck-primary'">{{ msg.name?.charAt(0) }}</div>
                            <div>
                                <p class="font-medium text-gray-800">{{ msg.name }}</p>
                                <p class="text-xs text-gray-400">{{ msg.email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatDate(msg.created_at) }}</span>
                    </div>
                    <p class="font-medium text-gray-700 text-sm">{{ msg.subject }}</p>
                    <p class="text-gray-500 text-sm line-clamp-2 mt-1">{{ msg.message }}</p>
                </Link>
            </div>

            <div v-if="messages.links && messages.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in messages.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm" :class="link.active ? 'bg-ck-primary text-white' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300'" v-html="link.label" />
            </div>
        </div>
    </div>
</template>
