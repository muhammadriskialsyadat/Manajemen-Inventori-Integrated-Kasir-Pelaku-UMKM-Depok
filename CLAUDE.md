# CLAUDE.md — Heaven Spot Indonesia Inventory System

## Tentang Project Ini

Project ini adalah PENGEMBANGAN dari sistem Penulisan Ilmiah (PI)
sebelumnya yang sudah live di heavenspotindo.my.id. Sistem lama
adalah sistem manajemen stok kaleng cat grafiti berbasis web
untuk Heaven Spot Indonesia (UMKM di Kota Medan, Sumatera Utara)
yang dibangun dengan Laravel + Filament + MySQL menggunakan
single-admin (hanya Owner/admin yang bisa akses).

Sistem lama sudah TIDAK DIPAKAI karena tidak relevan dengan
perkembangan bisnis. Project ini membangun ulang dan
mengembangkannya menjadi sistem multi-user dengan notifikasi
otomatis WhatsApp.

Judul resmi skripsi:
"Pengembangan Sistem Inventori Multi-User dengan Notifikasi
Otomatis WhatsApp Berbasis Fonnte pada Heaven Spot Indonesia
Menggunakan Metode Agile Scrum"

---

## Stack Teknologi

- Backend : PHP, Laravel (framework utama)
- Database : MySQL
- Admin Panel: Filament (admin panel builder untuk Laravel)
- RBAC : Laravel Spatie Permissions (role & permission)
- WhatsApp : Fonnte (WhatsApp Gateway — bukan official Meta
  WhatsApp Business API)
- Testing : Black Box Testing + UAT (User Acceptance Testing)

---

## Masalah yang Diselesaikan

1. Sistem lama hanya single-user (hanya Owner/admin)
2. Bisnis berkembang, kini ada 4 posisi berbeda dengan
   kebutuhan akses yang berbeda-beda
3. Tidak ada notifikasi otomatis — stok hanya bisa dipantau
   manual via website

---

## Solusi yang Dibangun

### 1. Multi-User dengan RBAC (Role-Based Access Control)

Menggunakan Laravel Spatie Permissions untuk mengatur 4 role:

| Role    | Hak Akses                                             |
| ------- | ----------------------------------------------------- |
| Owner   | Akses penuh ke seluruh modul sistem                   |
| Kasir   | Hanya modul transaksi penjualan (Sales Order)         |
| Gudang  | Hanya modul stok masuk/keluar (Purchase Order + stok) |
| Akuntan | Hanya laporan — READ ONLY (tidak bisa input data)     |

### 2. Pengaturan Batas Minimum Stok

- Owner bisa mengatur batas minimum stok per produk secara manual
- Sistem akan memantau stok dan memicu notifikasi otomatis
  ketika stok menyentuh atau melewati batas minimum

### 3. Notifikasi Otomatis via WhatsApp Gateway (Fonnte)

Notifikasi dikirim ke Owner ketika:

- Stok produk menyentuh batas minimum (stok menipis)
- Ada transaksi penjualan baru masuk
- Ada penerimaan barang baru dari supplier

Notifikasi dikirim ke Customer ketika:

- Konfirmasi pembelian otomatis setelah transaksi selesai

### 4. Penyimpanan Nomor WhatsApp Customer

- Data customer menyimpan nomor WhatsApp untuk keperluan
  notifikasi konfirmasi pembelian

---

## Fitur yang DIPERTAHANKAN dari Sistem Lama (PI)

Fitur-fitur berikut sudah ada di sistem PI dan harus
dipertahankan serta disesuaikan dengan sistem multi-user baru:

- Manajemen master data: kategori produk, produk,
  pelanggan (customer), supplier
- Purchase Order (PO) dari supplier
- Sales Order (SO) kepada customer
- Monitoring pergerakan stok real-time
- Dashboard analytics (disesuaikan per role)
- Laporan dalam format PDF dan Excel

---

## Yang TIDAK Termasuk Scope Sistem Ini

Jangan tambahkan fitur-fitur berikut karena di luar scope penelitian:

- Aplikasi mobile Android atau iOS
- Fitur broadcast promosi/marketing ke customer
- Algoritma forecasting atau prediksi stok otomatis
- Payment gateway (pembayaran online)
- Two-factor authentication (2FA)

---

## Metodologi Pengembangan: Agile Scrum (4 Sprint)

### Sprint 1 — Fondasi Multi-User & RBAC

- Migrasi dan penyesuaian sistem lama
- Instalasi dan konfigurasi Spatie Permissions
- Setup 4 role: Owner, Kasir, Gudang, Akuntan
- Middleware pembatasan akses per role

### Sprint 2 — Pengembangan Fitur Per Role

- Halaman dan modul khusus Kasir (Sales Order)
- Halaman dan modul khusus Gudang (stok masuk/keluar, PO)
- Dashboard Owner (akses penuh + ringkasan sistem)
- Halaman Akuntan (laporan read-only)

### Sprint 3 — Integrasi Notifikasi Fonnte

- Konfigurasi Fonnte WhatsApp Gateway
- Notifikasi stok minimum ke Owner
- Notifikasi transaksi penjualan ke Owner
- Notifikasi penerimaan barang ke Owner
- Notifikasi konfirmasi pembelian ke Customer

### Sprint 4 — Pengujian, UAT, dan Deployment

- Black Box Testing seluruh fitur
- User Acceptance Testing (UAT) bersama pengguna nyata
- Perbaikan bug hasil pengujian
- Deployment final

---

## Konvensi Coding

- Gunakan konvensi Laravel standar (PSR-12)
- Semua Resource Filament dibuat terpisah per role/modul
- Policy Laravel digunakan untuk pembatasan akses resource
- Middleware Spatie digunakan di level route/panel Filament
- Nama tabel menggunakan snake_case (bahasa Inggris)
- Nama kolom menggunakan snake_case (bahasa Inggris)
- Komentar kode boleh dalam Bahasa Indonesia

---

## Catatan Penting untuk Claude Code

1. Sistem ini dibangun DI ATAS sistem PI yang sudah ada —
   pahami struktur existing sebelum menambahkan fitur baru
2. Fonnte adalah WhatsApp Gateway pihak ketiga (bukan
   official Meta WhatsApp Business API) — integrasinya
   via HTTP request ke API Fonnte
3. Filament digunakan sebagai admin panel builder —
   semua UI manajemen data dibangun via Filament Resource
4. Spatie Permissions adalah library utama untuk RBAC —
   gunakan Gate, Policy, dan middleware Spatie secara konsisten
5. Setiap perubahan pada struktur database harus
   menggunakan Laravel Migration (jangan ubah database
   langsung)
   