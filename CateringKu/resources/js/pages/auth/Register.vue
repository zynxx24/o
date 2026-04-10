<script setup lang="ts">
import { useForm, Link, Head } from '@inertiajs/vue3'

defineOptions({ layout: null as any })

const form = useForm({
    name: '', username: '', email: '', password: '', password_confirmation: '',
})

function submit() {
    form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') })
}
</script>

<template>
    <Head title="Daftar - CateringKu" />
    <div class="min-h-screen flex">
        <!-- Left - Branding -->
        <div class="hidden lg:flex lg:w-1/2 gradient-hero items-center justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-24 -mt-24 animate-float"></div>
            <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/10 rounded-full -ml-20 -mb-20"></div>
            <div class="text-center relative z-10 px-12">
                <img src="/images/logo.svg" alt="CateringKu" class="w-32 h-32 mx-auto mb-8 rounded-3xl shadow-2xl" />
                <h2 class="text-4xl font-bold text-white mb-4">Bergabung Bersama</h2>
                <p class="text-orange-100 text-lg leading-relaxed">Daftar sekarang dan nikmati kemudahan memesan katering online.</p>
            </div>
        </div>

        <!-- Right - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex items-center gap-2 mb-8 justify-center">
                    <img src="/images/logo.svg" alt="CateringKu" class="h-12 w-12 rounded-xl" />
                    <span class="text-2xl font-bold text-ck-primary">CateringKu</span>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Akun</h1>
                <p class="text-gray-500 mb-8">Buat akun baru untuk mulai memesan</p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input v-model="form.name" type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="Nama lengkap" />
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <input v-model="form.username" type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="username" />
                        <p v-if="form.errors.username" class="text-red-500 text-xs mt-1">{{ form.errors.username }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input v-model="form.email" type="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="email@contoh.com" />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input v-model="form.password" type="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="Min. 8 karakter" />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                        <input v-model="form.password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="Ulangi password" />
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full bg-ck-primary hover:bg-ck-primary-dark text-white py-3.5 rounded-xl font-semibold transition-colors disabled:opacity-50 text-lg">
                        {{ form.processing ? 'Memproses...' : 'Daftar' }}
                    </button>
                </form>

                <p class="text-center mt-6 text-gray-500">
                    Sudah punya akun?
                    <Link href="/login" class="text-ck-primary hover:text-ck-primary-dark font-semibold">Masuk</Link>
                </p>
            </div>
        </div>
    </div>
</template>
