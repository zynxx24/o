<script setup lang="ts">
import { computed } from 'vue'
import { useAppearance } from '@/composables/useAppearance'

const props = withDefaults(defineProps<{
    size?: 'sm' | 'md' | 'lg'
    variant?: 'pill' | 'icon' | 'switch'
}>(), {
    size: 'md',
    variant: 'pill',
})

const { appearance, resolvedAppearance, updateAppearance, isMounted } = useAppearance()

// Safe resolved value: always 'light' during SSR/hydration, real value after mount
const safeResolved = computed(() => isMounted.value ? resolvedAppearance.value : 'light')

function cycle() {
    const modes = ['light', 'dark', 'system'] as const
    const current = modes.indexOf(appearance.value as typeof modes[number])
    const next = modes[(current + 1) % modes.length]
    updateAppearance(next)
}

function toggle() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark')
}

const sizeClasses = {
    sm: 'w-8 h-8 text-sm',
    md: 'w-9 h-9 text-base',
    lg: 'w-10 h-10 text-lg',
}
</script>

<template>
    <!-- Pill variant: shows current mode with nice toggle -->
    <button
        v-if="variant === 'pill'"
        @click="toggle"
        class="relative flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-300 group"
        :class="safeResolved === 'dark'
            ? 'bg-tn-surface hover:bg-tn-surface-hover text-tn-text border border-tn-border'
            : 'bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200/60'"
        :title="safeResolved === 'dark' ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
    >
        <div class="relative w-5 h-5 overflow-hidden">
            <!-- Sun -->
            <Transition
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 rotate-90 scale-0"
                enter-to-class="opacity-100 rotate-0 scale-100"
                leave-active-class="transition-all duration-300"
                leave-from-class="opacity-100 rotate-0 scale-100"
                leave-to-class="opacity-0 -rotate-90 scale-0"
            >
                <svg v-if="safeResolved === 'light'" class="w-5 h-5 absolute inset-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </Transition>
            <!-- Moon -->
            <Transition
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 -rotate-90 scale-0"
                enter-to-class="opacity-100 rotate-0 scale-100"
                leave-active-class="transition-all duration-300"
                leave-from-class="opacity-100 rotate-0 scale-100"
                leave-to-class="opacity-0 rotate-90 scale-0"
            >
                <svg v-if="safeResolved === 'dark'" class="w-5 h-5 absolute inset-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </Transition>
        </div>
        <span class="hidden sm:inline text-xs">{{ safeResolved === 'dark' ? 'Gelap' : 'Terang' }}</span>
    </button>

    <!-- Icon variant: compact circle button -->
    <button
        v-else-if="variant === 'icon'"
        @click="toggle"
        :class="[
            sizeClasses[size],
            'rounded-xl flex items-center justify-center transition-all duration-300',
            safeResolved === 'dark'
                ? 'bg-tn-surface hover:bg-tn-surface-hover text-indigo-300 border border-tn-border'
                : 'bg-gray-100 hover:bg-gray-200 text-amber-500 border border-gray-200/60'
        ]"
        :title="safeResolved === 'dark' ? 'Mode terang' : 'Mode gelap'"
    >
        <svg v-if="safeResolved === 'light'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <!-- Switch variant: iOS-style toggle -->
    <button
        v-else-if="variant === 'switch'"
        @click="toggle"
        class="relative inline-flex h-7 w-13 items-center rounded-full transition-colors duration-300 focus:outline-none"
        :class="safeResolved === 'dark' ? 'bg-indigo-600' : 'bg-gray-300'"
        :title="safeResolved === 'dark' ? 'Mode terang' : 'Mode gelap'"
    >
        <span
            class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm transition-transform duration-300"
            :class="safeResolved === 'dark' ? 'translate-x-7' : 'translate-x-1'"
        >
            <svg v-if="safeResolved === 'light'" class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
        </span>
    </button>
</template>
