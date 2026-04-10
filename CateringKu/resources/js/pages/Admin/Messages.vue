<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps<{ messages: { data: any[], links: any[] } }>()

function formatDate(d: string) { return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }
</script>

<template>
    <Head title="Pesan - Admin CateringKu" />
    <AdminLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900">Pesan Masuk</h1>
        </template>

        <div class="max-w-4xl mx-auto">
            <div class="space-y-3">
                <Link v-for="msg in messages.data" :key="msg.message_id" :href="`/admin/messages/${msg.message_id}`"
                      class="block bg-white rounded-2xl border overflow-hidden hover:shadow-lg transition-all duration-200 group"
                      :class="!msg.is_read ? 'border-l-4 border-l-ck-primary border-gray-100/80' : 'border-gray-100/80'">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-colors"
                                     :class="msg.is_read ? 'bg-gray-100 text-gray-500' : 'bg-gradient-to-br from-ck-primary/20 to-ck-coral/20 text-ck-primary'">
                                    {{ msg.name?.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover:text-ck-primary transition-colors">{{ msg.name }}</p>
                                    <p class="text-xs text-gray-400">{{ msg.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span v-if="!msg.is_read" class="w-2 h-2 bg-ck-primary rounded-full animate-pulse"></span>
                                <span class="text-xs text-gray-400">{{ formatDate(msg.created_at) }}</span>
                            </div>
                        </div>
                        <p class="font-medium text-gray-700 text-sm">{{ msg.subject }}</p>
                        <p class="text-gray-500 text-sm line-clamp-2 mt-1">{{ msg.message }}</p>
                    </div>
                </Link>
            </div>

            <div v-if="messages.data.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-100/80">
                <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-100 to-indigo-200 rounded-2xl flex items-center justify-center mb-4 text-3xl">📬</div>
                <h3 class="font-bold text-gray-800 mb-1">Belum Ada Pesan</h3>
                <p class="text-gray-500 text-sm">Pesan dari pengunjung akan muncul di sini.</p>
            </div>

            <div v-if="messages.links && messages.links.length > 3" class="mt-6 flex justify-center gap-1">
                <Link v-for="link in messages.links" :key="link.label" :href="link.url || ''" class="px-3 py-2 rounded-lg text-sm transition-colors" :class="link.active ? 'bg-ck-primary text-white shadow-sm' : link.url ? 'text-gray-600 hover:bg-gray-100' : 'text-gray-300'" v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>
