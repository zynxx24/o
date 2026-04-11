<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import VendorLayout from '@/layouts/VendorLayout.vue'

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
    <VendorLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kelola Menu</h1>
        </template>

        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ menuItems.length }} item menu terdaftar</p>
                </div>
                <button @click="showAddForm = !showAddForm" :class="showAddForm ? 'bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300' : 'btn-primary'" class="text-sm !py-2.5 px-5 rounded-xl font-semibold transition-all flex items-center gap-2">
                    <svg v-if="!showAddForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ showAddForm ? 'Tutup Form' : 'Tambah Menu' }}
                </button>
            </div>

            <!-- Add Form -->
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-2 scale-[0.98]"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="showAddForm" class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] p-6 mb-6 shadow-sm">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center text-white text-sm">+</span>
                        Tambah Menu Baru
                    </h3>
                    <form @submit.prevent="submitAdd" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Nama Menu *</label>
                            <input v-model="addForm.item_name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" placeholder="Mis: Nasi Goreng Spesial" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Harga *</label>
                            <input v-model.number="addForm.price" type="number" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" placeholder="25000" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Kategori</label>
                            <select v-model="addForm.category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all">
                                <option value="">Pilih kategori</option>
                                <option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Satuan</label>
                            <input v-model="addForm.unit" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" placeholder="porsi" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Min. Order</label>
                            <input v-model.number="addForm.min_order" type="number" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" />
                        </div>
                        <div class="flex items-end">
                            <button type="submit" :disabled="addForm.processing" class="btn-primary text-sm !py-2.5 w-full flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan
                            </button>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Deskripsi</label>
                            <textarea v-model="addForm.description" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" placeholder="Deskripsi menu..."></textarea>
                        </div>
                    </form>
                </div>
            </Transition>

            <!-- Menu List -->
            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] overflow-hidden shadow-sm">
                <div class="divide-y divide-gray-50 dark:divide-[#2a2c45]">
                    <div v-for="item in menuItems" :key="item.item_id" class="p-5 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <template v-if="editingId === item.item_id">
                            <form @submit.prevent="submitEdit(item.item_id)" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Nama</label>
                                    <input v-model="editForm.item_name" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Harga</label>
                                    <input v-model.number="editForm.price" type="number" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">Status</label>
                                    <select v-model="editForm.is_available" class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-[#2a2c45] bg-white dark:bg-[#161729] text-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary transition-all">
                                        <option :value="true">Tersedia</option>
                                        <option :value="false">Habis</option>
                                    </select>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-ck-primary hover:bg-ck-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Simpan
                                    </button>
                                    <button type="button" @click="editingId = null" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-white/5 rounded-lg transition-colors">Batal</button>
                                </div>
                            </form>
                        </template>
                        <template v-else>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-ck-primary-lighter to-ck-primary-light dark:from-ck-primary/20 dark:to-ck-coral/20 rounded-xl flex items-center justify-center text-xl shrink-0">🍽️</div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ item.item_name }}</h4>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ item.category?.category_name || '-' }} • {{ item.unit }} • Min. {{ item.min_order }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-ck-primary text-sm">{{ formatPrice(item.price) }}</span>
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-semibold" :class="item.is_available ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/30' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-800/30'">{{ item.is_available ? 'Tersedia' : 'Habis' }}</span>
                                    <button @click="startEdit(item)" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteItem(item.item_id)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="menuItems.length === 0" class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-900/20 rounded-2xl flex items-center justify-center mb-4 text-3xl">🍽️</div>
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-1">Belum Ada Menu</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Klik "Tambah Menu" untuk mulai menambahkan menu katering Anda.</p>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>
