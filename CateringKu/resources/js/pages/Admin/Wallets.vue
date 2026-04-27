<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Wallet, TrendingUp, ArrowUpRight, Snowflake, User } from 'lucide-vue-next';

interface WalletRow {
    wallet_id: number;
    balance: string;
    frozen_balance: string;
    total_earned: string;
    total_withdrawn: string;
    user: { id: number; name: string; email: string; role: string };
}
interface Summary { totalBalance: number; totalFrozen: number; totalEarned: number; totalWithdrawn: number; }

const props = defineProps<{
    wallets: { data: WalletRow[]; meta?: any; links?: any; last_page?: number };
    summary: Summary;
    filter: string | null;
}>();

function fmt(v: number | string) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

function roleColor(r: string) {
    return r === 'admin' ? 'badge-admin' : r === 'vendor' ? 'badge-vendor' : 'badge-customer';
}

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Dompet', href: '/admin/wallets' },
];
</script>

<template>
    <AdminLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Manajemen Dompet</h1>
        </template>
        <div class="aw-page">
            <h1 class="page-title">Manajemen Dompet</h1>

            <!-- Summary -->
            <div class="summary-grid">
                <div class="sum-card purple">
                    <Wallet :size="20"/>
                    <div>
                        <p class="sum-label">Total Saldo Aktif</p>
                        <p class="sum-val">{{ fmt(summary.totalBalance) }}</p>
                    </div>
                </div>
                <div class="sum-card blue">
                    <Snowflake :size="20"/>
                    <div>
                        <p class="sum-label">Total Deposit Beku</p>
                        <p class="sum-val">{{ fmt(summary.totalFrozen) }}</p>
                    </div>
                </div>
                <div class="sum-card green">
                    <TrendingUp :size="20"/>
                    <div>
                        <p class="sum-label">Total Penerimaan</p>
                        <p class="sum-val">{{ fmt(summary.totalEarned) }}</p>
                    </div>
                </div>
                <div class="sum-card orange">
                    <ArrowUpRight :size="20"/>
                    <div>
                        <p class="sum-label">Total Ditarik</p>
                        <p class="sum-val">{{ fmt(summary.totalWithdrawn) }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="filter-bar">
                <Link href="/admin/wallets" class="filter-btn" :class="{ active: !filter }">Semua</Link>
                <Link href="/admin/wallets?role=vendor" class="filter-btn" :class="{ active: filter === 'vendor' }">Vendor</Link>
                <Link href="/admin/wallets?role=admin" class="filter-btn" :class="{ active: filter === 'admin' }">Admin</Link>
            </div>

            <!-- Table -->
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th class="right">Saldo Tersedia</th>
                            <th class="right">Deposit Beku</th>
                            <th class="right">Total Diterima</th>
                            <th class="right">Total Ditarik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="w in wallets.data" :key="w.wallet_id">
                            <td>
                                <div class="user-cell">
                                    <div class="avatar">{{ w.user.name[0] }}</div>
                                    <div>
                                        <p class="user-name">{{ w.user.name }}</p>
                                        <p class="user-email">{{ w.user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge" :class="roleColor(w.user.role)">{{ w.user.role }}</span></td>
                            <td class="right mono">{{ fmt(w.balance) }}</td>
                            <td class="right mono frozen">{{ fmt(w.frozen_balance) }}</td>
                            <td class="right mono earned">{{ fmt(w.total_earned) }}</td>
                            <td class="right mono">{{ fmt(w.total_withdrawn) }}</td>
                        </tr>
                        <tr v-if="wallets.data.length === 0">
                            <td colspan="6" class="empty">Tidak ada dompet ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination" v-if="(wallets.meta?.last_page ?? wallets.last_page ?? 0) > 1">
                <Link
                    v-for="link in (wallets.meta?.links ?? wallets.links ?? [])"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    class="page-link"
                    :class="{ active: link.active, disabled: !link.url }"
                    v-html="link.label"
                />
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.aw-page { padding: 24px; max-width: 1200px; margin: 0 auto; }
.page-title { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 24px; }
.summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
@media(max-width:768px){ .summary-grid{ grid-template-columns:1fr 1fr; } }
.sum-card { display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); }
.sum-card.purple { background: linear-gradient(135deg,rgba(102,126,234,.2),rgba(118,75,162,.2)); color: #a78bfa; }
.sum-card.blue   { background: linear-gradient(135deg,rgba(96,165,250,.2),rgba(59,130,246,.2));  color: #60a5fa; }
.sum-card.green  { background: linear-gradient(135deg,rgba(52,211,153,.2),rgba(16,185,129,.2));  color: #34d399; }
.sum-card.orange { background: linear-gradient(135deg,rgba(251,191,36,.2),rgba(245,158,11,.2));  color: #fbbf24; }
.sum-label { font-size: 12px; color: #94a3b8; margin-bottom: 4px; }
.sum-val { font-size: 16px; font-weight: 700; }
.filter-bar { display: flex; gap: 8px; margin-bottom: 20px; }
.filter-btn { padding: 8px 18px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; background: transparent; transition: .2s; }
.filter-btn.active, .filter-btn:hover { background: #667eea; border-color: #667eea; color: #fff; }
.table-wrap { background: #1e293b; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 14px 16px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid rgba(255,255,255,.06); background: rgba(255,255,255,.02); }
.data-table td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.04); font-size: 14px; color: #e2e8f0; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,.02); }
.right { text-align: right; }
.mono { font-variant-numeric: tabular-nums; }
.frozen { color: #60a5fa; }
.earned { color: #34d399; }
.user-cell { display: flex; align-items: center; gap: 10px; }
.avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #fff; flex-shrink: 0; }
.user-name { font-weight: 500; font-size: 14px; }
.user-email { font-size: 12px; color: #64748b; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-admin    { background: rgba(248,113,113,.15); color: #f87171; }
.badge-vendor   { background: rgba(102,126,234,.15); color: #818cf8; }
.badge-customer { background: rgba(52,211,153,.15);  color: #34d399; }
.empty { text-align: center; color: #64748b; padding: 40px; }
.pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
.page-link { padding: 7px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; }
.page-link.active { background: #667eea; border-color: #667eea; color: #fff; }
.page-link.disabled { opacity: .4; pointer-events: none; }
</style>
