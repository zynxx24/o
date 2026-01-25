<?php
use App\Config\Session;
use App\Middleware\Security;

Session::start();
$isLoggedIn = Session::isLoggedIn();
$user = Session::user();
$cartCount = $cartItemCount ?? 0;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Platform katering online terpercaya untuk segala kebutuhan acara Anda. Pesan katering prasmanan, nasi kotak, dan snack box dengan mudah.">
    <title>Katering Online - Pesan Katering Terbaik</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'gradient': 'gradient 8s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                    },
                    backgroundSize: {
                        '300%': '300%',
                    },
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth theme transition */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.15s ease;
        }

        /* Glassmorphism */
        .glass {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        /* Hide scrollbar for categories */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316, #ef4444, #f97316);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 3s ease infinite;
        }

        /* Card hover effect */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        .dark .card-hover:hover {
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.3), 0 8px 10px -6px rgb(0 0 0 / 0.2);
        }

        /* Button glow effect */
        .btn-glow {
            position: relative;
            overflow: hidden;
        }

        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-glow:hover::before {
            left: 100%;
        }

        /* Dark mode toggle animation */
        .theme-toggle svg {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
    </style>
    <script>
        // Check theme on page load
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen flex flex-col transition-colors">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Navbar -->
    <nav
        class="glass bg-white/80 dark:bg-gray-900/80 border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold tracking-tight">
                        <span class="gradient-text">Katering</span><span
                            class="text-gray-800 dark:text-white">Online</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="/"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-lg font-medium transition-colors">Beranda</a>
                    <a href="/search"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-lg font-medium transition-colors">Cari
                        Vendor</a>
                    <a href="/about"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-lg font-medium transition-colors">Tentang</a>
                    <a href="/contact"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-lg font-medium transition-colors">Kontak</a>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-2">
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()"
                        class="theme-toggle p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                        aria-label="Toggle dark mode">
                        <!-- Sun Icon (visible in dark mode) -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <!-- Moon Icon (visible in light mode) -->
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                    </button>

                    <!-- Search Button (Mobile) -->
                    <a href="/search"
                        class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </a>

                    <?php if ($isLoggedIn): ?>
                        <!-- Cart -->
                        <a href="/cart"
                            class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <?php if ($cartCount > 0): ?>
                                <span
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold shadow-lg">
                                    <?php echo $cartCount > 9 ? '9+' : $cartCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button onclick="toggleUserMenu()"
                                class="flex items-center space-x-2 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-orange-400 to-red-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                    <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 hidden md:block" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="userMenu"
                                class="hidden absolute right-0 mt-2 w-56 glass-card bg-white/90 dark:bg-gray-800/90 rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 py-2 backdrop-blur-xl">
                                <div class="px-4 py-3 border-b border-gray-200/50 dark:border-gray-700/50">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <?php echo Security::e($user['name'] ?? 'User'); ?>
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo Security::e($user['email'] ?? ''); ?>
                                    </p>
                                </div>
                                <a href="/profile"
                                    class="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profil Saya
                                </a>
                                <a href="/orders"
                                    class="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    Pesanan Saya
                                </a>
                                <?php if (($user['role'] ?? '') === 'vendor'): ?>
                                    <a href="/vendor/dashboard"
                                        class="flex items-center px-4 py-2.5 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/30 font-medium transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        Vendor Dashboard
                                    </a>
                                <?php endif; ?>
                                <?php if (($user['role'] ?? '') === 'admin'): ?>
                                    <a href="/admin"
                                        class="flex items-center px-4 py-2.5 text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/30 font-medium transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Admin Dashboard
                                    </a>
                                <?php endif; ?>
                                <div class="border-t border-gray-200/50 dark:border-gray-700/50 my-2"></div>
                                <form action="/logout" method="POST" class="m-0">
                                    <?php echo Security::csrfField(); ?>
                                    <button type="submit"
                                        class="w-full flex items-center px-4 py-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login"
                            class="hidden md:inline-flex text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 font-medium transition-colors">Masuk</a>
                        <a href="/register"
                            class="btn-glow bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-500/25">
                            Daftar
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()"
                        class="md:hidden p-2 text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                        <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu"
            class="hidden md:hidden border-t border-gray-200/50 dark:border-gray-700/50 bg-white/95 dark:bg-gray-900/95">
            <div class="px-4 py-3 space-y-1">
                <a href="/"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl font-medium transition-colors">Beranda</a>
                <a href="/search"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl font-medium transition-colors">Cari
                    Vendor</a>
                <a href="/about"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl font-medium transition-colors">Tentang</a>
                <a href="/contact"
                    class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl font-medium transition-colors">Kontak</a>
                <?php if (!$isLoggedIn): ?>
                    <div class="pt-3 border-t border-gray-200/50 dark:border-gray-700/50">
                        <a href="/login"
                            class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-orange-900/30 hover:text-orange-600 dark:hover:text-orange-400 rounded-xl font-medium transition-colors">Masuk</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <script>
            // Theme toggle function
            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            }

            function toggleMobileMenu() {
                const menu = document.getElementById('mobileMenu');
                const menuIcon = document.getElementById('menuIcon');
                const closeIcon = document.getElementById('closeIcon');
                menu.classList.toggle('hidden');
                menuIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            }

            function toggleUserMenu() {
                const menu = document.getElementById('userMenu');
                menu.classList.toggle('hidden');
            }

            // Close user menu when clicking outside
            document.addEventListener('click', function (event) {
                const userMenu = document.getElementById('userMenu');
                const userButton = event.target.closest('button');
                if (userMenu && !userMenu.contains(event.target) && (!userButton || !userButton.onclick?.toString().includes('toggleUserMenu'))) {
                    userMenu.classList.add('hidden');
                }
            });

            // Toast notification function
            function showToast(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                const colors = {
                    success: 'bg-gradient-to-r from-green-500 to-emerald-500',
                    error: 'bg-gradient-to-r from-red-500 to-rose-500',
                    warning: 'bg-gradient-to-r from-yellow-500 to-amber-500',
                    info: 'bg-gradient-to-r from-blue-500 to-cyan-500'
                };

                toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-xl transform transition-all duration-300 translate-x-full backdrop-blur-sm`;
                toast.textContent = message;
                container.appendChild(toast);

                setTimeout(() => toast.classList.remove('translate-x-full'), 100);
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        </script>