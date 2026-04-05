<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps<{
    placeholder?: string
    modelValue?: string
    showFilter?: boolean
}>()

const emit = defineEmits<{
    'update:modelValue': [value: string]
    search: [query: string]
}>()

const query = ref('')

function handleSearch() {
    emit('search', query.value)
    router.get('/search', { q: query.value }, { preserveState: true })
}
</script>

<template>
    <form @submit.prevent="handleSearch" class="relative flex gap-3 w-full">
        <div class="flex-grow relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                v-model="query"
                type="text"
                :placeholder="placeholder ?? 'Cari nama vendor atau menu...'"
                class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary focus:outline-none transition-all bg-white text-gray-900 shadow-sm"
                @input="emit('update:modelValue', query)"
            />
        </div>
        <button
            type="submit"
            class="btn-primary flex items-center gap-2 shadow-md hover:shadow-lg"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            Cari
        </button>
    </form>
</template>
