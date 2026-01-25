<?php include 'layout/header.php'; ?>

<div class="min-h-[70vh] flex items-center justify-center">
    <div class="text-center px-4">
        <!-- 404 Illustration -->
        <div class="mb-8 relative">
            <div class="text-9xl font-bold gradient-text opacity-30 select-none">404</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-full flex items-center justify-center animate-float">
                    <svg class="w-16 h-16 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Halaman Tidak Ditemukan</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman telah dipindahkan atau dihapus.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/"
                class="btn-glow inline-flex items-center bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-600 transition-all shadow-lg shadow-orange-500/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Kembali ke Beranda
            </a>
            <a href="/contact"
                class="inline-flex items-center text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 px-6 py-3 font-medium transition-colors">
                Hubungi Kami
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </a>
        </div>

        <!-- Quick Links -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-gray-500 dark:text-gray-400 mb-4">Atau coba halaman berikut:</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/"
                    class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Beranda</a>
                <span class="text-gray-300 dark:text-gray-600">•</span>
                <a href="/search"
                    class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Cari
                    Vendor</a>
                <span class="text-gray-300 dark:text-gray-600">•</span>
                <a href="/about"
                    class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Tentang
                    Kami</a>
                <span class="text-gray-300 dark:text-gray-600">•</span>
                <a href="/contact"
                    class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Kontak</a>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>