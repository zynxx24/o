<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Wallet, ArrowDownLeft, ArrowUpRight, Clock, CheckCircle, XCircle, TrendingUp } from 'lucide-vue-next';

interface WalletData {
    wallet_id: number;
    balance: string;
    frozen_balance: string;
    total_earned: string;
    total_withdrawn: string;
}
interface Transaction {
    id: number;
    type: 'credit' | 'debit';
    category: string;
    amount: string;
    balance_after: string;
    description: string;
    created_at: string;
}
interface WithdrawalRequest {
    id: number;
    amount: string;
    bank_name: string;
    bank_account: string;
    account_name: string;
    status: 'pending' | 'approved' | 'rejected';
    admin_note: string | null;
    created_at: string;
}

const props = defineProps<{
    wallet: WalletData;
    transactions: { data: Transaction[]; meta: any };
    pendingWithdrawal: WithdrawalRequest | null;
    withdrawalHistory: WithdrawalRequest[];
}>();

const showWithdrawModal = ref(false);
const form = useForm({
    amount: '',
    bank_name: '',
    bank_account: '',
    account_name: '',
});

const balance = computed(() => parseFloat(props.wallet.balance));
const frozenBalance = computed(() => parseFloat(props.wallet.frozen_balance));
const totalEarned = computed(() => parseFloat(props.wallet.total_earned));
const totalWithdrawn = computed(() => parseFloat(props.wallet.total_withdrawn));

function fmt(val: number | string) {
    return 'Rp ' + Number(val).toLocaleString('id-ID');
}

function categoryLabel(cat: string) {
    const map: Record<string, string> = {
        commission: 'Komisi Order',
        withdrawal: 'Penarikan Dana',
        deposit: 'Deposit Vendor',
        deposit_refund: 'Pengembalian Deposit',
        platform_fee: 'Fee Platform',
        fee: 'Fee Platform',
        tax: 'Pajak',
        adjustment: 'Penyesuaian',
    };
    return map[cat] ?? cat;
}

function submitWithdraw() {
    form.post(route('wallet.withdraw'), {
        onSuccess: () => {
            showWithdrawModal.value = false;
            form.reset();
        },
    });
}

// Combine transactions + withdrawal history into one unified timeline
const unifiedHistory = computed(() => {
    const items: { id: string; type: 'tx' | 'wd'; date: Date; data: any }[] = [];

    for (const tx of props.transactions.data) {
        items.push({ id: `tx-${tx.id}`, type: 'tx', date: new Date(tx.created_at), data: tx });
    }
    for (const wd of props.withdrawalHistory) {
        items.push({ id: `wd-${wd.id}`, type: 'wd', date: new Date(wd.created_at), data: wd });
    }

    return items.sort((a, b) => b.date.getTime() - a.date.getTime());
});

const breadcrumbs = [{ title: 'Dompet', href: '/wallet' }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

            <!-- ── Two Cards Row ───────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Saldo Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-200/30 dark:shadow-indigo-900/20">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/3"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <Wallet :size="22" />
                            </div>
                            <p class="text-sm font-medium text-white/80">Saldo Tersedia</p>
                        </div>
                        <p class="text-3xl font-extrabold tracking-tight mb-1">{{ fmt(wallet.balance) }}</p>
                        <div class="flex items-center gap-4 text-xs text-white/70 mt-2">
                            <span v-if="frozenBalance > 0">🔒 Deposit: {{ fmt(wallet.frozen_balance) }}</span>
                            <span>↗ Ditarik: {{ fmt(wallet.total_withdrawn) }}</span>
                        </div>
                        <div class="mt-4">
                            <button
                                v-if="!pendingWithdrawal"
                                class="flex items-center gap-2 bg-white/20 hover:bg-white/30 border border-white/30 rounded-xl px-5 py-2.5 text-sm font-semibold transition-colors backdrop-blur-sm"
                                @click="showWithdrawModal = true"
                                :disabled="balance < 50000"
                            >
                                <ArrowUpRight :size="16" /> Tarik Dana
                            </button>
                            <div v-else class="flex items-center gap-2 text-xs text-white/85 bg-white/15 rounded-xl px-4 py-2.5">
                                <Clock :size="14" /> Penarikan sedang diproses
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Earned Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 rounded-2xl p-6 text-white shadow-xl shadow-emerald-200/30 dark:shadow-emerald-900/20">
                    <div class="absolute bottom-0 left-0 w-36 h-36 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <TrendingUp :size="22" />
                            </div>
                            <p class="text-sm font-medium text-white/80">Total Penerimaan</p>
                        </div>
                        <p class="text-3xl font-extrabold tracking-tight mb-1">{{ fmt(wallet.total_earned) }}</p>
                        <p class="text-xs text-white/70 mt-2">Akumulasi sepanjang waktu</p>
                    </div>
                </div>
            </div>

            <!-- ── Pending Withdrawal Alert ───────────── -->
            <div v-if="pendingWithdrawal" class="bg-amber-50 dark:bg-amber-900/15 border border-amber-200 dark:border-amber-800/30 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-800/30 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                        <Clock :size="20" />
                    </div>
                    <div>
                        <p class="font-bold text-amber-900 dark:text-amber-300 text-sm">Penarikan Menunggu Persetujuan</p>
                        <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">
                            {{ fmt(pendingWithdrawal.amount) }} → {{ pendingWithdrawal.bank_name }} · {{ pendingWithdrawal.bank_account }}
                        </p>
                        <p class="text-xs text-amber-500 dark:text-amber-600 mt-0.5">
                            Diajukan: {{ new Date(pendingWithdrawal.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── Unified Activity History ──────────── -->
            <div class="bg-white dark:bg-[#1f2037] rounded-2xl border border-gray-100/80 dark:border-[#2a2c45] overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-[#2a2c45]">
                    <h2 class="font-bold text-gray-900 dark:text-gray-100 text-lg">Aktivitas Dompet</h2>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Transaksi dan penarikan dana</p>
                </div>

                <div v-if="unifiedHistory.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                    <Wallet :size="48" class="mb-3 opacity-30" />
                    <p class="text-sm">Belum ada aktivitas</p>
                </div>

                <div v-else class="divide-y divide-gray-50 dark:divide-[#2a2c45]">
                    <!-- Transaction Item -->
                    <template v-for="item in unifiedHistory" :key="item.id">
                        <div v-if="item.type === 'tx'" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                            <div :class="[
                                'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
                                item.data.type === 'credit'
                                    ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 dark:text-emerald-400'
                                    : 'bg-rose-50 dark:bg-rose-900/20 text-rose-500 dark:text-rose-400'
                            ]">
                                <ArrowDownLeft v-if="item.data.type === 'credit'" :size="18" />
                                <ArrowUpRight v-else :size="18" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ item.data.description }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ categoryLabel(item.data.category) }} · {{ new Date(item.data.created_at).toLocaleDateString('id-ID') }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p :class="[
                                    'text-sm font-bold',
                                    item.data.type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                                ]">
                                    {{ item.data.type === 'credit' ? '+' : '-' }}{{ fmt(item.data.amount) }}
                                </p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Saldo: {{ fmt(item.data.balance_after) }}</p>
                            </div>
                        </div>

                        <!-- Withdrawal Item -->
                        <div v-else class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-violet-50 dark:bg-violet-900/20 text-violet-500 dark:text-violet-400">
                                <ArrowUpRight :size="18" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Penarikan ke {{ item.data.bank_name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ item.data.bank_account }} · {{ new Date(item.data.created_at).toLocaleDateString('id-ID') }}</p>
                                <p v-if="item.data.admin_note" class="text-xs text-amber-500 dark:text-amber-400 mt-1 italic">{{ item.data.admin_note }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ fmt(item.data.amount) }}</p>
                                <span :class="[
                                    'inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full mt-1',
                                    item.data.status === 'approved' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' :
                                    item.data.status === 'rejected' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400' :
                                    'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'
                                ]">
                                    <CheckCircle v-if="item.data.status === 'approved'" :size="12" />
                                    <XCircle v-else-if="item.data.status === 'rejected'" :size="12" />
                                    <Clock v-else :size="12" />
                                    {{ item.data.status === 'approved' ? 'Disetujui' : item.data.status === 'rejected' ? 'Ditolak' : 'Menunggu' }}
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ── Modal Tarik Dana ───────────────────────── -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showWithdrawModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[1000] flex items-center justify-center p-4" @click.self="showWithdrawModal = false">
                    <div class="bg-white dark:bg-[#1f2037] rounded-2xl p-7 w-full max-w-md border border-gray-100 dark:border-[#2a2c45] shadow-2xl">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-1">Tarik Dana</h3>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">Saldo tersedia: <strong class="text-gray-700 dark:text-gray-300">{{ fmt(wallet.balance) }}</strong></p>

                        <form @submit.prevent="submitWithdraw" class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block">Jumlah Penarikan (min. Rp 50.000)</label>
                                <input
                                    v-model="form.amount"
                                    type="number"
                                    min="50000"
                                    :max="balance"
                                    placeholder="Masukkan jumlah"
                                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-[#2a2c45] rounded-xl px-4 py-3 text-gray-800 dark:text-gray-200 text-sm outline-none focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/30 transition-all"
                                    required
                                />
                                <p v-if="form.errors.amount" class="text-xs text-rose-500 mt-1">{{ form.errors.amount }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block">Nama Bank</label>
                                <input v-model="form.bank_name" type="text" placeholder="BCA, BNI, Mandiri, dll" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-[#2a2c45] rounded-xl px-4 py-3 text-gray-800 dark:text-gray-200 text-sm outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all" required />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block">Nomor Rekening</label>
                                <input v-model="form.bank_account" type="text" placeholder="1234567890" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-[#2a2c45] rounded-xl px-4 py-3 text-gray-800 dark:text-gray-200 text-sm outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all" required />
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 block">Nama Pemilik Rekening</label>
                                <input v-model="form.account_name" type="text" placeholder="Sesuai buku tabungan" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-[#2a2c45] rounded-xl px-4 py-3 text-gray-800 dark:text-gray-200 text-sm outline-none focus:border-indigo-400 dark:focus:border-indigo-500 transition-all" required />
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="showWithdrawModal = false" class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-[#2a2c45] text-gray-500 dark:text-gray-400 text-sm font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" :disabled="form.processing" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold shadow-lg shadow-indigo-200/50 dark:shadow-indigo-900/30 hover:shadow-xl transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                    {{ form.processing ? 'Memproses...' : 'Ajukan Penarikan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>
