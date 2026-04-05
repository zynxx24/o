<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
    name: string
    icon?: string
    type?: string
    active?: boolean
}>()
</script>

<template>
    <Link
        :href="`/search?type=${type ?? name.toLowerCase()}`"
        :class="[
            'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all border',
            active
                ? 'bg-ck-primary text-white border-ck-primary shadow-lg shadow-ck-primary/20'
                : 'bg-white text-gray-600 border-gray-200 hover:border-ck-primary/30 hover:text-ck-primary hover:shadow-md',
        ]"
    >
        <span v-if="icon" class="text-base">{{ icon }}</span>
        {{ name }}
    </Link>
</template>
