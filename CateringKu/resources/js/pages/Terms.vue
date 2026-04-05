<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Syarat & Ketentuan — CateringKu" />
    <CateringLayout>
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">📜</div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Syarat & Ketentuan</h1>
                <p class="text-gray-500">Terakhir diperbarui: 1 April 2026</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 md:p-10 space-y-8 prose prose-gray max-w-none">
                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                        Ketentuan Umum
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Dengan menggunakan platform CateringKu, Anda menyetujui semua syarat dan ketentuan yang berlaku.
                        CateringKu adalah platform marketplace yang menghubungkan pelanggan dengan penyedia jasa katering (vendor).
                        Kami hanya bertindak sebagai perantara dan tidak bertanggung jawab langsung atas kualitas makanan yang disediakan oleh vendor.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                        Akun Pengguna
                    </h2>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Pengguna wajib memberikan data yang benar dan akurat saat mendaftar.</li>
                        <li>Setiap pengguna bertanggung jawab atas keamanan akun masing-masing.</li>
                        <li>CateringKu berhak menonaktifkan akun yang melanggar ketentuan.</li>
                        <li>Satu orang hanya boleh memiliki satu akun aktif.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                        Pemesanan & Pembayaran
                    </h2>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Semua pesanan bersifat mengikat setelah pembayaran dikonfirmasi.</li>
                        <li>Harga yang tertera sudah termasuk pajak kecuali dinyatakan lain.</li>
                        <li>Pembayaran harus dilakukan dalam waktu 1x24 jam setelah pemesanan.</li>
                        <li>Pesanan yang tidak dibayar akan dibatalkan secara otomatis.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">4</span>
                        Pembatalan & Pengembalian Dana
                    </h2>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Pembatalan pesanan dapat dilakukan maksimal 48 jam sebelum tanggal acara.</li>
                        <li>Pengembalian dana akan diproses dalam 3-7 hari kerja.</li>
                        <li>Biaya administrasi sebesar 5% akan dikenakan untuk setiap pembatalan.</li>
                        <li>Pembatalan di luar batas waktu tidak dapat melakukan pengembalian dana.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">5</span>
                        Ketentuan Vendor
                    </h2>
                    <ul class="text-gray-600 space-y-2 list-disc list-inside">
                        <li>Vendor wajib memiliki izin usaha dan sertifikasi keamanan pangan yang berlaku.</li>
                        <li>Vendor bertanggung jawab atas kualitas, kebersihan, dan keamanan makanan.</li>
                        <li>CateringKu berhak menghentikan kerjasama dengan vendor yang melanggar ketentuan.</li>
                        <li>Komisi platform sebesar 10% dari setiap transaksi yang berhasil.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-ck-primary/10 text-ck-primary rounded-lg flex items-center justify-center text-sm font-bold">6</span>
                        Penyelesaian Sengketa
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Segala sengketa yang timbul dari penggunaan layanan CateringKu akan diselesaikan secara musyawarah.
                        Apabila tidak tercapai kesepakatan, sengketa akan diselesaikan melalui Badan Arbitrase Nasional Indonesia (BANI)
                        yang berkedudukan di Jakarta.
                    </p>
                </section>

                <div class="pt-4 border-t border-gray-100 text-sm text-gray-400 text-center">
                    Jika Anda memiliki pertanyaan terkait syarat dan ketentuan ini, silakan hubungi kami di
                    <a href="mailto:legal@cateringku.com" class="text-ck-primary hover:underline">legal@cateringku.com</a>.
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
