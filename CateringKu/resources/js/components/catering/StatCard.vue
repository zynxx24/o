<script setup lang="ts">
defineProps<{
    title: string
    value: string | number
    subtitle?: string
    icon?: string
    trend?: 'up' | 'down' | 'neutral'
    trendValue?: string
    color?: 'primary' | 'green' | 'blue' | 'purple' | 'amber'
}>()

const colorMap: Record<string, { bg: string; icon: string; trend: string }> = {
    primary: { bg: 'from-orange-100 to-orange-200', icon: 'text-ck-primary', trend: 'text-ck-primary' },
    green: { bg: 'from-green-100 to-emerald-200', icon: 'text-green-600', trend: 'text-green-600' },
    blue: { bg: 'from-blue-100 to-cyan-200', icon: 'text-blue-600', trend: 'text-blue-600' },
    purple: { bg: 'from-purple-100 to-violet-200', icon: 'text-purple-600', trend: 'text-purple-600' },
    amber: { bg: 'from-amber-100 to-yellow-200', icon: 'text-amber-600', trend: 'text-amber-600' },
}
</script>

<template>
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">{{ title }}</p>
                <p class="text-3xl font-bold text-gray-900">{{ value }}</p>
                <p v-if="subtitle" class="text-xs text-gray-400 mt-1">{{ subtitle }}</p>
            </div>
            <div
                v-if="icon"
                :class="[
                    'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl bg-gradient-to-br',
                    colorMap[color ?? 'primary']?.bg ?? colorMap.primary.bg,
                ]"
            >
                {{ icon }}
            </div>
        </div>

        <div v-if="trendValue" class="mt-3 flex items-center gap-1 text-xs font-medium">
            <svg v-if="trend === 'up'" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            <svg v-else-if="trend === 'down'" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
            <span :class="trend === 'up' ? 'text-green-600' : trend === 'down' ? 'text-red-600' : 'text-gray-500'">
                {{ trendValue }}
            </span>
        </div>
    </div>
</template>
