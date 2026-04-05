<script setup lang="ts">
defineProps<{
    size?: 'sm' | 'md' | 'lg'
    showText?: boolean
}>()

const sizeMap = {
    sm: { img: 'h-8 w-8', text: 'text-lg' },
    md: { img: 'h-10 w-10', text: 'text-xl' },
    lg: { img: 'h-14 w-14', text: 'text-2xl' },
}
</script>

<template>
    <div class="flex items-center gap-2">
        <img
            src="/images/logo.svg"
            alt="CateringKu"
            :class="['rounded-lg', sizeMap[size ?? 'md'].img]"
        />
        <span
            v-if="showText !== false"
            :class="['font-bold text-ck-primary', sizeMap[size ?? 'md'].text]"
        >
            CateringKu
        </span>
    </div>
</template>
