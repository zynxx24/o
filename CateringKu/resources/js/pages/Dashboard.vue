<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3'
import { onMounted } from 'vue'

// This page should redirect based on user role
// It's only reached if someone navigates directly to /dashboard
onMounted(() => {
    const user = usePage().props.auth?.user as any
    if (user?.role === 'admin') {
        router.visit('/admin')
    } else if (user?.role === 'vendor') {
        router.visit('/vendor-panel')
    } else {
        router.visit('/orders')
    }
})
</script>

<template>
    <Head title="Dashboard - CateringKu" />
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center">
            <div class="w-10 h-10 border-4 border-ck-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500 text-sm">Mengalihkan ke dashboard...</p>
        </div>
    </div>
</template>
