# Naskah video presentasi Haven (± 9 menit)

Ketentuan responsi: minimal 7 menit, **semua anggota tampil**, dan mencakup profil tim, latar belakang, tujuan & manfaat, modul & fitur, demo, kaitan SDGs, serta kesimpulan.

## Persiapan (sebelum merekam)
```powershell
C:\xampp\php\php.exe bin\db-reset.php
C:\xampp\php\php.exe bin\seed-demo.php
C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public
```
- Siapkan **tiga jendela browser** (atau satu biasa + dua incognito): **Penyintas**, **Admin**, **Pendamping**. Semuanya masih belum masuk.
- Pastikan nomor layanan sudah diverifikasi tim (lihat README).
- Perbesar zoom browser ke 110 sampai 125% agar terbaca di video.

| Waktu | Bagian | Pembicara |
|---|---|---|
| 0:00 sampai 0:45 | Profil tim | Semua |
| 0:45 sampai 1:45 | Latar belakang & masalah | A |
| 1:45 sampai 2:30 | Tujuan, manfaat, SDGs | C |
| 2:30 sampai 3:30 | Arsitektur & modul | B |
| 3:30 sampai 7:45 | Demo alur lengkap | A, B, C bergantian |
| 7:45 sampai 8:30 | Keamanan & pengujian | A |
| 8:30 sampai 9:00 | Kesimpulan | Semua |

---

## 0:00 Profil tim (semua)
Setiap anggota memperkenalkan diri: nama, NIM, dan modul yang dikerjakan (lihat tabel Tim di README).

## 0:45 Latar belakang (A)
- Banyak kekerasan seksual tidak dilaporkan karena takut dikenali, tidak tahu harus ke mana, dan tidak ada pendamping.
- Haven menangani ketiganya: laporan tanpa nama, satu pendamping per kasus, dan rujukan ke lembaga resmi.
- Tunjukkan **Beranda**: kartu darurat 112 dan SAPA 129 di samping judul, lalu empat tahap pelaporan di bawahnya.

## 1:45 Tujuan, manfaat, SDGs (C)
- Gulir ke bagian **SDGs** di beranda, lalu buka **Tentang**.
- SDGs 5 target 5.2 sebagai tujuan utama; SDGs 16.3 lewat rujukan bantuan hukum; SDGs 3.4 lewat pendampingan psikologis.
- Manfaat: penyintas punya jalur aman, pendamping bekerja terstruktur, publik mendapat edukasi berbasis UU TPKS.

## 2:30 Arsitektur & modul (B)
- Tampilkan bagan alur di README: HTML/CSS/JS vanilla → `public/index.php` → Router → Controller → Model (PDO) → MySQL.
- Sebutkan 11 modul dan bahwa semuanya terhubung lewat satu alur kasus.
- Tunjukkan diagram status: Diterima → Ditinjau → Didampingi → Dirujuk → Selesai.

## 3:30 Demo

### a. Penyintas melapor tanpa akun (A, jendela Penyintas, ± 1 menit)
1. Klik **Buat laporan**. Di langkah 1 klik *Lanjut* tanpa memilih apa pun → tunjukkan pesan error.
2. Pilih *Pelecehan nonfisik* dan *Tidak saat ini*, lalu isi kronologi singkat. Tunjukkan penghitung karakter.
3. Isi wilayah *Jawa Tengah / Banyumas*. Di langkah 4 tunjukkan ringkasan, lampirkan satu foto, lalu centang persetujuan dan klik **Kirim**.
4. Tunjukkan **kode + PIN** yang hanya tampil sekali, dan tombol salin/unduh.
5. Tekan **Esc dua kali** → halaman langsung berganti ke pencarian cuaca. Jelaskan fitur *Keluar cepat*.

### b. Melacak dengan kode + PIN (A, ± 30 detik)
1. Buka **Lacak laporan**. Masukkan PIN salah sekali → pesan "Sisa 4 percobaan".
2. Masukkan `HV-9B2X6P` / `482915` → timeline status "Diterima".

### c. Admin memilah dan menugaskan (B, jendela Admin, ± 1 menit)
1. Masuk sebagai `admin@demo.id`. Tunjukkan lonceng notifikasi dan **Statistik** (grafik tren, jenis kejadian, beban pendamping). Jelaskan aturan "kategori < 5 digabung".
2. Buka **Antrean kasus**. `HV-9B2X6P` ada paling atas karena penyintas menyatakan dalam bahaya.
3. Buka kasusnya. Tunjukkan bahwa **admin tidak bisa membaca kronologi**, hanya data pemilahan dan riwayat akses.
4. Pilih urgensi *Tinggi*, pendamping **Dewi Anggraini**, tulis catatan "Sapa dalam 1 jam", lalu klik **Tugaskan**.

### d. Pendamping dan penyintas berkomunikasi (B + A, jendela Pendamping & Penyintas, ± 1 menit)
1. Masuk sebagai `dewi@demo.id`. Lonceng menunjukkan "Kasus baru ditugaskan".
2. Buka `HV-9B2X6P`: kronologi **didekripsi** untuk pendamping. Tunjukkan catatan dari admin di *Catatan internal*.
3. Tab **Pesan**: kirim "Halo, aku Dewi. Kamu aman sekarang?"
4. Di jendela Penyintas (masih di halaman kasus), buka tab Pesan. Pesan muncul **otomatis dalam ±5 detik** tanpa memuat ulang. Balas pesannya.
5. Kembali ke Dewi: balasan muncul dan pesan Dewi berstatus *Dibaca*.

### e. Jadwal sesi (C, ± 40 detik)
1. Dewi membuka **Jadwal & slot** dan membuka slot besok pukul 14.00. Coba slot yang bertabrakan untuk menunjukkan penolakan.
2. Penyintas membuka tab **Jadwal sesi**, memilih slot itu, lalu **Pesan sesi**.
3. Jelaskan: slot dikunci dengan transaksi database, jadi dua orang tidak bisa memesan slot yang sama (diuji otomatis di `tests/BookingTest.php`).

### f. Rujukan dan selesai (C, ± 40 detik)
1. Dewi memilih status **Dirujuk** → pilih *UPTD PPA Kabupaten Banyumas*, tulis catatan untuk penyintas, lalu centang persetujuan.
2. Penyintas melihat tab **Rujukan** berisi layanan tujuan dan catatan di timeline.
3. Buka **Layanan bantuan** di situs publik: filter provinsi *Jawa Tengah*, jenis *UPTD PPA*. Jelaskan label "Perlu verifikasi" dan bahwa kontak hanya tampil setelah dicek tim.

### g. Akun penyintas dan edukasi (C, ± 40 detik)
1. Masuk sebagai `rani@demo.id`: dashboard berisi laporan `HV-7Q4K2M`, sesi besok, dan pesan baru.
2. Buka **Edukasi** → artikel *Sembilan bentuk kekerasan seksual*. Tunjukkan sumber rujukan.

## 7:45 Keamanan & pengujian (A)
- Buka phpMyAdmin → tabel `reports` → kolom `chronology_enc` berisi teks sandi, bukan cerita aslinya.
- Buka **Audit log** sebagai admin: akses Dewi ke kasus dan akses yang ditolak tercatat.
- Jalankan `C:\xampp\php\php.exe tests\run.php` di terminal: 105 tes lulus.

## 8:30 Kesimpulan (semua)
- Haven memenuhi 11 modul yang saling terhubung, dari pelaporan sampai statistik.
- Data korban dilindungi dengan enkripsi, pembatasan akses, dan audit log.
- Rencana lanjutan: verifikasi data layanan bersama UPTD PPA Banyumas, notifikasi email nyata, dan uji coba dengan Satgas PPKPT kampus.
- Tutup dengan pesan: "Jika kamu dalam bahaya sekarang, telepon 112 atau SAPA 129."
