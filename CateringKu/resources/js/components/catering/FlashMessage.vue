<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps<{
    type?: 'success' | 'error' | 'warning' | 'info'
    message: string
    autoDismiss?: boolean
    duration?: number
}>()

const emit = defineEmits<{
    dismiss: []
}>()

const visible = ref(true)

const iconMap = {
    success: { icon: '✓', bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-700', iconBg: 'bg-green-100 text-green-600' },
    error: { icon: '✕', bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-700', iconBg: 'bg-red-100 text-red-600' },
    warning: { icon: '!', bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-700', iconBg: 'bg-amber-100 text-amber-600' },
    info: { icon: 'i', bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-700', iconBg: 'bg-blue-100 text-blue-600' },
}

const config = iconMap[props.type ?? 'success']

function dismiss() {
    visible.value = false
    emit('dismiss')
}

onMounted(() => {
    if (props.autoDismiss !== false) {
        setTimeout(dismiss, props.duration ?? 5000)
    }
})
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0 -translate-y-2"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="visible"
            :class="[
                'p-4 rounded-xl border flex items-center gap-3 shadow-sm',
                config.bg,
                config.border,
                config.text,
            ]"
        >
            <span :class="['w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0', config.iconBg]">
                {{ config.icon }}
            </span>
            <span class="text-sm flex-1">{{ message }}</span>
            <button @click="dismiss" class="shrink-0 p-1 rounded-lg hover:bg-black/5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </Transition>
</template>
