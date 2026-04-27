<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import DarkModeToggle from '@/components/DarkModeToggle.vue'

const page = usePage()
const user = computed(() => (page.props as any).auth?.user)
const vendor = computed(() => (page.props as any).vendor)

const sidebarCollapsed = ref(false)
const mobileMenuOpen = ref(false)

const navItems = [
    { name: 'Dashboard', href: '/vendor-panel', icon: 'dashboard', exact: true },
    { name: 'Pesanan', href: '/vendor-panel/orders', icon: 'orders' },
    { name: 'Menu', href: '/vendor-panel/menu', icon: 'menu' },
    { name: 'Ulasan', href: '/vendor-panel/reviews', icon: 'reviews' },
    { name: 'Dompet', href: '/wallet', icon: 'wallet' },
]

function isActive(item: { href: string, exact?: boolean }) {
    if (item.exact) return page.url === item.href
    return page.url.startsWith(item.href)
}

function closeMobileMenu() {
    mobileMenuOpen.value = false
}

// Close sidebar on resize
function handleResize() {
    if (window.innerWidth >= 1024) {
        mobileMenuOpen.value = false
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize)
    // Load sidebar state from localStorage
    const saved = localStorage.getItem('vendor-sidebar-collapsed')
    if (saved !== null) sidebarCollapsed.value = saved === 'true'
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})

function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
    localStorage.setItem('vendor-sidebar-collapsed', String(sidebarCollapsed.value))
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 dark:from-[#13132a] dark:via-[#111128] dark:to-[#0f0f22] flex transition-colors duration-300">
        <!-- Mobile overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="mobileMenuOpen" @click="closeMobileMenu" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed lg:sticky top-0 h-screen z-50 flex flex-col transition-all duration-300 ease-in-out',
                'bg-gradient-to-b from-[#1a1a2e] via-[#16162a] to-[#0f0f1a]',
                sidebarCollapsed ? 'lg:w-[76px]' : 'lg:w-[260px]',
                mobileMenuOpen ? 'w-[280px] translate-x-0' : 'w-[280px] -translate-x-full lg:translate-x-0',
            ]"
        >
            <!-- Logo Section -->
            <div class="flex items-center h-16 px-4 border-b border-white/[0.06]">
                <Link href="/vendor-panel" class="flex items-center gap-3 group min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-ck-primary to-ck-coral rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-ck-primary/20 shrink-0 group-hover:scale-105 transition-transform">
                        CK
                    </div>
                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0 -translate-x-2"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition-all duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="!sidebarCollapsed" class="min-w-0 hidden lg:block">
                            <span class="text-white font-bold text-base tracking-tight block">CateringKu</span>
                            <span class="text-[10px] text-white/40 font-medium uppercase tracking-widest">Vendor Panel</span>
                        </div>
                    </Transition>
                    <!-- Always show on mobile -->
                    <div class="min-w-0 lg:hidden">
                        <span class="text-white font-bold text-base tracking-tight block">CateringKu</span>
                        <span class="text-[10px] text-white/40 font-medium uppercase tracking-widest">Vendor Panel</span>
                    </div>
                </Link>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    @click="closeMobileMenu"
                    :class="[
                        'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 relative',
                        isActive(item)
                            ? 'bg-white/[0.12] text-white shadow-sm'
                            : 'text-white/50 hover:text-white/90 hover:bg-white/[0.06]',
                    ]"
                >
                    <!-- Active indicator -->
                    <div v-if="isActive(item)" class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 bg-gradient-to-b from-ck-primary to-ck-coral rounded-r-full"></div>

                    <!-- Icons -->
                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200', isActive(item) ? 'bg-gradient-to-br from-ck-primary to-ck-coral text-white shadow-md shadow-ck-primary/30' : 'bg-white/[0.06] text-white/60 group-hover:bg-white/[0.1] group-hover:text-white/80']">
                        <!-- Dashboard -->
                        <svg v-if="item.icon === 'dashboard'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <!-- Orders -->
                        <svg v-else-if="item.icon === 'orders'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <!-- Menu -->
                        <svg v-else-if="item.icon === 'menu'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <!-- Reviews -->
                        <svg v-else-if="item.icon === 'reviews'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <!-- Wallet -->
                        <svg v-else-if="item.icon === 'wallet'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>

                    <Transition
                        enter-active-class="transition-all duration-200"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition-all duration-150"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <span v-if="!sidebarCollapsed" class="hidden lg:inline truncate">{{ item.name }}</span>
                    </Transition>
                    <span class="lg:hidden truncate">{{ item.name }}</span>
                </Link>

                <!-- Divider -->
                <div class="!my-4 border-t border-white/[0.06]"></div>

                <!-- External links -->
                <Link href="/" @click="closeMobileMenu" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-white/40 hover:text-white/70 hover:bg-white/[0.04] transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-white/[0.04] text-white/40 group-hover:text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <span v-if="!sidebarCollapsed" class="hidden lg:inline">Lihat Website</span>
                    <span class="lg:hidden">Lihat Website</span>
                </Link>
            </nav>

            <!-- User Section -->
            <div class="border-t border-white/[0.06] p-3">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/[0.04]">
                    <div class="w-9 h-9 bg-gradient-to-br from-ck-primary/80 to-ck-coral/80 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() || 'V' }}
                    </div>
                    <div v-if="!sidebarCollapsed" class="min-w-0 flex-1 hidden lg:block">
                        <p class="text-sm font-medium text-white/80 truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/30 truncate">Vendor</p>
                    </div>
                    <div class="min-w-0 flex-1 lg:hidden">
                        <p class="text-sm font-medium text-white/80 truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/30 truncate">Vendor</p>
                    </div>
                    <Link href="/logout" method="post" as="button" class="shrink-0 p-1.5 text-white/30 hover:text-red-400 rounded-lg hover:bg-white/[0.06] transition-all" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-[#1a1b2e]/80 backdrop-blur-xl border-b border-gray-100/80 dark:border-[#2a2c45] h-16 flex items-center px-4 sm:px-6 lg:px-8 gap-4">
                <!-- Mobile menu toggle -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Sidebar toggle (desktop) -->
                <button @click="toggleSidebar" class="hidden lg:flex p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </button>

                <!-- Page title slot area -->
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

                <!-- Right side actions -->
                <div class="flex items-center gap-2">
                    <DarkModeToggle variant="icon" size="sm" />
                    <Link href="/" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-ck-primary hover:bg-ck-primary/5 rounded-lg transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Website
                    </Link>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
