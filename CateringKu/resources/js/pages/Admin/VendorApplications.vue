<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { CheckCircle, XCircle, Clock, ExternalLink } from 'lucide-vue-next';

interface Application {
    id: number;
    vendor_name: string;
    description: string;
    address: string;
    city: string;
    province: string;
    phone: string;
    email: string;
    deposit_amount: string;
    payment_method: string|null;
    payment_proof: string|null;
    status: string;
    rejection_reason: string|null;
    created_at: string;
    reviewed_at: string|null;
    user: { id: number; name: string; email: string };
    reviewer: { name: string }|null;
}

const props = defineProps<{
    applications: { data: Application[]; meta: any };
    filter: string|null;
}>();

const rejectModal = ref<{ show: boolean; id: number; reason: string }>({ show: false, id: 0, reason: '' });
const detailModal = ref<{ show: boolean; app: Application|null }>({ show: false, app: null });

function approve(id: number) {
    if (!confirm('Setujui aplikasi vendor ini? User akan diupgrade ke role vendor.')) return;
    router.patch(route('admin.vendor-applications.approve', id));
}

function openReject(id: number) { rejectModal.value = { show: true, id, reason: '' }; }

function submitReject() {
    router.patch(route('admin.vendor-applications.reject', rejectModal.value.id),
        { rejection_reason: rejectModal.value.reason },
        { onSuccess: () => { rejectModal.value.show = false; } }
    );
}

function openDetail(app: Application) { detailModal.value = { show: true, app }; }

function fmt(v: string|number) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

const statusCfg: Record<string,{label:string;cls:string}> = {
    draft:           { label: 'Draft',             cls: 'gray' },
    submitted:       { label: 'Menunggu Review',   cls: 'yellow' },
    deposit_pending: { label: 'Verifikasi Deposit', cls: 'blue' },
    approved:        { label: 'Disetujui',          cls: 'green' },
    rejected:        { label: 'Ditolak',            cls: 'red' },
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Aplikasi Vendor', href: '/admin/vendor-applications' },
];
</script>

<template>
    <AdminLayout>
        <template #header>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Aplikasi Pendaftaran Vendor</h1>
        </template>
        <div class="va-page">
            <h1 class="page-title">Aplikasi Pendaftaran Vendor</h1>

            <div class="filter-bar">
                <Link href="/admin/vendor-applications" class="filter-btn" :class="{ active: !filter }">Semua</Link>
                <Link href="/admin/vendor-applications?status=submitted" class="filter-btn" :class="{ active: filter==='submitted' }">Menunggu Review</Link>
                <Link href="/admin/vendor-applications?status=deposit_pending" class="filter-btn" :class="{ active: filter==='deposit_pending' }">Verifikasi Deposit</Link>
                <Link href="/admin/vendor-applications?status=approved" class="filter-btn" :class="{ active: filter==='approved' }">Disetujui</Link>
                <Link href="/admin/vendor-applications?status=rejected" class="filter-btn" :class="{ active: filter==='rejected' }">Ditolak</Link>
            </div>

            <div class="list">
                <div v-for="app in applications.data" :key="app.id" class="app-card">
                    <div class="app-left">
                        <div class="app-header">
                            <div>
                                <p class="app-name">{{ app.vendor_name }}</p>
                                <p class="app-city">{{ app.city }}, {{ app.province }}</p>
                            </div>
                            <span class="status-badge" :class="statusCfg[app.status]?.cls ?? 'gray'">
                                <CheckCircle v-if="app.status==='approved'" :size="12"/>
                                <XCircle v-else-if="app.status==='rejected'" :size="12"/>
                                <Clock v-else :size="12"/>
                                {{ statusCfg[app.status]?.label ?? app.status }}
                            </span>
                        </div>
                        <div class="app-meta">
                            <span>👤 {{ app.user.name }}</span>
                            <span>📞 {{ app.phone }}</span>
                            <span>💰 {{ fmt(app.deposit_amount) }}</span>
                            <span v-if="app.payment_method">🏦 {{ app.payment_method }}</span>
                        </div>
                        <p class="app-desc">{{ app.description.slice(0, 120) }}{{ app.description.length > 120 ? '...' : '' }}</p>
                        <p v-if="app.rejection_reason" class="rejection-note">Alasan: {{ app.rejection_reason }}</p>
                    </div>
                    <div class="app-right">
                        <button class="btn-detail" @click="openDetail(app)">
                            <ExternalLink :size="14"/> Detail
                        </button>
                        <template v-if="app.status === 'submitted' || app.status === 'deposit_pending'">
                            <button class="btn-approve" @click="approve(app.id)"><CheckCircle :size="14"/> Setujui</button>
                            <button class="btn-reject" @click="openReject(app.id)"><XCircle :size="14"/> Tolak</button>
                        </template>
                    </div>
                </div>
                <div v-if="applications.data.length === 0" class="empty">Tidak ada aplikasi vendor</div>
            </div>
        </div>

        <!-- Detail Modal -->
        <Teleport to="body">
            <div v-if="detailModal.show" class="modal-overlay" @click.self="detailModal.show = false">
                <div class="modal-large">
                    <h3>Detail Aplikasi — {{ detailModal.app?.vendor_name }}</h3>
                    <div class="detail-grid" v-if="detailModal.app">
                        <div class="drow"><span>Pemohon</span><strong>{{ detailModal.app.user.name }} ({{ detailModal.app.user.email }})</strong></div>
                        <div class="drow"><span>Telepon</span><strong>{{ detailModal.app.phone }}</strong></div>
                        <div class="drow"><span>Email Usaha</span><strong>{{ detailModal.app.email }}</strong></div>
                        <div class="drow"><span>Alamat</span><strong>{{ detailModal.app.address }}</strong></div>
                        <div class="drow"><span>Kota</span><strong>{{ detailModal.app.city }}, {{ detailModal.app.province }}</strong></div>
                        <div class="drow"><span>Deposit</span><strong class="gold">{{ fmt(detailModal.app.deposit_amount) }}</strong></div>
                        <div class="drow"><span>Metode</span><strong>{{ detailModal.app.payment_method ?? '-' }}</strong></div>
                        <div class="drow full"><span>Deskripsi</span><p class="desc-full">{{ detailModal.app.description }}</p></div>
                        <div class="drow full" v-if="detailModal.app.payment_proof">
                            <span>Bukti Transfer</span>
                            <img :src="'/storage/' + detailModal.app.payment_proof" class="proof-img" alt="Bukti transfer"/>
                        </div>
                    </div>
                    <button class="btn-close" @click="detailModal.show = false">Tutup</button>
                </div>
            </div>
        </Teleport>

        <!-- Reject Modal -->
        <Teleport to="body">
            <div v-if="rejectModal.show" class="modal-overlay" @click.self="rejectModal.show = false">
                <div class="modal">
                    <h3>Tolak Aplikasi Vendor</h3>
                    <p class="modal-sub">Berikan alasan yang jelas agar pemohon bisa perbaiki</p>
                    <textarea v-model="rejectModal.reason" class="modal-textarea" rows="4" placeholder="Alasan penolakan..."></textarea>
                    <div class="modal-actions">
                        <button class="btn-cancel" @click="rejectModal.show = false">Batal</button>
                        <button class="btn-confirm-reject" @click="submitReject" :disabled="!rejectModal.reason.trim()">Konfirmasi Tolak</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<style scoped>
.va-page { padding: 24px; max-width: 960px; margin: 0 auto; }
.page-title { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 20px; }
.filter-bar { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.filter-btn { padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; }
.filter-btn.active { background: #667eea; border-color: #667eea; color: #fff; }
.list { display: flex; flex-direction: column; gap: 12px; }
.app-card { background: #1e293b; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); padding: 20px; display: flex; gap: 16px; }
@media(max-width:640px){ .app-card { flex-direction: column; } }
.app-left { flex: 1; display: flex; flex-direction: column; gap: 10px; }
.app-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.app-name { font-size: 16px; font-weight: 700; color: #fff; }
.app-city { font-size: 13px; color: #94a3b8; }
.status-badge { display: flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.status-badge.yellow { background: rgba(251,191,36,.15); color: #fbbf24; }
.status-badge.green  { background: rgba(52,211,153,.15);  color: #34d399; }
.status-badge.red    { background: rgba(248,113,113,.15); color: #f87171; }
.status-badge.blue   { background: rgba(96,165,250,.15);  color: #60a5fa; }
.status-badge.gray   { background: rgba(255,255,255,.08); color: #94a3b8; }
.app-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #94a3b8; }
.app-desc { font-size: 13px; color: #64748b; line-height: 1.5; }
.rejection-note { font-size: 13px; color: #fbbf24; font-style: italic; }
.app-right { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }
.btn-detail  { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; cursor: pointer; transition: .2s; }
.btn-detail:hover { background: rgba(255,255,255,.1); }
.btn-approve { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: rgba(52,211,153,.15); border: 1px solid rgba(52,211,153,.3); color: #34d399; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-approve:hover { background: rgba(52,211,153,.25); }
.btn-reject  { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: rgba(248,113,113,.15); border: 1px solid rgba(248,113,113,.3); color: #f87171; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-reject:hover { background: rgba(248,113,113,.25); }
.empty { text-align: center; color: #64748b; padding: 48px; background: #1e293b; border-radius: 14px; }
/* Modals */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.75); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal, .modal-large { background: #1e293b; border-radius: 18px; padding: 28px; width: 100%; border: 1px solid rgba(255,255,255,.1); }
.modal { max-width: 440px; }
.modal-large { max-width: 680px; max-height: 85vh; overflow-y: auto; }
.modal h3, .modal-large h3 { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 20px; }
.modal-sub { font-size: 13px; color: #94a3b8; margin-bottom: 14px; }
.modal-textarea { width: 100%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 10px; padding: 12px; color: #fff; font-size: 14px; outline: none; resize: vertical; font-family: inherit; box-sizing: border-box; }
.modal-textarea:focus { border-color: #667eea; }
.modal-actions { display: flex; gap: 10px; margin-top: 16px; justify-content: flex-end; }
.btn-cancel { padding: 10px 20px; border-radius: 9px; border: 1px solid rgba(255,255,255,.1); background: transparent; color: #94a3b8; font-size: 14px; cursor: pointer; }
.btn-confirm-reject { padding: 10px 20px; border-radius: 9px; background: #ef4444; border: none; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-confirm-reject:disabled { opacity: .5; cursor: not-allowed; }
.detail-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }
.drow { display: flex; flex-direction: column; gap: 4px; background: rgba(255,255,255,.04); border-radius: 8px; padding: 12px; }
.drow.full { grid-column: 1/-1; }
.drow span { font-size: 12px; color: #64748b; }
.drow strong { font-size: 14px; color: #e2e8f0; }
.gold { color: #fbbf24 !important; }
.desc-full { font-size: 14px; color: #e2e8f0; line-height: 1.6; margin: 0; }
.proof-img { max-width: 100%; max-height: 300px; border-radius: 10px; object-fit: contain; margin-top: 8px; }
.btn-close { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,.1); background: transparent; color: #94a3b8; font-size: 14px; cursor: pointer; }
</style>
