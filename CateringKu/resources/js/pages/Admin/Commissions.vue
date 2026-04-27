<script setup lang="ts">
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { PieChart, TrendingUp, Settings } from 'lucide-vue-next';

interface Commission {
    id: number;
    gross_amount: string;
    tax_amount: string;
    platform_amount: string;
    vendor_amount: string;
    tax_rate: string;
    platform_rate: string;
    status: 'pending'|'distributed';
    distributed_at: string|null;
    created_at: string;
    order: {
        order_number: string;
        total_amount: string;
        user: { name: string };
        vendor: { vendor_name: string };
    };
}
interface Summary { totalGross: number; totalTax: number; totalPlatform: number; totalVendor: number; }
interface Rule { id: number; tax_rate: string; platform_rate: string; }

const props = defineProps<{
    commissions: { data: Commission[]; meta?: any; last_page?: number; links?: any[] };
    summary: Summary;
    rule: Rule;
}>();

const showRuleEdit = ref(false);
const ruleForm = reactive({ tax_rate: props.rule.tax_rate, platform_rate: props.rule.platform_rate });

function saveRule() {
    router.patch(route('admin.commissions.rules.update'), ruleForm, {
        onSuccess: () => { showRuleEdit.value = false; },
    });
}

function fmt(v: number|string) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }
function pct(v: string|number) { return Number(v).toFixed(2) + '%'; }

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Komisi', href: '/admin/commissions' },
];
</script>

<template>
    <AdminLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Dashboard Komisi</h1>
        </template>
        <div class="com-page">
            <div class="page-header">
                <h1 class="page-title">Dashboard Komisi</h1>
                <button class="btn-edit-rule" @click="showRuleEdit = !showRuleEdit">
                    <Settings :size="15"/> Aturan Komisi
                </button>
            </div>

            <!-- Rule Editor -->
            <div v-if="showRuleEdit" class="rule-editor">
                <h3>⚙️ Aturan Split Komisi</h3>
                <div class="rule-preview">
                    <div class="rule-item">
                        <label>Pajak Negara (PPN) %</label>
                        <input v-model="ruleForm.tax_rate" type="number" step="0.01" min="0" max="50" class="rule-input"/>
                    </div>
                    <div class="rule-item">
                        <label>Fee Platform % (dari net setelah pajak)</label>
                        <input v-model="ruleForm.platform_rate" type="number" step="0.01" min="0" max="50" class="rule-input"/>
                    </div>
                    <div class="rule-item">
                        <label>Payout Vendor %</label>
                        <p class="rule-calc">{{ (100 - Number(ruleForm.platform_rate)).toFixed(2) }}% dari net setelah pajak</p>
                    </div>
                </div>
                <div class="rule-example">
                    <p class="example-title">Simulasi untuk order Rp 1.000.000:</p>
                    <div class="ex-row"><span>Pajak ({{ pct(ruleForm.tax_rate) }})</span><strong>{{ fmt(Math.round(1000000 * Number(ruleForm.tax_rate) / 100)) }}</strong></div>
                    <div class="ex-row">
                        <span>Fee Platform ({{ pct(ruleForm.platform_rate) }} dari net)</span>
                        <strong>{{ fmt(Math.round((1000000 - 1000000 * Number(ruleForm.tax_rate) / 100) * Number(ruleForm.platform_rate) / 100)) }}</strong>
                    </div>
                    <div class="ex-row">
                        <span>Payout Vendor</span>
                        <strong class="gold">{{
                            fmt(Math.round(
                                (1000000 - 1000000 * Number(ruleForm.tax_rate) / 100) *
                                (1 - Number(ruleForm.platform_rate) / 100)
                            ))
                        }}</strong>
                    </div>
                </div>
                <div class="rule-actions">
                    <button class="btn-cancel" @click="showRuleEdit = false">Batal</button>
                    <button class="btn-save" @click="saveRule">Simpan Aturan</button>
                </div>
            </div>

            <!-- Summary -->
            <div class="summary-grid">
                <div class="sum-card total">
                    <PieChart :size="20"/>
                    <div>
                        <p class="sum-label">Total Gross</p>
                        <p class="sum-val">{{ fmt(summary.totalGross) }}</p>
                    </div>
                </div>
                <div class="sum-card tax">
                    <div class="sum-icon">🏛️</div>
                    <div>
                        <p class="sum-label">Pajak Negara</p>
                        <p class="sum-val">{{ fmt(summary.totalTax) }}</p>
                    </div>
                </div>
                <div class="sum-card platform">
                    <TrendingUp :size="20"/>
                    <div>
                        <p class="sum-label">Fee Platform</p>
                        <p class="sum-val">{{ fmt(summary.totalPlatform) }}</p>
                    </div>
                </div>
                <div class="sum-card vendor">
                    <div class="sum-icon">🏪</div>
                    <div>
                        <p class="sum-label">Payout Vendor</p>
                        <p class="sum-val">{{ fmt(summary.totalVendor) }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Rule Banner -->
            <div class="active-rule">
                <span>📐 Aturan aktif:</span>
                <span class="rule-tag">PPN {{ pct(rule.tax_rate) }}</span>
                <span class="rule-tag">Platform {{ pct(rule.platform_rate) }}</span>
                <span class="rule-tag">Vendor {{ (100 - Number(rule.platform_rate)).toFixed(2) }}%</span>
            </div>

            <!-- Table -->
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Vendor</th>
                            <th>Customer</th>
                            <th class="right">Gross</th>
                            <th class="right">Pajak</th>
                            <th class="right">Platform</th>
                            <th class="right">Vendor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in commissions.data" :key="c.id">
                            <td class="order-num">{{ c.order.order_number }}</td>
                            <td>{{ c.order.vendor?.vendor_name ?? '-' }}</td>
                            <td>{{ c.order.user?.name ?? '-' }}</td>
                            <td class="right mono">{{ fmt(c.gross_amount) }}</td>
                            <td class="right mono tax">{{ fmt(c.tax_amount) }}</td>
                            <td class="right mono platform">{{ fmt(c.platform_amount) }}</td>
                            <td class="right mono vendor">{{ fmt(c.vendor_amount) }}</td>
                            <td>
                                <span class="badge" :class="c.status === 'distributed' ? 'distributed' : 'pending'">
                                    {{ c.status === 'distributed' ? 'Distribusi' : 'Menunggu' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="commissions.data.length === 0">
                            <td colspan="8" class="empty">Belum ada komisi yang dicatat</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination" v-if="(commissions.meta?.last_page ?? commissions.last_page ?? 0) > 1">
                <Link v-for="link in (commissions.meta?.links ?? commissions.links ?? [])" :key="link.label"
                    :href="link.url ?? '#'" class="page-link"
                    :class="{ active: link.active, disabled: !link.url }"
                    v-html="link.label"/>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.com-page { padding: 24px; max-width: 1200px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 22px; font-weight: 800; color: #fff; }
.btn-edit-rule { display: flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); color: #94a3b8; font-size: 13px; cursor: pointer; transition: .2s; }
.btn-edit-rule:hover { background: rgba(255,255,255,.1); color: #fff; }
/* Rule Editor */
.rule-editor { background: #1e293b; border-radius: 16px; border: 1px solid rgba(102,126,234,.3); padding: 24px; margin-bottom: 24px; }
.rule-editor h3 { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 20px; }
.rule-preview { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 20px; }
@media(max-width:640px){ .rule-preview { grid-template-columns: 1fr; } }
.rule-item { display: flex; flex-direction: column; gap: 6px; }
.rule-item label { font-size: 12px; color: #94a3b8; }
.rule-input { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); border-radius: 8px; padding: 10px; color: #fff; font-size: 16px; font-weight: 700; width: 100%; outline: none; }
.rule-input:focus { border-color: #667eea; }
.rule-calc { font-size: 16px; font-weight: 700; color: #34d399; margin: 0; }
.rule-example { background: rgba(255,255,255,.04); border-radius: 10px; padding: 16px; margin-bottom: 16px; }
.example-title { font-size: 12px; color: #64748b; margin-bottom: 10px; }
.ex-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px; }
.ex-row span { color: #94a3b8; }
.ex-row strong { color: #e2e8f0; }
.gold { color: #fbbf24 !important; }
.rule-actions { display: flex; gap: 10px; justify-content: flex-end; }
.btn-cancel { padding: 10px 20px; border-radius: 9px; border: 1px solid rgba(255,255,255,.1); background: transparent; color: #94a3b8; font-size: 14px; cursor: pointer; }
.btn-save { padding: 10px 24px; border-radius: 9px; background: linear-gradient(135deg,#667eea,#764ba2); border: none; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
/* Summary */
.summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
@media(max-width:768px){ .summary-grid { grid-template-columns: 1fr 1fr; } }
.sum-card { display: flex; align-items: center; gap: 14px; padding: 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); }
.sum-card.total    { background: linear-gradient(135deg,rgba(102,126,234,.2),rgba(118,75,162,.2)); color: #a78bfa; }
.sum-card.tax      { background: linear-gradient(135deg,rgba(248,113,113,.2),rgba(239,68,68,.2));  color: #f87171; }
.sum-card.platform { background: linear-gradient(135deg,rgba(96,165,250,.2),rgba(59,130,246,.2));  color: #60a5fa; }
.sum-card.vendor   { background: linear-gradient(135deg,rgba(52,211,153,.2),rgba(16,185,129,.2));  color: #34d399; }
.sum-icon { font-size: 20px; }
.sum-label { font-size: 12px; color: #94a3b8; margin-bottom: 4px; }
.sum-val { font-size: 16px; font-weight: 700; }
/* Active Rule */
.active-rule { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; font-size: 13px; color: #94a3b8; }
.rule-tag { padding: 4px 12px; background: rgba(102,126,234,.15); border: 1px solid rgba(102,126,234,.3); border-radius: 20px; color: #818cf8; font-size: 12px; font-weight: 600; }
/* Table */
.table-wrap { background: #1e293b; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); overflow: auto; }
.data-table { width: 100%; border-collapse: collapse; min-width: 800px; }
.data-table th { padding: 13px 14px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid rgba(255,255,255,.06); background: rgba(255,255,255,.02); }
.data-table td { padding: 13px 14px; border-bottom: 1px solid rgba(255,255,255,.04); font-size: 13px; color: #e2e8f0; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,.02); }
.right { text-align: right; }
.mono { font-variant-numeric: tabular-nums; }
.order-num { font-family: monospace; font-size: 13px; color: #818cf8; }
.tax      { color: #f87171; }
.platform { color: #60a5fa; }
.vendor   { color: #34d399; }
.badge { padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge.distributed { background: rgba(52,211,153,.15); color: #34d399; }
.badge.pending     { background: rgba(251,191,36,.15);  color: #fbbf24; }
.empty { text-align: center; color: #64748b; padding: 40px; }
.pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
.page-link { padding: 7px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; }
.page-link.active { background: #667eea; border-color: #667eea; color: #fff; }
.page-link.disabled { opacity: .4; pointer-events: none; }
</style>
