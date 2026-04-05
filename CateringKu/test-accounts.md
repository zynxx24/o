# 🔐 CateringKu — Akun Test

Jalankan seeder: `php artisan db:seed --class=FullTestSeeder`
Atau reset penuh: `bash run.sh fresh`

---

## 📊 Data yang Dibuat

| Data | Jumlah | Detail |
|------|--------|--------|
| Admin | 1 | Super Admin |
| Vendor | 5 | Masing-masing 15 menu |
| Customer | 15 | Dengan orders & reviews |
| Menu Items | 75 | 15 per vendor |
| Orders | ~45-60 | Random 2-4 order per user |
| Reviews | ~30-40 | Rating 1-4, random per completed order |
| Promos | 6 | Kode diskon aktif |
| Carts | 3 | Keranjang aktif |
| Contact Messages | 8 | Berbagai subjek |

---

## 👤 Akun Login

### 🛡️ Admin
| Field | Value |
|-------|-------|
| Email | admin@cateringku.com |
| Password | admin123 |
| Akses | `/admin` |

### 🏪 Vendors (password semua: `vendor123`)

| Vendor | Email | Kota |
|--------|-------|------|
| Dapur Nusantara Catering | nusantara@cateringku.com | Jakarta |
| Sari Rasa Katering | sarirasa@cateringku.com | Bandung |
| Berkah Jaya Catering | berkah@cateringku.com | Surabaya |
| Royal Feast Catering | royal@cateringku.com | Jakarta |
| Padang Bundo Catering | padang@cateringku.com | Medan |

### 🙋 Customers (password semua: `user123`)

| Nama | Email |
|------|-------|
| Ahmad Fauzi | ahmad_fauzi@gmail.com |
| Siti Nurhaliza | siti_nur@gmail.com |
| Budi Santoso | budi_s@gmail.com |
| Dewi Kartika | dewi_k@gmail.com |
| Eko Prasetyo | eko_pras@gmail.com |
| Fitri Handayani | fitri_h@gmail.com |
| Gunawan Wibowo | gunawan_w@gmail.com |
| Hana Pertiwi | hana_p@gmail.com |
| Irfan Hakim | irfan_h@gmail.com |
| Joko Widodo | joko_w@gmail.com |
| Kartini Putri | kartini_p@gmail.com |
| Lukman Hakim | lukman_h@gmail.com |
| Maya Sari | maya_sari@gmail.com |
| Nanda Pratama | nanda_p@gmail.com |
| Olivia Wijaya | olivia_w@gmail.com |

---

### 🎫 Kode Promo

| Kode | Diskon | Keterangan |
|------|--------|------------|
| WELCOME10 | 10% | Member baru, min 100K |
| HEMAT20K | Rp20.000 | Min order 200K |
| NUSANTARA15 | 15% | Khusus vendor Nusantara |
| ROYAL25 | 25% | Khusus Royal Feast |
| PADANG10 | 10% | Khusus Padang Bundo |
| WEEKEND30 | Rp30.000 | Weekend, min 250K |

---

> ⚠️ **PENTING**: Jangan gunakan akun ini di production!
