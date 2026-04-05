<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Kebijakan Privasi — CateringKu" />
    <CateringLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">🔒</div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Kebijakan Privasi</h1>
                <p class="text-gray-500">Terakhir diperbarui: 1 April 2026</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 md:p-10 space-y-8 prose prose-gray max-w-none">
                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                        Informasi yang Kami Kumpulkan
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Kami mengumpulkan informasi berikut saat Anda menggunakan layanan CateringKu:</p>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li><strong>Data Pribadi</strong>: Nama lengkap, alamat email, nomor telepon, dan alamat.</li>
                        <li><strong>Data Transaksi</strong>: Riwayat pesanan, metode pembayaran, dan preferensi menu.</li>
                        <li><strong>Data Teknis</strong>: Alamat IP, jenis browser, perangkat, dan cookie.</li>
                        <li><strong>Data Lokasi</strong>: Kota dan wilayah untuk mencocokkan vendor terdekat.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                        Penggunaan Informasi
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Informasi yang kami kumpulkan digunakan untuk:</p>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Memproses dan mengirimkan pesanan Anda.</li>
                        <li>Komunikasi terkait pesanan, promo, dan pembaruan layanan.</li>
                        <li>Meningkatkan kualitas layanan dan pengalaman pengguna.</li>
                        <li>Memenuhi kewajiban hukum dan peraturan yang berlaku.</li>
                        <li>Mendeteksi dan mencegah aktivitas penipuan.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                        Perlindungan Data
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Kami menerapkan langkah-langkah keamanan teknis dan organisasional yang sesuai untuk melindungi data pribadi Anda,
                        termasuk enkripsi SSL/TLS, pembatasan akses, dan audit keamanan secara berkala.
                        Data sensitif seperti informasi pembayaran tidak pernah disimpan di server kami.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">4</span>
                        Berbagi Data dengan Pihak Ketiga
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Kami hanya membagikan data Anda kepada:</p>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li><strong>Vendor</strong>: Informasi pesanan yang diperlukan untuk memenuhi pesanan.</li>
                        <li><strong>Payment Gateway</strong>: Untuk memproses pembayaran secara aman.</li>
                        <li><strong>Pihak Berwajib</strong>: Jika diwajibkan oleh hukum atau perintah pengadilan.</li>
                    </ul>
                    <p class="text-gray-600 leading-relaxed mt-3">
                        Kami <strong>tidak pernah</strong> menjual data pribadi Anda kepada pihak ketiga.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">5</span>
                        Hak Pengguna
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">Anda memiliki hak untuk:</p>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Mengakses dan mengunduh data pribadi Anda.</li>
                        <li>Memperbarui atau mengoreksi data yang tidak akurat.</li>
                        <li>Menghapus akun dan data pribadi Anda.</li>
                        <li>Menolak pengiriman materi pemasaran.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">6</span>
                        Cookie
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Kami menggunakan cookie untuk meningkatkan pengalaman pengguna, menganalisis trafik, dan mempersonalisasi konten.
                        Anda dapat mengelola preferensi cookie melalui pengaturan browser Anda.
                        Cookie esensial diperlukan untuk fungsi dasar situs dan tidak dapat dinonaktifkan.
                    </p>
                </section>

                <div class="pt-4 border-t border-gray-100 text-sm text-gray-400 text-center">
                    Pertanyaan tentang kebijakan privasi? Hubungi kami di
                    <a href="mailto:privacy@cateringku.com" class="text-ck-primary hover:underline">privacy@cateringku.com</a>.
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
