<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    status: string
}>()

const statusConfig = computed(() => {
    const map: Record<string, { label: string; bg: string; text: string; dot: string }> = {
        pending: { label: 'Menunggu', bg: 'bg-amber-50', text: 'text-amber-700', dot: 'bg-amber-400' },
        confirmed: { label: 'Dikonfirmasi', bg: 'bg-blue-50', text: 'text-blue-700', dot: 'bg-blue-400' },
        processing: { label: 'Diproses', bg: 'bg-indigo-50', text: 'text-indigo-700', dot: 'bg-indigo-400' },
        delivered: { label: 'Dikirim', bg: 'bg-cyan-50', text: 'text-cyan-700', dot: 'bg-cyan-400' },
        completed: { label: 'Selesai', bg: 'bg-green-50', text: 'text-green-700', dot: 'bg-green-400' },
        cancelled: { label: 'Dibatalkan', bg: 'bg-red-50', text: 'text-red-700', dot: 'bg-red-400' },
        refunded: { label: 'Dikembalikan', bg: 'bg-gray-50', text: 'text-gray-700', dot: 'bg-gray-400' },
    }
    return map[props.status] ?? { label: props.status, bg: 'bg-gray-50', text: 'text-gray-700', dot: 'bg-gray-400' }
})
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold',
            statusConfig.bg,
            statusConfig.text,
        ]"
    >
        <span :class="['w-1.5 h-1.5 rounded-full', statusConfig.dot]" />
        {{ statusConfig.label }}
    </span>
</template>
