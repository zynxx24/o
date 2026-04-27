<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { CheckCircle, XCircle, Clock } from 'lucide-vue-next';

interface Withdrawal {
    id: number;
    amount: string;
    bank_name: string;
    bank_account: string;
    account_name: string;
    status: 'pending'|'approved'|'rejected';
    admin_note: string|null;
    created_at: string;
    wallet: { user: { id: number; name: string; email: string } };
}

const props = defineProps<{
    withdrawals: { data: Withdrawal[]; meta?: any; last_page?: number; links?: any[] };
    filter: string|null;
}>();

const rejectModal = ref<{ show: boolean; id: number; note: string }>({ show: false, id: 0, note: '' });

function approve(id: number) {
    if (!confirm('Setujui penarikan ini?')) return;
    router.patch(route('admin.withdrawals.approve', id));
}

function openReject(id: number) {
    rejectModal.value = { show: true, id, note: '' };
}

function submitReject() {
    router.patch(route('admin.withdrawals.reject', rejectModal.value.id), { note: rejectModal.value.note }, {
        onSuccess: () => { rejectModal.value.show = false; },
    });
}

function fmt(v: number|string) { return 'Rp ' + Number(v).toLocaleString('id-ID'); }

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Penarikan Dana', href: '/admin/withdrawals' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="wd-page">
            <h1 class="page-title">Penarikan Dana</h1>

            <div class="filter-bar">
                <Link href="/admin/withdrawals" class="filter-btn" :class="{ active: !filter }">Semua</Link>
                <Link href="/admin/withdrawals?status=pending" class="filter-btn" :class="{ active: filter==='pending' }">Menunggu</Link>
                <Link href="/admin/withdrawals?status=approved" class="filter-btn" :class="{ active: filter==='approved' }">Disetujui</Link>
                <Link href="/admin/withdrawals?status=rejected" class="filter-btn" :class="{ active: filter==='rejected' }">Ditolak</Link>
            </div>

            <div class="list">
                <div v-for="wd in withdrawals.data" :key="wd.id" class="wd-card">
                    <div class="wd-left">
                        <div class="user-info">
                            <div class="avatar">{{ wd.wallet.user.name[0] }}</div>
                            <div>
                                <p class="uname">{{ wd.wallet.user.name }}</p>
                                <p class="uemail">{{ wd.wallet.user.email }}</p>
                            </div>
                        </div>
                        <div class="wd-details">
                            <p class="wd-amount">{{ fmt(wd.amount) }}</p>
                            <p class="wd-bank">{{ wd.bank_name }} — {{ wd.bank_account }}</p>
                            <p class="wd-acct">a/n {{ wd.account_name }}</p>
                            <p class="wd-date">{{ new Date(wd.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
                        </div>
                        <p v-if="wd.admin_note" class="wd-note">Catatan: {{ wd.admin_note }}</p>
                    </div>
                    <div class="wd-right">
                        <span class="status-badge" :class="wd.status">
                            <CheckCircle v-if="wd.status==='approved'" :size="13"/>
                            <XCircle v-else-if="wd.status==='rejected'" :size="13"/>
                            <Clock v-else :size="13"/>
                            {{ wd.status === 'approved' ? 'Disetujui' : wd.status === 'rejected' ? 'Ditolak' : 'Menunggu' }}
                        </span>
                        <div v-if="wd.status === 'pending'" class="wd-actions">
                            <button class="btn-approve" @click="approve(wd.id)"><CheckCircle :size="14"/> Setujui</button>
                            <button class="btn-reject" @click="openReject(wd.id)"><XCircle :size="14"/> Tolak</button>
                        </div>
                    </div>
                </div>
                <div v-if="withdrawals.data.length === 0" class="empty">Tidak ada permintaan penarikan</div>
            </div>

            <div class="pagination" v-if="(withdrawals.meta?.last_page ?? withdrawals.last_page ?? 0) > 1">
                <Link v-for="link in (withdrawals.meta?.links ?? withdrawals.links ?? [])" :key="link.label"
                    :href="link.url ?? '#'" class="page-link"
                    :class="{ active: link.active, disabled: !link.url }"
                    v-html="link.label"/>
            </div>
        </div>

        <!-- Reject Modal -->
        <Teleport to="body">
            <div v-if="rejectModal.show" class="modal-overlay" @click.self="rejectModal.show = false">
                <div class="modal">
                    <h3>Tolak Penarikan</h3>
                    <p class="modal-sub">Berikan alasan penolakan untuk vendor</p>
                    <textarea v-model="rejectModal.note" class="modal-textarea" rows="4" placeholder="Alasan penolakan..."></textarea>
                    <div class="modal-actions">
                        <button class="btn-cancel" @click="rejectModal.show = false">Batal</button>
                        <button class="btn-confirm-reject" @click="submitReject" :disabled="!rejectModal.note.trim()">Konfirmasi Tolak</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
.wd-page { padding: 24px; max-width: 900px; margin: 0 auto; }
.page-title { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 20px; }
.filter-bar { display: flex; gap: 8px; margin-bottom: 20px; }
.filter-btn { padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; }
.filter-btn.active { background: #667eea; border-color: #667eea; color: #fff; }
.list { display: flex; flex-direction: column; gap: 12px; }
.wd-card { background: #1e293b; border-radius: 14px; border: 1px solid rgba(255,255,255,.08); padding: 20px; display: flex; justify-content: space-between; gap: 20px; }
@media(max-width:640px) { .wd-card { flex-direction: column; } }
.wd-left { flex: 1; display: flex; flex-direction: column; gap: 12px; }
.user-info { display: flex; align-items: center; gap: 10px; }
.avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; flex-shrink: 0; }
.uname { font-weight: 600; color: #e2e8f0; }
.uemail { font-size: 12px; color: #64748b; }
.wd-amount { font-size: 20px; font-weight: 800; color: #fff; }
.wd-bank { font-size: 14px; color: #94a3b8; }
.wd-acct { font-size: 13px; color: #64748b; }
.wd-date { font-size: 12px; color: #475569; }
.wd-note { font-size: 13px; color: #fbbf24; font-style: italic; }
.wd-right { display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }
.status-badge { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.pending  { background: rgba(251,191,36,.15); color: #fbbf24; }
.status-badge.approved { background: rgba(52,211,153,.15);  color: #34d399; }
.status-badge.rejected { background: rgba(248,113,113,.15); color: #f87171; }
.wd-actions { display: flex; flex-direction: column; gap: 8px; }
.btn-approve { display: flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; background: rgba(52,211,153,.15); border: 1px solid rgba(52,211,153,.3); color: #34d399; font-size: 13px; font-weight: 600; cursor: pointer; transition: .2s; }
.btn-approve:hover { background: rgba(52,211,153,.25); }
.btn-reject  { display: flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; background: rgba(248,113,113,.15); border: 1px solid rgba(248,113,113,.3); color: #f87171; font-size: 13px; font-weight: 600; cursor: pointer; transition: .2s; }
.btn-reject:hover { background: rgba(248,113,113,.25); }
.empty { text-align: center; color: #64748b; padding: 48px; background: #1e293b; border-radius: 14px; }
.pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
.page-link { padding: 7px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 13px; text-decoration: none; }
.page-link.active { background: #667eea; border-color: #667eea; color: #fff; }
.page-link.disabled { opacity: .4; pointer-events: none; }
/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.7); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal { background: #1e293b; border-radius: 18px; padding: 28px; width: 100%; max-width: 440px; border: 1px solid rgba(255,255,255,.1); }
.modal h3 { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.modal-sub { font-size: 13px; color: #94a3b8; margin-bottom: 18px; }
.modal-textarea { width: 100%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 10px; padding: 12px; color: #fff; font-size: 14px; outline: none; resize: vertical; font-family: inherit; box-sizing: border-box; }
.modal-textarea:focus { border-color: #667eea; }
.modal-actions { display: flex; gap: 10px; margin-top: 16px; justify-content: flex-end; }
.btn-cancel { padding: 10px 20px; border-radius: 9px; border: 1px solid rgba(255,255,255,.1); background: transparent; color: #94a3b8; font-size: 14px; cursor: pointer; }
.btn-confirm-reject { padding: 10px 20px; border-radius: 9px; background: #ef4444; border: none; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-confirm-reject:disabled { opacity: .5; cursor: not-allowed; }
</style>
