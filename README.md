# Haven

**Layanan pelaporan, pendampingan, dan edukasi untuk penyintas kekerasan seksual.**

Haven adalah aplikasi web tempat penyintas bisa melapor tanpa harus menyebut nama, didampingi satu pendamping per kasus lewat pesan terenkripsi dan jadwal panggilan, lalu dirujuk ke layanan resmi seperti UPTD PPA, rumah sakit, atau bantuan hukum. Haven juga menyediakan artikel edukasi berdasarkan UU No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual (UU TPKS).

Proyek akhir Responsi 2 Praktikum Pemrograman Web I, Jurusan Informatika, Fakultas Teknik, Universitas Jenderal Soedirman, 2026.

> Haven adalah proyek akademik dan tidak menggantikan layanan darurat. Dalam keadaan darurat hubungi **112** atau **SAPA 129**.

---

## 1. Latar belakang dan tujuan

### Masalah
Banyak kasus kekerasan seksual tidak pernah dilaporkan. Tiga alasan yang paling sering muncul:

1. **Takut identitasnya terbuka**, lalu disalahkan atau dikucilkan.
2. **Tidak tahu harus melapor ke mana** dan apa langkah berikutnya.
3. **Tidak ada yang menemani** selama proses yang panjang dan melelahkan.

### Tujuan
- Menyediakan jalur lapor yang aman: bisa anonim dengan kode + PIN, dan isi laporan dienkripsi.
- Menghubungkan penyintas dengan pendamping sampai rujukan ke layanan resmi.
- Meningkatkan pemahaman masyarakat tentang bentuk kekerasan seksual dan hak korban.

### Kaitan dengan SDGs
| SDGs | Target | Peran Haven |
|---|---|---|
| **5 Kesetaraan Gender** (utama) | 5.2 Menghapus segala bentuk kekerasan terhadap perempuan dan anak perempuan | Pelaporan aman, pendampingan, edukasi |
| 16 Perdamaian, Keadilan, dan Kelembagaan yang Tangguh | 16.1 dan 16.3 Mengurangi kekerasan, akses keadilan | Rujukan ke bantuan hukum dan unit PPA |
| 3 Kehidupan Sehat dan Sejahtera | 3.4 Kesehatan mental | Pendampingan psikologis dan rujukan ke psikolog |

---

## 2. Modul dan fitur (10 modul)

| No | Modul | Fitur utama |
|---|---|---|
| 1 | **Autentikasi & Akun** | Daftar, masuk, keluar, lupa/reset kata sandi, profil, ganti kata sandi (otomatis mengeluarkan sesi lain), hapus akun dengan masa tunggu 7 hari, admin mengundang pendamping |
| 2 | **Pelaporan** | Formulir 4 langkah, anonim atau berakun, unggah bukti (JPG/PNG/PDF, dicek dari isi file), kode `HV-XXXXXX` + PIN 6 digit |
| 3 | **Lacak Laporan** | Buka laporan dengan kode + PIN (dikunci 15 menit setelah 5 kali salah), timeline status, tautkan laporan anonim ke akun |
| 4 | **Manajemen Kasus** | Antrean admin (filter, paginasi, prioritas "dalam bahaya"), penugasan dan pengalihan pendamping, alur status yang dijaga server, catatan internal terenkripsi |
| 5 | **Jadwal Pendampingan** | Pendamping membuka slot (suara/video/tatap muka), penyintas memesan dan membatalkan, satu slot tidak bisa dipesan dua kali (transaksi + `SELECT … FOR UPDATE`) |
| 6 | **Pesan Aman** | Percakapan per kasus, isi terenkripsi, pembaruan otomatis tiap 5 detik lewat `fetch`, tanda dibaca |
| 7 | **Edukasi** | 7 artikel orisinal, kategori, pencarian, CMS admin (draf/terbit, sampul, pratinjau) |
| 9 | **Direktori Layanan** | Filter provinsi/kota/jenis/24 jam, kontak hanya tampil setelah diverifikasi, dipakai untuk rujukan kasus |
| 10 | **Dashboard & Statistik** | Dashboard per peran, grafik `<canvas>` dari endpoint JSON, kategori dengan kurang dari 5 kasus digabung demi privasi |
| 11 | **Notifikasi & Audit Log** | Lonceng notifikasi, notifikasi untuk penyintas anonim, audit log setiap akses ke data laporan |

### Alur utama
```
Lapor (2) ─► kode + PIN ─► Lacak (3)
   │
   ▼
Admin memilah & menugaskan (4) ─► notifikasi (11)
   │
   ▼
Pendamping ◄─► Penyintas: Pesan (6) + Jadwal (5)
   │
   ▼
Rujukan ke Direktori (9) ─► Selesai
Edukasi (7) ditautkan dari beranda dan laporan. Statistik (10) dihitung dari semua modul.
```

### Status kasus
`Diterima → Ditinjau → Didampingi → (Dirujuk →) Selesai`, atau `Ditinjau → Ditutup` dengan alasan wajib. Perpindahan lain ditolak server.

### Keamanan dan keselamatan pengguna
- **Keluar cepat** di setiap halaman (tombol, atau tekan Esc dua kali), langsung pindah ke situs netral.
- Kronologi, kontak, catatan, pesan, dan nama file bukti dienkripsi dengan **AES-256-GCM** (`APP_KEY`). Data yang diubah atau dibaca dengan kunci lain ditolak.
- Kata sandi dan PIN memakai `password_hash`. Sesi diganti saat masuk, cookie `HttpOnly` + `SameSite=Lax`, sesi berakhir setelah 30 menit tanpa aktivitas.
- Token CSRF di setiap `POST` dan `fetch`, prepared statement di setiap query, semua output di-escape.
- Admin tidak bisa membaca kronologi. Pendamping hanya bisa membuka kasus yang ditugaskan kepadanya.
- Bukti disimpan di luar folder publik dan hanya disajikan setelah izin dicek.
- Rate limit untuk masuk, lacak, kirim laporan, dan pesan.
- Header `Content-Security-Policy`, `X-Frame-Options`, dan `Referrer-Policy: no-referrer`.

---

## 3. Teknologi

| Lapisan | Teknologi |
|---|---|
| Frontend | HTML5, CSS3, JavaScript vanilla (tanpa framework) |
| Backend | PHP 8.2 vanilla, pola MVC ringan dengan front controller (tanpa framework, tanpa Composer) |
| Database | MySQL / MariaDB 10.4 (XAMPP) lewat PDO |
| Keamanan | OpenSSL (AES-256-GCM), `password_hash`, `finfo` |
| Pengujian | Test runner PHP buatan sendiri, skrip end-to-end `curl`, uji browser lewat Edge DevTools Protocol |
| Huruf | Kazimir Text (judul), Proxima Nova (teks), Anonymous Pro (angka dan kode) |

---

## 4. Instalasi dan konfigurasi

Kebutuhan: **XAMPP dengan PHP 8.0+** (diuji dengan PHP 8.2.12 dan MariaDB 10.4) dan ekstensi `pdo_mysql`, `openssl`, `fileinfo` (semuanya aktif bawaan XAMPP).

```powershell
# 1. Ambil kode
git clone <url-repo> haven-v2
cd haven-v2

# 2. Buat .env dan kunci enkripsi
C:\xampp\php\php.exe bin\keygen.php
```
Buka `.env` dan sesuaikan `DB_USER`/`DB_PASS` jika MySQL kamu memakai kata sandi. Nama database bawaannya `haven_v2`.

```powershell
# 3. Nyalakan MySQL di XAMPP Control Panel, lalu buat tabel + data awal
C:\xampp\php\php.exe bin\db-reset.php

# 4. (Opsional, disarankan untuk demo) isi aktivitas contoh: 49 laporan, pesan, sesi
C:\xampp\php\php.exe bin\seed-demo.php
```

Tanpa langkah 3, kamu bisa mengimpor lewat phpMyAdmin dengan urutan `database/schema.sql`, lalu `database/seed.sql`, lalu `database/seed-content.sql`.

> **Penting:** `APP_KEY` mengunci semua data terenkripsi. Jika kunci diganti, laporan lama tidak bisa dibaca lagi. Jangan pernah meng-commit `.env`.

### Huruf berlisensi
Kazimir Text dan Proxima Nova adalah huruf berbayar dan tidak disertakan di repo. Letakkan file `.woff2` milikmu di `public/assets/fonts/` sesuai nama di `public/assets/fonts/README.md`. Tanpa file itu, halaman memakai huruf pengganti.

---

## 5. Menjalankan aplikasi

### Pilihan A: server bawaan PHP (paling mudah)
```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public
```
Buka **http://127.0.0.1:8000**

### Pilihan B: Apache XAMPP
Tambahkan ke `C:\xampp\apache\conf\extra\httpd-xampp.conf` (ganti path sesuai lokasi proyek), lalu restart Apache:
```apache
Alias /haven-v2 "C:/path/ke/haven-v2/public"
<Directory "C:/path/ke/haven-v2/public">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```
Buka **http://localhost/haven-v2/**. Hanya folder `public/` yang terbuka ke web. Folder lain ditolak lewat `.htaccess`.

### Akun demo
Semua akun memakai kata sandi `haven123`.

| Peran | Email |
|---|---|
| Penyintas | `rani@demo.id` |
| Pendamping | `dewi@demo.id` (juga `sari@`, `bayu@`, `laras@demo.id`) |
| Admin | `admin@demo.id` |

Setelah `bin/seed-demo.php`:
- `HV-9B2X6P`, PIN `482915`: laporan anonim yang menyatakan dalam bahaya, menunggu di antrean admin.
- `HV-7Q4K2M`: laporan berakun milik Rani, didampingi Dewi, dengan sesi besok pukul 10.00.

Email (reset kata sandi, undangan) tidak benar-benar dikirim. Isinya ditulis ke `storage/mail.log`.

### Pengujian
```powershell
C:\xampp\php\php.exe tests\run.php                                                   # 105 unit test
C:\xampp\php\php.exe bin\db-reset.php; powershell -ExecutionPolicy Bypass -File tests\smoke.ps1          # akun
C:\xampp\php\php.exe bin\db-reset.php; powershell -ExecutionPolicy Bypass -File tests\smoke-cases.ps1    # modul 2 sampai 6, 9, 11
C:\xampp\php\php.exe bin\db-reset.php; powershell -ExecutionPolicy Bypass -File tests\smoke-content.ps1  # modul 7, 10
C:\xampp\php\php.exe bin\db-reset.php; node tests\browser.mjs                        # JavaScript di Edge headless
```
Skrip end-to-end butuh server berjalan di `127.0.0.1:8000` dan database yang baru di-reset.

---

## Sebelum demo: verifikasi direktori layanan
Data layanan bawaan adalah **contoh**. Hanya 112 dan SAPA 129 yang berisi kontak. Layanan regional (UPTD PPA, Unit PPA Polresta, RSUD, Satgas PPKPT Unsoed, LBH) sengaja dibiarkan **tanpa nomor** dan berlabel "Perlu verifikasi".

**Tim wajib menelepon tiap lembaga**, lalu mengisi nomor dan jam layanan lewat *Admin › Layanan* dan mencentang "sudah dicek". Nomor yang salah di situs untuk korban bisa berbahaya. Cek ulang juga nomor SAPA 129.

---

## Struktur folder
```
public/         satu-satunya folder web: index.php, .htaccess, assets/, uploads/covers/
app/
  core/         Router, App, View, Database, Session, Csrf, Crypto, Validator, RateLimiter,
                Auth, Audit, Notifier, CaseWorkflow, CaseAccess, Upload, Markup, …
  controllers/  satu controller per modul
  models/       Report, Message, Slot, Booking, Service, Article, Stats, …
  views/        layouts/, partials/, pages/
  routes.php    seluruh rute
database/       schema.sql, seed.sql, seed-content.sql
bin/            keygen.php, db-reset.php, seed-demo.php, purge-accounts.php
storage/        uploads/ (bukti), logs/, mail.log (tidak bisa dibuka dari web)
tests/          unit test, smoke test, uji browser
prototype/      prototipe HTML statis (Task 0) sebagai referensi desain
docs/           demo-script.md
```

## Tim
| Anggota | NIM | Tanggung jawab |
|---|---|---|
| Anggota A | (isi) | Modul 1 sampai 3 dan keamanan inti (enkripsi, CSRF, rate limit) |
| Anggota B | (isi) | Modul 4, 5, 6, 11 |
| Anggota C | (isi) | Modul 7, 9, 10 dan antarmuka |

Isi nama dan NIM di tabel ini dan di `app/views/pages/static/tentang.php`.

## Sumber rujukan konten
- UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, JDIH BPK: https://peraturan.bpk.go.id/
- Komnas Perempuan: https://komnasperempuan.go.id/
- Kementerian PPPA dan layanan SAPA 129: https://www.kemenpppa.go.id/
- LPSK: https://www.lpsk.go.id/

Seluruh teks artikel ditulis sendiri untuk proyek ini. Foto berasal dari Pexels.
