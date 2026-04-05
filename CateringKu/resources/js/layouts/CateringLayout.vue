<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const cartCount = computed(() => page.props.cartCount ?? 0)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)

const navLinks = [
    { name: 'Beranda', href: '/', routeName: 'home' },
    { name: 'Cari Vendor', href: '/search', routeName: 'search' },
    { name: 'Tentang', href: '/about', routeName: 'about' },
    { name: 'Kontak', href: '/contact', routeName: 'contact' },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center gap-2">
                            <img src="/images/logo.svg" alt="CateringKu" class="h-10 w-10 rounded-lg" />
                            <span class="text-xl font-bold text-ck-primary hidden sm:block">CateringKu</span>
                        </Link>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="link.href"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="$page.url.startsWith(link.href) && link.href !== '/' || $page.url === link.href
                                ? 'text-ck-primary bg-ck-primary/5'
                                : 'text-gray-600 hover:text-ck-primary hover:bg-gray-50'"
                        >
                            {{ link.name }}
                        </Link>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-3">
                        <!-- Cart -->
                        <Link
                            v-if="user"
                            href="/cart"
                            class="relative p-2 text-gray-500 hover:text-ck-primary transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-1 -right-1 w-5 h-5 bg-ck-primary text-white text-xs flex items-center justify-center rounded-full font-bold"
                            >
                                {{ cartCount }}
                            </span>
                        </Link>

                        <!-- User Menu -->
                        <template v-if="user">
                            <div class="relative">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    <div class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-full flex items-center justify-center text-sm font-bold">
                                        {{ user.name?.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-gray-700">{{ user.name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div
                                    v-if="userMenuOpen"
                                    @click="userMenuOpen = false"
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                                >
                                    <Link href="/orders" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Pesanan Saya</Link>
                                    <Link href="/dashboard" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Dashboard</Link>
                                    <Link href="/settings/profile" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</Link>
                                    <hr class="my-1 border-gray-100">
                                    <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Keluar</Link>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-ck-primary transition-colors">Masuk</Link>
                            <Link href="/register" class="text-sm font-medium text-white bg-ck-primary hover:bg-ck-primary-dark px-4 py-2 rounded-lg transition-colors">Daftar</Link>
                        </template>

                        <!-- Mobile menu button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <div v-if="mobileMenuOpen" class="md:hidden py-3 border-t border-gray-100">
                    <Link
                        v-for="link in navLinks"
                        :key="link.routeName"
                        :href="link.href"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-ck-primary"
                    >
                        {{ link.name }}
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Click overlay to close user menu -->
        <div v-if="userMenuOpen" @click="userMenuOpen = false" class="fixed inset-0 z-40" />

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ $page.props.flash.success }}
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 mb-4">
                            <img src="/images/logo.svg" alt="CateringKu" class="h-10 w-10 rounded-lg" />
                            <span class="text-xl font-bold text-ck-primary">CateringKu</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed max-w-md">
                            Platform pemesanan katering online terlengkap di Indonesia. Temukan vendor terpercaya untuk segala acara Anda.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Menu</h4>
                        <div class="space-y-2">
                            <Link v-for="link in navLinks" :key="link.routeName" :href="link.href" class="block text-sm text-gray-500 hover:text-ck-primary transition-colors">{{ link.name }}</Link>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Kontak</h4>
                        <div class="space-y-2 text-sm text-gray-500">
                            <p>📧 info@cateringku.com</p>
                            <p>📞 (021) 1234-5678</p>
                            <p>📍 Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-400">
                    &copy; {{ new Date().getFullYear() }} CateringKu. Semua hak dilindungi.
                </div>
            </div>
        </footer>
    </div>
</template>
