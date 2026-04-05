<script setup lang="ts">
import { Link, usePage, Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const page = usePage()
const user = computed(() => (page.props as any).auth?.user)
const cartCount = computed(() => (page.props as any).cartCount ?? 0)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const scrolled = ref(false)

// Force light mode for public catering pages
onMounted(() => {
    // Remove dark class from html element when on catering pages
    // so the public layout always renders in light mode
})

const navLinks = [
    { name: 'Beranda', href: '/', routeName: 'home', icon: '🏠' },
    { name: 'Cari Vendor', href: '/search', routeName: 'search', icon: '🔍' },
    { name: 'Tentang', href: '/about', routeName: 'about', icon: 'ℹ️' },
    { name: 'Kontak', href: '/contact', routeName: 'contact', icon: '✉️' },
]

function isActive(link: { href: string }) {
    if (link.href === '/') return page.url === '/'
    return page.url.startsWith(link.href)
}

function handleScroll() {
    scrolled.value = window.scrollY > 10
}

function closeMenus() {
    userMenuOpen.value = false
    mobileMenuOpen.value = false
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
    document.addEventListener('click', (e: Event) => {
        const target = e.target as HTMLElement
        if (!target.closest('.user-menu-container')) {
            userMenuOpen.value = false
        }
    })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <!-- 'light' class ensures this layout always renders in light mode regardless of user's dark mode preference -->
    <div class="light min-h-screen flex flex-col" style="background: linear-gradient(to bottom, #f9fafb, #ffffff); color: #1f2937;">
        <!-- Navbar -->
        <nav
            :class="[
                'sticky top-0 z-50 transition-all duration-300',
                scrolled
                    ? 'shadow-lg border-b border-gray-100'
                    : 'border-b border-gray-100/50'
            ]"
            style="background: white;"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center gap-2.5 group">
                            <img
                                src="/images/logo.svg"
                                alt="CateringKu"
                                class="h-10 w-10 rounded-xl shadow-sm group-hover:shadow-md transition-shadow"
                            />
                            <span class="text-xl font-bold bg-gradient-to-r from-ck-primary to-ck-coral bg-clip-text text-transparent hidden sm:block">
                                CateringKu
                            </span>
                        </Link>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="link.href"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                            :class="isActive(link)
                                ? 'text-ck-primary bg-ck-primary/8 shadow-sm shadow-ck-primary/10'
                                : 'text-gray-600 hover:text-ck-primary hover:bg-ck-primary/5'"
                        >
                            {{ link.name }}
                        </Link>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-2">
                        <!-- Cart -->
                        <Link
                            v-if="user"
                            href="/cart"
                            class="relative p-2.5 text-gray-500 hover:text-ck-primary hover:bg-ck-primary/5 rounded-xl transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-gradient-to-br from-ck-primary to-ck-coral text-white text-[10px] flex items-center justify-center rounded-full font-bold shadow-lg shadow-ck-primary/30 animate-pulse"
                            >
                                {{ cartCount }}
                            </span>
                        </Link>

                        <!-- User Menu -->
                        <template v-if="user">
                            <div class="relative user-menu-container">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-50 transition-all"
                                >
                                    <div class="w-8 h-8 bg-gradient-to-br from-ck-primary to-ck-coral text-white rounded-xl flex items-center justify-center text-xs font-bold shadow-sm">
                                        {{ user.name?.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[100px] truncate">{{ user.name }}</span>
                                    <svg :class="['w-3.5 h-3.5 text-gray-400 transition-transform', userMenuOpen && 'rotate-180']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>

                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 scale-95 -translate-y-1"
                                    enter-to-class="opacity-100 scale-100 translate-y-0"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 scale-100 translate-y-0"
                                    leave-to-class="opacity-0 scale-95 -translate-y-1"
                                >
                                    <div
                                        v-if="userMenuOpen"
                                        class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl shadow-black/10 border border-gray-100 py-2 z-50 overflow-hidden"
                                    >
                                        <div class="px-4 py-2 border-b border-gray-100 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</p>
                                            <p class="text-xs text-gray-400 capitalize">{{ user.role || 'customer' }}</p>
                                        </div>
                                        <Link @click="closeMenus" href="/orders" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-ck-primary/5 hover:text-ck-primary transition-colors">
                                            <span class="text-base">📦</span> Pesanan Saya
                                        </Link>
                                        <Link @click="closeMenus" href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-ck-primary/5 hover:text-ck-primary transition-colors">
                                            <span class="text-base">👤</span> Profil
                                        </Link>
                                        <Link @click="closeMenus" href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-ck-primary/5 hover:text-ck-primary transition-colors">
                                            <span class="text-base">📊</span> Dashboard
                                        </Link>
                                        <Link @click="closeMenus" href="/settings/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-ck-primary/5 hover:text-ck-primary transition-colors">
                                            <span class="text-base">⚙️</span> Pengaturan
                                        </Link>
                                        <hr class="my-1 border-gray-100">
                                        <Link @click="closeMenus" href="/logout" method="post" as="button" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <span class="text-base">🚪</span> Keluar
                                        </Link>
                                    </div>
                                </Transition>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-ck-primary transition-colors px-3 py-2">
                                Masuk
                            </Link>
                            <Link href="/register" class="text-sm font-semibold text-white bg-gradient-to-r from-ck-primary to-ck-coral hover:shadow-lg hover:shadow-ck-primary/25 px-5 py-2.5 rounded-xl transition-all hover:-translate-y-0.5">
                                Daftar
                            </Link>
                        </template>

                        <!-- Mobile menu button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-500 hover:text-ck-primary rounded-xl hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-if="mobileMenuOpen" class="md:hidden py-3 space-y-1 border-t border-gray-100">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="link.href"
                            @click="mobileMenuOpen = false"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors',
                                isActive(link)
                                    ? 'text-ck-primary bg-ck-primary/5'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-ck-primary'
                            ]"
                        >
                            <span>{{ link.icon }}</span>
                            {{ link.name }}
                        </Link>
                    </div>
                </Transition>
            </div>
        </nav>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-3"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div v-if="($page.props as any).flash?.success || ($page.props as any).flash?.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div
                    v-if="($page.props as any).flash?.success"
                    class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm flex items-center gap-3 shadow-sm"
                >
                    <span class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">✓</span>
                    {{ ($page.props as any).flash.success }}
                </div>
                <div
                    v-if="($page.props as any).flash?.error"
                    class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm flex items-center gap-3 shadow-sm"
                >
                    <span class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center shrink-0">✕</span>
                    {{ ($page.props as any).flash.error }}
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 text-gray-300 mt-auto">
            <!-- CTA Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative -top-8">
                    <div class="bg-gradient-to-r from-ck-primary via-ck-coral to-ck-amber rounded-2xl p-8 md:p-10 text-white text-center shadow-2xl shadow-ck-primary/20 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white/5 rounded-full animate-float"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl md:text-3xl font-bold mb-3">Siap Memesan Katering? 🍽️</h3>
                            <p class="text-orange-100 mb-6 max-w-lg mx-auto">
                                Temukan vendor katering terbaik dan pesan dengan mudah untuk acara Anda.
                            </p>
                            <Link href="/search" class="inline-flex items-center gap-2 bg-white text-ck-primary font-bold px-8 py-3.5 rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5">
                                Mulai Pesan Sekarang
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust & Security Badge -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 mb-10">
                <div class="flex flex-wrap justify-center gap-6 md:gap-10">
                    <div class="flex items-center gap-2.5 text-gray-400">
                        <div class="w-10 h-10 bg-green-500/15 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-300">Dilindungi 100%</p>
                            <p class="text-xs text-gray-500">Keamanan Terjamin</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 text-gray-400">
                        <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-300">Data Terenkripsi</p>
                            <p class="text-xs text-gray-500">SSL/TLS Protected</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 text-gray-400">
                        <div class="w-10 h-10 bg-purple-500/15 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-300">Pembayaran Aman</p>
                            <p class="text-xs text-gray-500">Multi Payment Gateway</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12">
                    <!-- Brand -->
                    <div class="md:col-span-4">
                        <div class="flex items-center gap-2.5 mb-4">
                            <img src="/images/logo.svg" alt="CateringKu" class="h-10 w-10 rounded-xl" />
                            <span class="text-xl font-bold text-white">CateringKu</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-5">
                            Platform pemesanan katering online terlengkap di Indonesia.
                            Temukan vendor terpercaya untuk segala acara Anda — dari prasmanan, nasi kotak, hingga snack box.
                        </p>
                        <!-- Hashtags -->
                        <div class="flex flex-wrap gap-2 mb-5">
                            <span class="text-xs font-medium bg-ck-primary/15 text-ck-primary px-3 py-1.5 rounded-full hover:bg-ck-primary/25 transition-colors cursor-default">#CateringKu</span>
                            <span class="text-xs font-medium bg-ck-primary/15 text-ck-primary px-3 py-1.5 rounded-full hover:bg-ck-primary/25 transition-colors cursor-default">#KateringOnline</span>
                            <span class="text-xs font-medium bg-ck-primary/15 text-ck-primary px-3 py-1.5 rounded-full hover:bg-ck-primary/25 transition-colors cursor-default">#PesanKatering</span>
                            <span class="text-xs font-medium bg-ck-primary/15 text-ck-primary px-3 py-1.5 rounded-full hover:bg-ck-primary/25 transition-colors cursor-default">#NasiKotak</span>
                        </div>
                        <!-- Social Media -->
                        <div class="flex gap-3">
                            <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-ck-primary rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all text-sm" aria-label="Twitter">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-gradient-to-br hover:from-purple-500 hover:to-pink-500 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all text-sm" aria-label="Instagram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-green-500 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all text-sm" aria-label="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            </a>
                            <a href="#" class="w-9 h-9 bg-gray-800 hover:bg-blue-500 rounded-xl flex items-center justify-center text-gray-400 hover:text-white transition-all text-sm" aria-label="Telegram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Nav Links -->
                    <div class="md:col-span-2">
                        <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Menu</h4>
                        <div class="space-y-2.5">
                            <Link v-for="link in navLinks" :key="link.routeName" :href="link.href" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">
                                {{ link.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Layanan -->
                    <div class="md:col-span-3">
                        <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Layanan</h4>
                        <div class="space-y-2.5">
                            <Link href="/search?type=prasmanan" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">Prasmanan</Link>
                            <Link href="/search?type=nasi-kotak" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">Nasi Kotak</Link>
                            <Link href="/search?type=snack-box" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">Snack Box</Link>
                            <Link href="/search?type=wedding" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">Pernikahan</Link>
                            <Link href="/search?type=tumpeng" class="block text-sm text-gray-400 hover:text-ck-primary transition-colors">Tumpeng</Link>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="md:col-span-3">
                        <h4 class="font-semibold text-white mb-4 text-sm uppercase tracking-wider">Kontak</h4>
                        <div class="space-y-3 text-sm text-gray-400">
                            <div class="flex items-start gap-3">
                                <span class="text-base mt-0.5">📧</span>
                                <div>
                                    <p class="text-gray-300 font-medium">Email</p>
                                    <p>info@cateringku.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-base mt-0.5">📞</span>
                                <div>
                                    <p class="text-gray-300 font-medium">Telepon</p>
                                    <p>(021) 1234-5678</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-base mt-0.5">📍</span>
                                <div>
                                    <p class="text-gray-300 font-medium">Lokasi</p>
                                    <p>Jakarta, Indonesia</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-base mt-0.5">🕐</span>
                                <div>
                                    <p class="text-gray-300 font-medium">Jam Operasional</p>
                                    <p>Senin — Sabtu, 08:00 — 17:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-10 pt-6 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">
                        &copy; {{ new Date().getFullYear() }} CateringKu. Semua hak dilindungi.
                    </p>
                    <div class="flex items-center gap-6 text-sm text-gray-500">
                        <Link href="/terms" class="hover:text-gray-300 transition-colors">Syarat &amp; Ketentuan</Link>
                        <Link href="/privacy" class="hover:text-gray-300 transition-colors">Kebijakan Privasi</Link>
                        <span class="flex items-center gap-1.5 text-green-500/70">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                            Secure
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

