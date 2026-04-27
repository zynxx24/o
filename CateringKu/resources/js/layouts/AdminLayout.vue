<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import DarkModeToggle from '@/components/DarkModeToggle.vue'

const page = usePage()
const user = computed(() => (page.props as any).auth?.user)
const stats = computed(() => (page.props as any).stats || {})

const sidebarCollapsed = ref(false)
const mobileMenuOpen = ref(false)

const navItems = [
    { name: 'Dashboard', href: '/admin', icon: 'dashboard', exact: true },
    { name: 'Pesanan', href: '/admin/orders', icon: 'orders', badge: () => stats.value.pendingOrders || 0 },
    { name: 'Pesan', href: '/admin/messages', icon: 'messages', badge: () => stats.value.unreadMessages || 0 },
    { name: 'Kelola Dompet', href: '/admin/wallets', icon: 'wallet' },
    { name: 'Dompet Saya', href: '/wallet', icon: 'my-wallet' },
    { name: 'Penarikan', href: '/admin/withdrawals', icon: 'withdrawal', badge: () => stats.value.pendingWithdrawals || 0 },
    { name: 'Aplikasi Vendor', href: '/admin/vendor-applications', icon: 'vendor-app', badge: () => stats.value.pendingVendorApps || 0 },
    { name: 'Komisi', href: '/admin/commissions', icon: 'commission' },
]

function isActive(item: { href: string, exact?: boolean }) {
    if (item.exact) return page.url === item.href
    return page.url.startsWith(item.href)
}

function closeMobileMenu() {
    mobileMenuOpen.value = false
}

function handleResize() {
    if (window.innerWidth >= 1024) mobileMenuOpen.value = false
}

onMounted(() => {
    window.addEventListener('resize', handleResize)
    const saved = localStorage.getItem('admin-sidebar-collapsed')
    if (saved !== null) sidebarCollapsed.value = saved === 'true'
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})

function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
    localStorage.setItem('admin-sidebar-collapsed', String(sidebarCollapsed.value))
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 dark:from-[#13132a] dark:via-[#111128] dark:to-[#0f0f22] flex transition-colors duration-300">
        <!-- Mobile overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
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
                'bg-gradient-to-b from-[#0f172a] via-[#0c1426] to-[#070d1a]',
                sidebarCollapsed ? 'lg:w-[76px]' : 'lg:w-[260px]',
                mobileMenuOpen ? 'w-[280px] translate-x-0' : 'w-[280px] -translate-x-full lg:translate-x-0',
            ]"
        >
            <!-- Logo Section -->
            <div class="flex items-center h-16 px-4 border-b border-white/[0.06]">
                <Link href="/admin" class="flex items-center gap-3 group min-w-0">
                    <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-red-500/20 shrink-0 group-hover:scale-105 transition-transform">
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
                            <span class="text-[10px] text-red-300/50 font-medium uppercase tracking-widest">Admin Panel</span>
                        </div>
                    </Transition>
                    <div class="min-w-0 lg:hidden">
                        <span class="text-white font-bold text-base tracking-tight block">CateringKu</span>
                        <span class="text-[10px] text-red-300/50 font-medium uppercase tracking-widest">Admin Panel</span>
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
                    <div v-if="isActive(item)" class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 bg-gradient-to-b from-red-400 to-rose-500 rounded-r-full"></div>

                    <!-- Icons -->
                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200', isActive(item) ? 'bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-md shadow-red-500/30' : 'bg-white/[0.06] text-white/60 group-hover:bg-white/[0.1] group-hover:text-white/80']">
                        <svg v-if="item.icon === 'dashboard'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <svg v-else-if="item.icon === 'orders'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <svg v-else-if="item.icon === 'messages'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <svg v-else-if="item.icon === 'wallet'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <svg v-else-if="item.icon === 'my-wallet'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg v-else-if="item.icon === 'withdrawal'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <svg v-else-if="item.icon === 'vendor-app'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <svg v-else-if="item.icon === 'commission'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>

                    <span v-if="!sidebarCollapsed" class="hidden lg:inline truncate flex-1">{{ item.name }}</span>
                    <span class="lg:hidden truncate flex-1">{{ item.name }}</span>

                    <!-- Badge -->
                    <span v-if="item.badge && item.badge() > 0 && (!sidebarCollapsed || !true)" class="px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-md min-w-[20px] text-center">
                        {{ item.badge() }}
                    </span>
                </Link>

                <div class="!my-4 border-t border-white/[0.06]"></div>

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
                    <div class="w-9 h-9 bg-gradient-to-br from-red-500/80 to-rose-600/80 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                    </div>
                    <div v-if="!sidebarCollapsed" class="min-w-0 flex-1 hidden lg:block">
                        <p class="text-sm font-medium text-white/80 truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/30 truncate">Administrator</p>
                    </div>
                    <div class="min-w-0 flex-1 lg:hidden">
                        <p class="text-sm font-medium text-white/80 truncate">{{ user?.name }}</p>
                        <p class="text-[11px] text-white/30 truncate">Administrator</p>
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
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <button @click="toggleSidebar" class="hidden lg:flex p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </button>

                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

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
