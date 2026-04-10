<script setup lang="ts">
import { useForm, Link, Head } from '@inertiajs/vue3'

defineOptions({ layout: null as any })

const form = useForm({ email: '', password: '', remember: false })

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
    <Head title="Masuk - CateringKu" />
    <div class="min-h-screen flex bg-white">
        <!-- Left - Branding -->
        <div class="hidden lg:flex lg:w-1/2 gradient-hero items-center justify-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-24 -mt-24 animate-float"></div>
            <div class="absolute bottom-0 left-0 w-56 h-56 bg-white/10 rounded-full -ml-20 -mb-20"></div>
            <div class="text-center relative z-10 px-12">
                <img src="/images/logo.svg" alt="CateringKu" class="w-32 h-32 mx-auto mb-8 rounded-3xl shadow-2xl" />
                <h2 class="text-4xl font-bold text-white mb-4">Selamat Datang</h2>
                <p class="text-orange-100 text-lg leading-relaxed">Masuk untuk mulai memesan katering terbaik untuk acara Anda.</p>
            </div>
        </div>

        <!-- Right - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex items-center gap-2 mb-8 justify-center">
                    <img src="/images/logo.svg" alt="CateringKu" class="h-12 w-12 rounded-xl" />
                    <span class="text-2xl font-bold text-ck-primary">CateringKu</span>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">Masuk</h1>
                <p class="text-gray-500 mb-8">Masukkan email dan password untuk melanjutkan</p>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input v-model="form.email" type="email" required autofocus class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="email@contoh.com" />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input v-model="form.password" type="password" required class="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-ck-primary focus:border-ck-primary" placeholder="••••••••" />
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-ck-primary focus:ring-ck-primary" />
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full bg-ck-primary hover:bg-ck-primary-dark text-white py-3.5 rounded-xl font-semibold transition-colors disabled:opacity-50 text-lg">
                        {{ form.processing ? 'Memproses...' : 'Masuk' }}
                    </button>
                </form>

                <p class="text-center mt-6 text-gray-500">
                    Belum punya akun?
                    <Link href="/register" class="text-ck-primary hover:text-ck-primary-dark font-semibold">Daftar Sekarang</Link>
                </p>
            </div>
        </div>
    </div>
</template>