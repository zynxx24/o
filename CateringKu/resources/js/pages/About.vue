<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <CateringLayout>
        <Head title="Tentang Kami — CateringKu" />
        <!-- Hero -->
        <div class="gradient-hero rounded-3xl p-8 md:p-12 mb-12 text-white text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-16 -mt-16 animate-float"></div>
            <div class="relative z-10">
                <h1 class="text-4xl font-bold mb-3">Tentang CateringKu</h1>
                <p class="text-orange-100 text-lg max-w-2xl mx-auto">Platform pemesanan katering online terlengkap di Indonesia</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div v-for="stat in [{n:'500+',l:'Vendor Aktif'},{n:'10.000+',l:'Pesanan Selesai'},{n:'50+',l:'Kota'},{n:'4.8',l:'Rating Rata-rata'}]" :key="stat.l" class="bg-white rounded-2xl p-6 border border-gray-100 text-center card-hover">
                <p class="text-3xl font-bold text-ck-primary mb-1">{{ stat.n }}</p>
                <p class="text-sm text-gray-500">{{ stat.l }}</p>
            </div>
        </div>

        <!-- Story -->
        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Cerita Kami</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                CateringKu didirikan dengan visi mempermudah masyarakat Indonesia dalam memesan katering untuk berbagai acara.
                Kami percaya bahwa setiap momen spesial layak mendapatkan hidangan terbaik tanpa repot.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Dengan platform kami, Anda dapat menemukan vendor katering terpercaya, membandingkan menu dan harga,
                serta melakukan pemesanan dalam hitungan menit. Kami menghubungkan ribuan pelanggan dengan vendor katering
                terbaik di seluruh Indonesia.
            </p>
        </div>

        <!-- Values -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Mengapa Memilih Kami?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="v in [{i:'🤝',t:'Terpercaya',d:'Semua vendor melalui verifikasi ketat dan review dari pelanggan nyata.'},{i:'⚡',t:'Mudah & Cepat',d:'Pesan dalam hitungan menit. Proses simpel dari pencarian hingga pembayaran.'},{i:'💰',t:'Harga Transparan',d:'Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga yang Anda bayar.'}]" :key="v.t" class="bg-white rounded-2xl p-6 border border-gray-100 text-center card-hover">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl mx-auto mb-4 flex items-center justify-center text-3xl">{{ v.i }}</div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ v.t }}</h3>
                    <p class="text-gray-500 text-sm">{{ v.d }}</p>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
