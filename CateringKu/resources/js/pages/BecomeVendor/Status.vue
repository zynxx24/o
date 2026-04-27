<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Clock, CheckCircle, XCircle, AlertCircle, ArrowRight } from 'lucide-vue-next';

interface Application {
    id: number;
    vendor_name: string;
    status: 'draft'|'submitted'|'deposit_pending'|'approved'|'rejected';
    rejection_reason: string|null;
    deposit_amount: string;
    payment_method: string|null;
    created_at: string;
    reviewed_at: string|null;
}

const props = defineProps<{ application: Application }>();

const statusConfig: Record<string, { label: string; color: string; icon: any; desc: string }> = {
    draft:           { label: 'Draft',            color: 'gray',   icon: AlertCircle,  desc: 'Pendaftaran belum dikirim.' },
    submitted:       { label: 'Menunggu Review',  color: 'yellow', icon: Clock,        desc: 'Tim admin sedang meninjau aplikasi Anda. Biasanya 1-3 hari kerja.' },
    deposit_pending: { label: 'Verifikasi Deposit', color: 'blue', icon: Clock,        desc: 'Bukti transfer deposit sedang diverifikasi.' },
    approved:        { label: 'Disetujui ✅',     color: 'green',  icon: CheckCircle,  desc: 'Selamat! Akun Anda telah diupgrade menjadi vendor.' },
    rejected:        { label: 'Ditolak',          color: 'red',    icon: XCircle,      desc: 'Mohon maaf, aplikasi Anda tidak dapat disetujui saat ini.' },
};

const cfg = statusConfig[props.application.status] ?? statusConfig['draft'];

const breadcrumbs = [{ title: 'Status Pendaftaran Vendor', href: '/become-vendor/status' }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="status-page">
            <div class="status-card">
                <!-- Icon -->
                <div class="status-icon" :class="cfg.color">
                    <component :is="cfg.icon" :size="48"/>
                </div>

                <h1 class="status-label" :class="cfg.color">{{ cfg.label }}</h1>
                <p class="status-desc">{{ cfg.desc }}</p>

                <!-- Rejection reason -->
                <div v-if="application.status === 'rejected' && application.rejection_reason" class="rejection-box">
                    <p class="rejection-title">Alasan Penolakan:</p>
                    <p>{{ application.rejection_reason }}</p>
                </div>

                <!-- Details -->
                <div class="details-box">
                    <div class="detail-row"><span>Nama Usaha</span><strong>{{ application.vendor_name }}</strong></div>
                    <div class="detail-row"><span>Deposit</span><strong class="gold">Rp {{ Number(application.deposit_amount).toLocaleString('id-ID') }}</strong></div>
                    <div v-if="application.payment_method" class="detail-row"><span>Metode</span><strong>{{ application.payment_method }}</strong></div>
                    <div class="detail-row"><span>Tanggal Daftar</span><strong>{{ new Date(application.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</strong></div>
                    <div v-if="application.reviewed_at" class="detail-row">
                        <span>Tanggal Review</span>
                        <strong>{{ new Date(application.reviewed_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</strong>
                    </div>
                </div>

                <!-- CTA -->
                <div class="cta-area">
                    <Link v-if="application.status === 'approved'" href="/vendor-panel" class="btn-primary">
                        Buka Dashboard Vendor <ArrowRight :size="16"/>
                    </Link>
                    <Link v-else href="/" class="btn-secondary">Kembali ke Beranda</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.status-page{padding:40px 24px;display:flex;justify-content:center}
.status-card{background:#1e293b;border-radius:20px;padding:40px 32px;max-width:500px;width:100%;text-align:center;border:1px solid rgba(255,255,255,.08)}
.status-icon{width:88px;height:88px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.status-icon.yellow{background:rgba(251,191,36,.15);color:#fbbf24}
.status-icon.green{background:rgba(52,211,153,.15);color:#34d399}
.status-icon.red{background:rgba(248,113,113,.15);color:#f87171}
.status-icon.blue{background:rgba(96,165,250,.15);color:#60a5fa}
.status-icon.gray{background:rgba(255,255,255,.08);color:#94a3b8}
.status-label{font-size:22px;font-weight:800;margin-bottom:10px}
.status-label.yellow{color:#fbbf24}
.status-label.green{color:#34d399}
.status-label.red{color:#f87171}
.status-label.blue{color:#60a5fa}
.status-label.gray{color:#94a3b8}
.status-desc{font-size:14px;color:#94a3b8;line-height:1.6;margin-bottom:24px}
.rejection-box{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);border-radius:10px;padding:16px;text-align:left;margin-bottom:20px;color:#fca5a5;font-size:14px}
.rejection-title{font-weight:600;margin-bottom:6px}
.details-box{background:rgba(255,255,255,.04);border-radius:12px;padding:20px;text-align:left;display:flex;flex-direction:column;gap:12px;margin-bottom:28px}
.detail-row{display:flex;justify-content:space-between;font-size:14px}
.detail-row span{color:#64748b}
.detail-row strong{color:#e2e8f0}
.gold{color:#fbbf24!important}
.cta-area{display:flex;justify-content:center;gap:12px}
.btn-primary{display:flex;align-items:center;gap:8px;padding:13px 28px;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-weight:700;font-size:15px;text-decoration:none}
.btn-secondary{padding:13px 28px;border-radius:10px;border:1px solid rgba(255,255,255,.15);color:#94a3b8;text-decoration:none;font-size:14px}
</style>
