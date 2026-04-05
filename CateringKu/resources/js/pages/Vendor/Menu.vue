<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{ menuItems: any[], categories: any[] }>()

function formatPrice(p: number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(p) }

const showAddForm = ref(false)
const editingId = ref<number | null>(null)

const addForm = useForm({ item_name: '', price: 0, category_id: '', description: '', unit: 'porsi', min_order: 1 })
const editForm = useForm({ item_name: '', price: 0, category_id: '', description: '', unit: '', min_order: 1, is_available: true })

function submitAdd() {
    addForm.post('/vendor-panel/menu', { preserveScroll: true, onSuccess: () => { addForm.reset(); showAddForm.value = false } })
}

function startEdit(item: any) {
    editingId.value = item.item_id
    editForm.item_name = item.item_name
    editForm.price = item.price
    editForm.category_id = item.category_id || ''
    editForm.description = item.description || ''
    editForm.unit = item.unit
    editForm.min_order = item.min_order
    editForm.is_available = item.is_available
}

function submitEdit(id: number) {
    editForm.patch(`/vendor-panel/menu/${id}`, { preserveScroll: true, onSuccess: () => { editingId.value = null } })
}

function deleteItem(id: number) {
    if (confirm('Yakin ingin menghapus menu ini?')) router.delete(`/vendor-panel/menu/${id}`, { preserveScroll: true })
}
</script>

<template>
    <Head title="Menu - Vendor CateringKu" />
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white/80 backdrop-blur-xl shadow-sm border-b border-gray-100/80 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 gap-4">
                <Link href="/vendor-panel" class="flex items-center gap-2.5 group"><img src="/images/logo.svg" class="h-9 w-9 rounded-xl shadow-sm" /><span class="text-lg font-bold bg-gradient-to-r from-ck-primary to-orange-600 bg-clip-text text-transparent">CateringKu</span></Link>
                <span class="px-2.5 py-1 bg-gradient-to-r from-ck-primary to-orange-500 text-white text-xs rounded-lg font-bold shadow-sm shadow-orange-200">VENDOR</span>
                <div class="ml-auto flex items-center gap-1">
                    <Link href="/" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">🌐 Website</Link>
                    <Link href="/vendor-panel" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Dashboard</Link>
                    <Link href="/vendor-panel/orders" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Pesanan</Link>
                    <Link href="/vendor-panel/reviews" class="px-3 py-2 text-sm text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg font-medium transition-all">Ulasan</Link>
                    <div class="w-px h-6 bg-gray-200 mx-2"></div>
                    <Link href="/logout" method="post" as="button" class="px-3 py-2 text-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-medium transition-all">Keluar</Link>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">🍽️ Kelola Menu</h1>
                <button @click="showAddForm = !showAddForm" class="btn-primary text-sm !py-2.5">+ Tambah Menu</button>
            </div>

            <!-- Add Form -->
            <div v-if="showAddForm" class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-800 mb-4">Tambah Menu Baru</h3>
                <form @submit.prevent="submitAdd" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Nama Menu *</label><input v-model="addForm.item_name" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-ck-primary" /></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Harga *</label><input v-model.number="addForm.price" type="number" required min="0" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-ck-primary" /></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label><select v-model="addForm.category_id" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm"><option value="">-</option><option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Satuan</label><input v-model="addForm.unit" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm" /></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Min. Order</label><input v-model.number="addForm.min_order" type="number" min="1" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm" /></div>
                    <div class="flex items-end"><button type="submit" :disabled="addForm.processing" class="btn-primary text-sm !py-2.5 w-full">Simpan</button></div>
                    <div class="md:col-span-3"><label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label><textarea v-model="addForm.description" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm"></textarea></div>
                </form>
            </div>

            <!-- Menu List -->
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    <div v-for="item in menuItems" :key="item.item_id" class="p-5">
                        <template v-if="editingId === item.item_id">
                            <form @submit.prevent="submitEdit(item.item_id)" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                <div><label class="block text-xs text-gray-500 mb-1">Nama</label><input v-model="editForm.item_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm" /></div>
                                <div><label class="block text-xs text-gray-500 mb-1">Harga</label><input v-model.number="editForm.price" type="number" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm" /></div>
                                <div><label class="block text-xs text-gray-500 mb-1">Status</label><select v-model="editForm.is_available" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"><option :value="true">Tersedia</option><option :value="false">Habis</option></select></div>
                                <div class="flex gap-2"><button type="submit" class="bg-ck-primary text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button><button type="button" @click="editingId = null" class="text-gray-500 px-3 py-2 text-sm">Batal</button></div>
                            </form>
                        </template>
                        <template v-else>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-ck-primary-light rounded-xl flex items-center justify-center text-2xl shrink-0">🍽️</div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ item.item_name }}</h4>
                                        <p class="text-sm text-gray-400">{{ item.category?.category_name || '-' }} • {{ item.unit }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-bold text-ck-primary">{{ formatPrice(item.price) }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full" :class="item.is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">{{ item.is_available ? 'Tersedia' : 'Habis' }}</span>
                                    <button @click="startEdit(item)" class="text-sm text-blue-500 hover:text-blue-700">Edit</button>
                                    <button @click="deleteItem(item.item_id)" class="text-sm text-red-500 hover:text-red-700">Hapus</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="menuItems.length === 0" class="p-10 text-center text-gray-500">Belum ada menu. Klik "Tambah Menu" untuk mulai.</div>
            </div>
        </div>
    </div>
</template>
