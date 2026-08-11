# Kostara — Sistem Informasi Penyewaan & Manajemen Kos

Aplikasi web berbasis **Laravel 12** (Blade bawaan, tanpa Vue/React) dengan database **MySQL** yang bisa dikelola lewat **phpMyAdmin**, dibangun mengikuti PRD Kostara (4 role: Superadmin, Owner, Penjaga Kos, Customer).

---

## 1. Persyaratan

- PHP >= 8.2 (dengan ekstensi: mbstring, xml, curl, mysql/pdo_mysql, bcmath, zip)
- Composer 2
- MySQL 5.7+/8 atau MariaDB (diakses lewat phpMyAdmin)
- Node.js (opsional, hanya jika ingin build ulang asset Tailwind lokal — saat ini styling memakai Tailwind CDN sehingga **tidak wajib** `npm install`)

## 2. Instalasi

```bash
# 1. Ekstrak/clone folder ini, lalu masuk ke direktori project
cd kostara-app

# 2. Install dependency PHP (mengunduh Laravel framework dari Packagist)
composer install

# 3. Salin file environment
cp .env.example .env
php artisan key:generate
```

## 3. Konfigurasi Database (MySQL via phpMyAdmin)

1. Buka phpMyAdmin, buat database baru bernama **`kostara`** (collation `utf8mb4_unicode_ci`).
2. Sesuaikan kredensial di file `.env` bila perlu:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kostara
DB_USERNAME=root
DB_PASSWORD=
```

3. Jalankan migrasi + seeder (membuat struktur tabel dan akun contoh):

```bash
php artisan migrate --seed
```

4. Buat symbolic link untuk file publik (foto kos/kamar/QRIS):

```bash
php artisan storage:link
```

5. Jalankan server lokal:

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## 4. Akun Contoh (dari seeder)

| Role | Email | Password |
|---|---|---|
| Superadmin | superadmin@kostara.test | password |
| Owner | owner@kostara.test | password |
| Penjaga Kos | penjaga@kostara.test | password |
| Customer | customer@kostara.test | password |

Seeder juga membuat 1 kos contoh ("Kos Melati Asri") dengan 2 kamar (satu **Terisi** dengan riwayat pembayaran Lunas, satu **Tersedia**) agar dashboard Owner (kartu kamar terisi) langsung bisa dicoba.

## 5. Struktur Fitur vs Modul PRD

| Modul PRD | Implementasi |
|---|---|
| 3 — Role & Hak Akses | Kolom `role` di tabel `users` + middleware `role:...` |
| 5 — Manajemen Akun (Superadmin) | `Superadmin\UserManagementController` — CRUD akun semua role, nonaktifkan/aktifkan |
| 6.1/6.2 — Dashboard & Kartu Kamar Terisi | `DashboardController`, kartu kamar bisa diklik di `dashboard/owner.blade.php` → detail di `owner/kamar/show.blade.php` |
| 7/8 — Kos & Kamar | `Owner\KosController`, `Owner\KamarController` |
| 9 — Landing Page (Guest) | `GuestController` — info umum kos + tombol Daftar/Login & WhatsApp Owner |
| 10 — Sewa Langsung | `Owner\PenyewaLangsungController` — upload KTP ke disk privat |
| 11 — Booking | `BookingController` |
| 12/13 — Sewa & Jatuh Tempo | `SewaController`, dihitung dari `tanggal_selesai` |
| 15 — Pembayaran (rekening/QRIS, upload bukti, validasi) | `Owner\PaymentSettingController`, `Customer\PembayaranController`, `Owner\PembayaranValidationController` (tombol **Ya, Validasi** / **Belum**) |
| 16-19 — Maintenance (+ jam layanan 07.00-17.00, tombol WA jika di luar jam) | `MaintenanceController` |
| 20 — Tugas Penjaga | `TaskController` |
| 26 — Pengumuman | `AnnouncementController` |
| 27 — Keamanan Data (KTP privat, hashing password) | disk `private` + `SecureFileController`, hashing bawaan Laravel |

## 6. Catatan Keamanan (BR05)

Foto KTP dan bukti pembayaran disimpan di disk **`private`** (`storage/app/private`, tidak dapat diakses lewat URL publik). Akses hanya melalui route terautentikasi `route('file.ktp', ...)` dan `route('file.bukti-pembayaran', ...)` yang memeriksa kepemilikan/role sebelum menyajikan file.

## 7. Catatan WhatsApp (BR08)

Semua tombol WhatsApp (Hubungi Owner, Hubungi Penjaga, dst.) hanya membuka `https://wa.me/...` dengan pesan yang sudah terisi (lihat `User::waLink()`). Sistem **tidak** mengirim pesan otomatis — pengguna tetap menekan tombol Send di WhatsApp secara manual.

## 8. Batasan / Pengembangan Lanjutan

Bagian ini adalah kerangka kerja (working skeleton) yang mengimplementasikan alur inti PRD. Beberapa hal berikut belum termasuk dan bisa dikembangkan lebih lanjut sesuai roadmap "Fitur Pengembangan Berikutnya" di PRD:

- Laporan/analytics (grafik pendapatan, okupansi) & export Excel/PDF
- Notifikasi in-app real-time (saat ini status terlihat lewat halaman, bukan push notification)
- Reminder otomatis terjadwal (`php artisan schedule:run` perlu di-cron-kan bila ingin auto-cek jatuh tempo)
- Payment gateway/QRIS otomatis (saat ini QRIS statis diunggah manual oleh Owner, sesuai PRD MVP)
- Multi-bahasa, PWA/mobile responsive lanjutan

## 9. Struktur Folder Penting

```
app/Http/Controllers/        Controller per modul (Owner/, Superadmin/, Customer/, Auth/)
app/Http/Middleware/         EnsureUserHasRole.php — middleware role:...
app/Models/                  Kos, Kamar, Booking, Sewa, Pembayaran, dst.
database/migrations/         Struktur tabel MySQL
database/seeders/            Akun & data contoh
resources/views/             Blade views (layout, dashboard, owner/, customer/, dst.)
routes/web.php                Semua rute aplikasi
```
