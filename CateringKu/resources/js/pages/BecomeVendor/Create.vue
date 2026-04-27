<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Building2, Shield, Upload, AlertTriangle, CheckCircle } from 'lucide-vue-next';

const form = useForm({
    vendor_name: '',
    description: '',
    address: '',
    city: '',
    province: '',
    phone: '',
    email: '',
    payment_method: '',
    payment_proof: null as File | null,
});

const step = ref(1);
const proofPreview = ref<string | null>(null);

function nextStep() { if (step.value < 3) step.value++; }
function prevStep() { if (step.value > 1) step.value--; }

function onFileChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.payment_proof = file;
    proofPreview.value = URL.createObjectURL(file);
}

function submit() {
    form.post(route('become-vendor.store'), { forceFormData: true });
}

const breadcrumbs = [{ title: 'Daftar Vendor', href: '/become-vendor' }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bv-page">
            <div class="bv-header">
                <h1>Daftar sebagai Vendor</h1>
                <p>Bergabunglah dengan CateringKu dan raih penghasilan lebih</p>
            </div>

            <!-- Stepper -->
            <div class="stepper">
                <div v-for="i in 3" :key="i" class="step" :class="{ active: step===i, done: step>i }">
                    <div class="step-num">
                        <CheckCircle v-if="step > i" :size="16"/>
                        <span v-else>{{ i }}</span>
                    </div>
                    <span>{{ ['Profil', 'Deposit', 'Konfirmasi'][i-1] }}</span>
                </div>
            </div>

            <div class="bv-card">
                <form @submit.prevent="submit">
                    <!-- Step 1 -->
                    <div v-show="step === 1">
                        <h2 class="step-title"><Building2 :size="18"/> Informasi Usaha</h2>
                        <div class="form-grid">
                            <div class="form-group full">
                                <label>Nama Usaha *</label>
                                <input v-model="form.vendor_name" class="fi" placeholder="Dapur Bunda Catering" required/>
                                <span class="err">{{ form.errors.vendor_name }}</span>
                            </div>
                            <div class="form-group full">
                                <label>Deskripsi *</label>
                                <textarea v-model="form.description" class="fi" rows="3" placeholder="Ceritakan keunggulan katering Anda..." required></textarea>
                            </div>
                            <div class="form-group full">
                                <label>Alamat Lengkap *</label>
                                <textarea v-model="form.address" class="fi" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Kota *</label>
                                <input v-model="form.city" class="fi" required/>
                            </div>
                            <div class="form-group">
                                <label>Provinsi *</label>
                                <input v-model="form.province" class="fi" required/>
                            </div>
                            <div class="form-group">
                                <label>Telepon *</label>
                                <input v-model="form.phone" type="tel" class="fi" required/>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input v-model="form.email" type="email" class="fi" required/>
                            </div>
                        </div>
                        <div class="actions"><button type="button" class="btn-next" @click="nextStep">Lanjut →</button></div>
                    </div>

                    <!-- Step 2 -->
                    <div v-show="step === 2">
                        <h2 class="step-title"><Shield :size="18"/> Deposit Jaminan</h2>
                        <div class="deposit-box">
                            <p class="deposit-amount">Rp 10.000.000</p>
                            <p class="deposit-note">Dana dibekukan sebagai jaminan. Dapat dicairkan kembali jika berhenti jadi vendor.</p>
                        </div>
                        <div class="bank-info">
                            <p><strong>BCA</strong> — 1234 5678 90 — a/n PT CateringKu Indonesia</p>
                        </div>
                        <div class="form-group" style="margin-bottom:16px">
                            <label>Metode Transfer *</label>
                            <select v-model="form.payment_method" class="fi" required>
                                <option value="">-- Pilih --</option>
                                <option>Transfer BCA</option>
                                <option>Transfer BNI</option>
                                <option>Transfer Mandiri</option>
                                <option>Transfer BRI</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><Upload :size="13" style="vertical-align:-2px"/> Bukti Transfer *</label>
                            <div class="upload-area" @click="($refs.fi as HTMLInputElement).click()">
                                <img v-if="proofPreview" :src="proofPreview" class="proof-img"/>
                                <div v-else class="upload-placeholder">
                                    <Upload :size="28"/>
                                    <p>Klik untuk upload (JPG/PNG, maks 5MB)</p>
                                </div>
                            </div>
                            <input ref="fi" type="file" accept="image/*" class="hidden" @change="onFileChange"/>
                            <span class="err">{{ form.errors.payment_proof }}</span>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn-back" @click="prevStep">← Kembali</button>
                            <button type="button" class="btn-next" @click="nextStep" :disabled="!form.payment_method || !form.payment_proof">Lanjut →</button>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div v-show="step === 3">
                        <h2 class="step-title">✅ Konfirmasi</h2>
                        <div class="confirm-box">
                            <div class="cr"><span>Nama Usaha</span><strong>{{ form.vendor_name }}</strong></div>
                            <div class="cr"><span>Lokasi</span><strong>{{ form.city }}, {{ form.province }}</strong></div>
                            <div class="cr"><span>Telepon</span><strong>{{ form.phone }}</strong></div>
                            <div class="cr"><span>Email</span><strong>{{ form.email }}</strong></div>
                            <div class="cr"><span>Deposit</span><strong class="gold">Rp 10.000.000</strong></div>
                            <div class="cr"><span>Metode</span><strong>{{ form.payment_method }}</strong></div>
                        </div>
                        <div class="warning-box">
                            <AlertTriangle :size="15"/>
                            <p>Dengan mendaftar, Anda menyetujui syarat & ketentuan vendor. Deposit dibekukan setelah verifikasi admin.</p>
                        </div>
                        <div class="actions">
                            <button type="button" class="btn-back" @click="prevStep">← Kembali</button>
                            <button type="submit" class="btn-submit" :disabled="form.processing">
                                {{ form.processing ? 'Mengirim...' : '🚀 Daftar Sekarang' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.bv-page{padding:24px;max-width:700px;margin:0 auto}
.bv-header{text-align:center;margin-bottom:28px}
.bv-header h1{font-size:26px;font-weight:800;color:#fff;margin-bottom:6px}
.bv-header p{color:#94a3b8}
.stepper{display:flex;justify-content:center;gap:0;margin-bottom:28px}
.step{display:flex;flex-direction:column;align-items:center;gap:6px;flex:1;max-width:140px;font-size:12px;color:#64748b}
.step-num{width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.08);border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-weight:700;color:#64748b}
.step.active .step-num{background:#667eea;border-color:#667eea;color:#fff}
.step.done .step-num{background:#34d399;border-color:#34d399;color:#fff}
.step.active span:last-child{color:#667eea;font-weight:600}
.bv-card{background:#1e293b;border-radius:18px;padding:28px;border:1px solid rgba(255,255,255,.08)}
.step-title{display:flex;align-items:center;gap:8px;font-size:18px;font-weight:700;color:#fff;margin-bottom:20px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-group.full{grid-column:1/-1}
.form-group label{font-size:13px;font-weight:500;color:#cbd5e1}
.fi{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:9px;padding:11px 14px;color:#fff;font-size:14px;outline:none;width:100%;font-family:inherit;transition:border-color .2s}
.fi:focus{border-color:#667eea}
.err{font-size:12px;color:#f87171}
.deposit-box{background:linear-gradient(135deg,rgba(102,126,234,.15),rgba(118,75,162,.15));border:1px solid rgba(102,126,234,.3);border-radius:12px;padding:20px;text-align:center;margin-bottom:16px}
.deposit-amount{font-size:28px;font-weight:800;color:#a78bfa;margin-bottom:8px}
.deposit-note{font-size:13px;color:#94a3b8}
.bank-info{background:rgba(255,255,255,.04);border-radius:10px;padding:14px;text-align:center;font-size:14px;color:#e2e8f0;margin-bottom:20px}
.upload-area{border:2px dashed rgba(102,126,234,.4);border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:.2s}
.upload-area:hover{border-color:#667eea;background:rgba(102,126,234,.05)}
.upload-placeholder{display:flex;flex-direction:column;align-items:center;gap:8px;color:#64748b;font-size:13px}
.proof-img{max-width:100%;max-height:180px;border-radius:8px;object-fit:contain}
.hidden{display:none}
.confirm-box{background:rgba(255,255,255,.04);border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:10px;margin-bottom:16px}
.cr{display:flex;justify-content:space-between;font-size:14px}
.cr span{color:#64748b}
.cr strong{color:#e2e8f0}
.gold{color:#fbbf24!important}
.warning-box{display:flex;align-items:flex-start;gap:8px;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.3);border-radius:10px;padding:14px;color:#fbbf24;font-size:13px;margin-bottom:20px}
.actions{display:flex;gap:10px;justify-content:flex-end;margin-top:24px}
.btn-back{padding:11px 22px;border-radius:9px;border:1px solid rgba(255,255,255,.12);background:transparent;color:#94a3b8;font-size:14px;cursor:pointer}
.btn-next{padding:11px 22px;border-radius:9px;background:linear-gradient(135deg,#667eea,#764ba2);border:none;color:#fff;font-size:14px;font-weight:600;cursor:pointer}
.btn-next:disabled{opacity:.5;cursor:not-allowed}
.btn-submit{padding:11px 28px;border-radius:9px;background:linear-gradient(135deg,#43e97b,#38f9d7);border:none;color:#0f172a;font-size:15px;font-weight:700;cursor:pointer}
.btn-submit:disabled{opacity:.6;cursor:not-allowed}
</style>
