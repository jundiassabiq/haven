-- Haven education content (Tasks 11 and 12). Original text written for this project; it refers to, but does not copy,
-- UU No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual (UU TPKS). Import after seed.sql.
USE haven_v2;

INSERT INTO article_categories (id, slug, name) VALUES
 (1, 'mengenali', 'Mengenali'),
 (2, 'setelah', 'Setelah kejadian'),
 (3, 'hak', 'Hak korban'),
 (4, 'mendampingi', 'Mendampingi'),
 (5, 'digital', 'Keamanan digital');

INSERT INTO articles (id, category_id, author_id, title, slug, excerpt, body, sources, cover_path, cover_alt, read_minutes, status, published_at, created_at, updated_at) VALUES
(1, 1, 1, 'Sembilan bentuk kekerasan seksual menurut UU TPKS', 'sembilan-bentuk-kekerasan-seksual',
 'Mengenali bentuknya adalah langkah pertama untuk berkata "ini tidak benar".',
'Kekerasan seksual tidak selalu berupa sentuhan, dan tidak selalu dilakukan orang asing. Pada 2022, Indonesia mengesahkan Undang-Undang Nomor 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, yang sering disingkat UU TPKS. Untuk pertama kalinya berbagai bentuk kekerasan seksual diakui dengan jelas, termasuk yang dulu sering dianggap "bercanda" atau "urusan pribadi".

Berikut sembilan bentuk yang disebut dalam undang-undang itu, dijelaskan dengan bahasa sehari-hari.

## 1. Pelecehan seksual nonfisik
Ucapan, isyarat, tulisan, atau perbuatan bernuansa seksual yang ditujukan pada tubuh atau seksualitas seseorang dan membuatnya merasa direndahkan. Contohnya siulan dan komentar tentang tubuh di jalan, atau lelucon seksual yang terus diulang meski sudah diminta berhenti.

## 2. Pelecehan seksual fisik
Sentuhan atau kontak fisik bernuansa seksual tanpa persetujuan, misalnya meraba di kendaraan umum atau sengaja menempelkan tubuh.

## 3. Pemaksaan kontrasepsi
Memaksa seseorang memakai alat kontrasepsi dengan ancaman, tipu daya, atau penyalahgunaan kekuasaan, sehingga ia kehilangan kendali atas fungsi reproduksinya untuk sementara.

## 4. Pemaksaan sterilisasi
Seperti di atas, tetapi akibatnya permanen: seseorang dibuat tidak bisa memiliki keturunan tanpa persetujuan yang bebas.

## 5. Pemaksaan perkawinan
Memaksa seseorang menikah, termasuk menikahkan korban dengan pelaku dengan alasan "menutup aib". Perkawinan anak juga termasuk di dalamnya.

## 6. Penyiksaan seksual
Perbuatan seksual yang menyakiti atau merendahkan, dilakukan untuk mengintimidasi, menghukum, atau memaksa pengakuan dari seseorang.

## 7. Eksploitasi seksual
Memanfaatkan tubuh atau seksualitas seseorang untuk keuntungan, dengan memanfaatkan kerentanan, ketergantungan, atau kepercayaannya.

## 8. Perbudakan seksual
Membatasi kebebasan seseorang dan menjadikannya tidak berdaya untuk dieksploitasi secara seksual.

## 9. Kekerasan seksual berbasis elektronik
Merekam atau mengambil gambar bermuatan seksual tanpa izin, menyebarkan konten seksual seseorang tanpa persetujuannya, atau menguntit lewat perangkat elektronik untuk tujuan seksual. Ini tetap kekerasan meskipun foto atau videonya awalnya dibuat secara sukarela.

> Bukan salahmu
> Kesalahan selalu ada pada pelaku. Pakaian, tempat, waktu, atau hubunganmu dengan pelaku tidak membuat kekerasan itu menjadi salahmu.

## Kalau kamu mengenali salah satunya
Kamu tidak harus langsung melapor ke polisi. Kamu boleh mulai dengan bercerita ke orang yang kamu percaya, atau [membuat laporan anonim di Haven](/lapor) untuk mendapat pendamping. Pendamping bisa membantumu memahami pilihan, termasuk layanan kesehatan, psikologis, dan bantuan hukum.

- [Cari layanan bantuan terdekat](/layanan)
- [Uji pemahamanmu dengan kuis](/kuis)',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, Pasal 4 ayat (1), JDIH BPK | https://peraturan.bpk.go.id/
Komnas Perempuan, publikasi dan Catatan Tahunan | https://komnasperempuan.go.id/
Kementerian Pemberdayaan Perempuan dan Perlindungan Anak | https://www.kemenpppa.go.id/',
 'img/reaching-hands.jpg', 'Dua tangan saling meraih di latar abu-abu', 6, 'terbit', '2026-11-10 09:00:00', '2026-11-10 09:00:00', '2026-11-10 09:00:00'),

(2, 2, 1, 'Langkah yang bisa kamu ambil setelah kejadian', 'langkah-setelah-kejadian',
 'Tidak ada urutan yang wajib. Ini pilihan-pilihan yang bisa membantu, termasuk menjaga bukti.',
'Tidak ada urutan langkah yang wajib setelah kekerasan seksual. Bagian di bawah ini adalah pilihan. Beberapa, seperti pemeriksaan kesehatan, lebih berguna bila dilakukan dalam beberapa hari pertama.

## Pastikan kamu aman dulu
Jika pelaku masih di dekatmu atau kamu merasa terancam, pindah ke tempat yang lebih aman dan hubungi **112**. Kabari satu orang yang kamu percaya tentang di mana kamu berada.

## Periksakan kesehatanmu sesegera mungkin
Tenaga kesehatan bisa memeriksa luka, memberi obat pencegahan kehamilan, dan mencegah sebagian infeksi menular seksual. Beberapa obat ini paling efektif bila diberikan dalam beberapa hari pertama, jadi lebih cepat lebih baik. Kamu boleh menolak pemeriksaan apa pun yang tidak kamu inginkan.

Jika kamu mungkin ingin menempuh jalur hukum, sampaikan kepada petugas. Rumah sakit dapat membuat visum, yaitu catatan medis yang bisa dipakai sebagai bukti.

## Simpan bukti, meski belum yakin akan melapor
- Jangan hapus pesan, foto, atau riwayat panggilan. Ambil tangkapan layar yang memperlihatkan nama akun dan tanggal.
- Simpan pakaian yang dikenakan dalam kantong kertas, dan sebaiknya tidak dicuci dulu.
- Tulis kronologi dengan kata-katamu sendiri selagi ingatan masih segar: kapan, di mana, siapa saja yang ada.

Menyimpan bukti tidak mewajibkanmu melapor. Ini hanya menjaga pilihanmu tetap terbuka.

## Cari teman bicara
Kamu bisa bercerita ke orang yang kamu percaya, menelepon SAPA 129, atau [membuat laporan di Haven](/lapor) dan menunggu pendamping membalas lewat pesan.

## Melapor ke polisi tidak wajib
Melapor ke polisi bisa dilakukan lewat unit Perlindungan Perempuan dan Anak (PPA). Lembaga bantuan hukum dapat mendampingi tanpa biaya. Jika kamu belum siap, itu juga keputusan yang sah.

> Reaksi setiap orang berbeda
> Ada yang marah, ada yang takut, ada yang mati rasa, dan ada yang merasa biasa saja selama berhari-hari. Semua itu reaksi yang umum. Jika susah tidur atau sulit beraktivitas berlanjut lebih dari beberapa minggu, psikolog bisa membantu.',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, JDIH BPK | https://peraturan.bpk.go.id/
Kementerian Pemberdayaan Perempuan dan Perlindungan Anak, layanan SAPA 129 | https://www.kemenpppa.go.id/
Komnas Perempuan | https://komnasperempuan.go.id/',
 'img/sunset.jpg', 'Anak meraih matahari terbenam', 5, 'terbit', '2026-11-08 09:00:00', '2026-11-08 09:00:00', '2026-11-08 09:00:00'),

(3, 3, 1, 'Hak-hakmu sebagai korban', 'hak-korban',
 'Penanganan, pelindungan, dan pemulihan adalah hakmu, bukan kebaikan hati orang lain.',
'UU TPKS menegaskan bahwa korban kekerasan seksual punya hak yang harus dipenuhi negara. Hak itu dikelompokkan menjadi tiga: hak atas **penanganan**, hak atas **pelindungan**, dan hak atas **pemulihan**. Kamu tidak perlu menunggu proses hukum selesai untuk mendapatkannya.

## Hak atas penanganan
- Mendapat informasi tentang hak-hakmu dan perkembangan kasusmu.
- Mendapat layanan kesehatan, termasuk pemeriksaan dan pengobatan.
- Mendapat pendampingan hukum dan psikologis sejak awal.
- Diperiksa oleh petugas yang tidak menghakimi dan tidak menanyakan hal yang merendahkan.

## Hak atas pelindungan
- Kerahasiaan identitasmu dijaga.
- Dilindungi dari ancaman atau kekerasan lanjutan, termasuk dari pelaku dan keluarganya.
- Tidak kehilangan pekerjaan, pendidikan, atau hak lain karena melapor.

Lembaga Perlindungan Saksi dan Korban (LPSK) dapat memberikan perlindungan jika kamu merasa terancam.

## Hak atas pemulihan
- Dukungan psikologis dan sosial, misalnya konseling dan kelompok dukungan.
- Restitusi, yaitu ganti kerugian dari pelaku atas kerugian yang kamu alami.
- Bantuan untuk kembali beraktivitas, seperti sekolah atau bekerja.

## Di mana hak ini bisa diminta
UPTD Perlindungan Perempuan dan Anak (UPTD PPA) di kabupaten, kota, atau provinsi adalah pintu layanan terpadu. Kamu bisa datang langsung, atau minta pendamping Haven membantu menghubungkan. Lihat [direktori layanan](/layanan).

> Kamu tidak harus melapor ke polisi dulu
> Layanan kesehatan, psikologis, dan pendampingan tetap bisa kamu akses walaupun belum atau tidak ingin menempuh jalur hukum.',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, ketentuan hak korban, JDIH BPK | https://peraturan.bpk.go.id/
Lembaga Perlindungan Saksi dan Korban | https://www.lpsk.go.id/
Kementerian Pemberdayaan Perempuan dan Perlindungan Anak | https://www.kemenpppa.go.id/',
 'img/counseling.jpg', 'Seorang perempuan berbicara dengan konselor', 5, 'terbit', '2026-11-05 09:00:00', '2026-11-05 09:00:00', '2026-11-05 09:00:00'),

(4, 4, 1, 'Cara mendampingi teman yang bercerita', 'cara-mendampingi-teman',
 'Apa yang sebaiknya diucapkan, dan apa yang sebaiknya tidak.',
'Temanmu tidak menunggu solusi darimu. Biasanya ia ingin tahu satu hal: kamu percaya atau tidak. Bagian di bawah berisi kalimat yang membantu dan kalimat yang sebaiknya tidak diucapkan.

## Dengarkan dulu
Beri ruang dan waktu. Biarkan temanmu bercerita dengan caranya sendiri, tanpa dipotong dan tanpa didesak menceritakan detail yang tidak ingin ia ceritakan.

## Kalimat yang membantu
- "Aku percaya kamu."
- "Ini bukan salahmu."
- "Terima kasih sudah mau cerita ke aku."
- "Apa yang kamu butuhkan sekarang?"

## Kalimat yang sebaiknya dihindari
- "Kenapa kamu tidak melawan?"
- "Kamu pakai baju apa waktu itu?"
- "Kok baru cerita sekarang?"
- "Sudah, lupakan saja."

Pertanyaan seperti itu, meski maksudnya baik, bisa terdengar menyalahkan dan membuat korban menyesal telah bercerita.

## Hormati keputusannya
Tawarkan pilihan, jangan memaksa. Kamu bisa menyebutkan layanan yang tersedia, menawarkan menemani ke rumah sakit, atau membantu [membuat laporan](/lapor). Keputusan tetap ada di tangannya, termasuk keputusan untuk tidak melapor.

## Jaga ceritanya
Jangan menceritakan kembali kepada orang lain atau mengunggahnya ke media sosial tanpa izin, walaupun niatmu membantu. Menyebarkan cerita bisa membuatnya kembali terluka dan berisiko bagi keselamatannya.

## Jaga dirimu juga
Mendengar cerita kekerasan bisa membuatmu ikut sedih atau marah, terutama jika pelakunya kamu kenal. Kamu juga boleh bercerita ke konselor, tanpa menyebut nama temanmu.

> Di lingkungan kampus
> Perguruan tinggi wajib memiliki Satuan Tugas Pencegahan dan Penanganan Kekerasan. Temanmu bisa melapor ke Satgas kampus, dan pendamping Haven dapat membantu menyiapkan langkahnya.',
'Kementerian Pemberdayaan Perempuan dan Perlindungan Anak | https://www.kemenpppa.go.id/
Komnas Perempuan | https://komnasperempuan.go.id/
Permendikbudristek No. 55 Tahun 2024 tentang Pencegahan dan Penanganan Kekerasan di Lingkungan Perguruan Tinggi, JDIH BPK | https://peraturan.bpk.go.id/',
 'img/holding-hands.jpg', 'Dua orang saling menggenggam tangan', 4, 'terbit', '2026-11-03 09:00:00', '2026-11-03 09:00:00', '2026-11-03 09:00:00'),

(5, 1, 1, 'Memahami consent: setuju yang sesungguhnya', 'memahami-consent',
 'Diam bukan berarti iya. Persetujuan harus sadar, sukarela, dan boleh ditarik kapan pun.',
'Consent, atau persetujuan, adalah kesepakatan yang jelas dan sukarela untuk melakukan aktivitas seksual tertentu. Tanpa persetujuan, aktivitas itu adalah kekerasan.

## Ciri persetujuan yang sah
- **Sukarela.** Diberikan tanpa paksaan, ancaman, atau tekanan.
- **Sadar.** Orang itu mampu memahami dan memutuskan, tidak dalam keadaan mabuk berat, tertidur, atau pingsan.
- **Spesifik.** Setuju pada satu hal tidak berarti setuju pada hal lain.
- **Bisa ditarik.** Seseorang boleh berubah pikiran kapan saja, bahkan di tengah-tengah.
- **Jelas.** Bila ragu, tanyakan. Ketiadaan penolakan tidak sama dengan persetujuan.

## Yang bukan persetujuan
- Diam atau tidak melawan. Banyak orang membeku karena takut.
- Pernah setuju sebelumnya, atau sedang berpacaran atau menikah.
- "Iya" yang diucapkan karena takut kehilangan pekerjaan, nilai, atau hubungan.
- Persetujuan dari anak. Anak tidak dapat memberi persetujuan seksual.

## Relasi kuasa
Persetujuan sulit benar-benar bebas bila ada ketimpangan kuasa, misalnya dosen dan mahasiswa, atasan dan bawahan, atau orang dewasa dan remaja. Orang yang lebih berkuasa bertanggung jawab untuk tidak memanfaatkan posisinya.

> Cara sederhana
> Tanyakan, dengarkan jawabannya, dan hormati. "Tidak", "nanti dulu", atau keraguan berarti berhenti.',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, JDIH BPK | https://peraturan.bpk.go.id/
Komnas Perempuan | https://komnasperempuan.go.id/',
 'img/support-circle.jpg', 'Kelompok dukungan duduk melingkar', 4, 'terbit', '2026-10-30 09:00:00', '2026-10-30 09:00:00', '2026-10-30 09:00:00'),

(6, 5, 1, 'Kekerasan seksual berbasis elektronik dan cara melindungi diri', 'kekerasan-berbasis-elektronik',
 'Ancaman menyebar foto, pesan cabul, dan penguntitan daring. Begini cara mengamankan bukti dan akun.',
'UU TPKS memasukkan kekerasan seksual berbasis elektronik sebagai tindak pidana. Artinya ancaman menyebar foto, pemerasan dengan konten intim, dan penguntitan lewat ponsel bisa dilaporkan seperti kekerasan lain.

## Bentuk yang sering terjadi
- Menyebarkan atau mengancam menyebarkan foto atau video intim tanpa izin.
- Memeras dengan konten intim, misalnya meminta uang atau konten lain agar tidak disebar.
- Merekam atau memotret bagian tubuh seseorang secara diam-diam.
- Mengirim pesan, gambar, atau video seksual yang tidak diminta.
- Menguntit lewat lokasi, akun, atau perangkat seseorang.
- Membuat konten seksual palsu yang memakai wajah seseorang.

## Jika kamu diancam
- **Jangan menuruti permintaan pelaku.** Memberi uang atau konten lain biasanya tidak menghentikan ancaman.
- **Simpan bukti.** Tangkapan layar percakapan, nama akun, tautan, dan tanggal. Simpan di tempat yang aman.
- **Laporkan ke platform.** Sebagian besar media sosial punya formulir khusus untuk konten intim tanpa persetujuan.
- **Cari dukungan.** Ceritakan ke orang tepercaya atau [buat laporan di Haven](/lapor).

## Amankan akun dan perangkat
- Ganti kata sandi dan aktifkan verifikasi dua langkah.
- Periksa perangkat yang masuk ke akunmu dan keluarkan yang tidak dikenal.
- Matikan berbagi lokasi yang tidak perlu.
- Periksa aplikasi asing di ponsel yang mungkin dipasang orang lain.

> Mengirim foto bukan izin untuk menyebarkannya
> Yang melanggar hukum adalah orang yang menyebarkan atau mengancam menyebarkan, bukan kamu yang dulu mengirimnya.',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, JDIH BPK | https://peraturan.bpk.go.id/
Komnas Perempuan, kekerasan berbasis gender online | https://komnasperempuan.go.id/
Kementerian Pemberdayaan Perempuan dan Perlindungan Anak | https://www.kemenpppa.go.id/',
 'img/phone.jpg', 'Perempuan berhijab memegang ponsel', 6, 'terbit', '2026-10-27 09:00:00', '2026-10-27 09:00:00', '2026-10-27 09:00:00'),

(7, 1, 1, 'Mengenali grooming sejak dini', 'mengenali-grooming',
 'Perhatian berlebih, hadiah, dan permintaan merahasiakan. Tanda-tanda yang sering terlewat.',
'Grooming adalah proses ketika pelaku membangun kedekatan dan kepercayaan secara bertahap agar bisa memanipulasi dan melakukan kekerasan seksual. Grooming sering terjadi pada anak dan remaja, tetapi juga bisa terjadi pada orang dewasa, terutama dalam relasi yang timpang.

## Tanda-tanda yang perlu diwaspadai
- Perhatian, pujian, atau hadiah yang berlebihan dan terasa "terlalu istimewa".
- Mengajak bicara di kanal pribadi dan meminta percakapan dirahasiakan.
- Perlahan memisahkan korban dari teman atau keluarganya.
- Menguji batas sedikit demi sedikit, misalnya lelucon seksual, sentuhan "tidak sengaja", atau meminta foto.
- Membuat korban merasa berutang budi atau takut kehilangan sesuatu.

## Di lingkungan pendidikan dan kerja
Grooming juga bisa dilakukan orang yang punya kuasa, seperti pengajar, pembimbing, atau atasan. Polanya mirip: perhatian khusus, janji keuntungan, lalu permintaan yang melewati batas dengan ancaman tersirat.

## Apa yang bisa dilakukan
- Percayai rasa tidak nyamanmu. Kamu tidak perlu menunggu "bukti kuat" untuk menjaga jarak.
- Ceritakan kepada orang yang kamu percaya. Rahasia yang diminta pelaku adalah alat untuk mengontrol.
- Simpan pesan dan hadiah sebagai bukti.
- Hubungi Satgas kampus, UPTD PPA, atau [buat laporan di Haven](/lapor).

> Untuk orang tua dan pengasuh
> Ajak anak berbicara tentang tubuh dan batasnya, dan tegaskan bahwa tidak ada orang dewasa yang boleh meminta rahasia tentang sentuhan atau foto.',
'UU RI No. 12 Tahun 2022 tentang Tindak Pidana Kekerasan Seksual, JDIH BPK | https://peraturan.bpk.go.id/
Kementerian Pemberdayaan Perempuan dan Perlindungan Anak | https://www.kemenpppa.go.id/
Komnas Perempuan | https://komnasperempuan.go.id/',
 'img/laptop.jpg', 'Tangan mengetik di laptop', 5, 'terbit', '2026-10-24 09:00:00', '2026-10-24 09:00:00', '2026-10-24 09:00:00');

-- ---------- Quiz (Task 12) ----------
INSERT INTO quizzes (id, slug, title, description, status, created_at) VALUES
 (1, 'kenali-kekerasan-seksual', 'Kuis: kekerasan seksual dan UU TPKS',
  'Sepuluh soal pilihan ganda tentang consent, bentuk kekerasan seksual dalam UU TPKS, hak korban, dan cara menanggapi teman yang bercerita. Setelah selesai, setiap soal diberi pembahasan.',
  'terbit', '2026-11-01 09:00:00');

INSERT INTO quiz_questions (id, quiz_id, position, question, explanation, related_article_id) VALUES
 (1, 1, 1, 'Menurut UU TPKS, mana yang termasuk tindak pidana kekerasan seksual?', 'UU TPKS mengakui pelecehan seksual nonfisik, misalnya ucapan, isyarat, atau tulisan bernuansa seksual yang merendahkan, sebagai salah satu tindak pidana kekerasan seksual.', 1),
 (2, 1, 2, 'Persetujuan (consent) yang sah adalah persetujuan yang…', 'Diam, takut, atau tidak sadar bukan tanda setuju. Persetujuan harus jelas, sukarela, dan boleh dicabut kapan pun.', 5),
 (3, 1, 3, 'Menyebarkan foto atau video intim seseorang tanpa izinnya termasuk…', 'UU TPKS mengatur kekerasan seksual berbasis elektronik, termasuk menyebarkan konten seksual seseorang tanpa persetujuannya, meskipun konten itu awalnya dibuat secara sukarela.', 6),
 (4, 1, 4, 'Temanmu bercerita bahwa ia mengalami pelecehan. Respons pertama yang paling membantu adalah…', 'Rasa dipercaya adalah langkah pertama pemulihan. Hindari pertanyaan yang menyalahkan, dan jangan bagikan cerita tanpa izinnya.', 4),
 (5, 1, 5, 'Apa yang dimaksud dengan grooming?', 'Grooming sering dimulai dengan perhatian berlebih, hadiah, dan permintaan merahasiakan sesuatu, lalu batasan korban perlahan dilanggar.', 7),
 (6, 1, 6, 'Setelah kejadian, langkah apa yang membantu menjaga bukti?', 'Bukti digital dan fisik bisa membantu bila kamu memutuskan melapor. Pemeriksaan kesehatan juga penting untuk dirimu sendiri, apa pun keputusanmu nanti.', 2),
 (7, 1, 7, 'UU TPKS menjamin hak korban atas…', 'Hak korban mencakup penanganan, pelindungan, dan pemulihan, termasuk layanan kesehatan, bantuan hukum, dan dukungan psikologis.', 3),
 (8, 1, 8, 'Siapa yang paling sering menjadi pelaku kekerasan seksual?', 'Banyak kasus dilakukan oleh orang yang dikenal korban, seperti pasangan, teman, keluarga, atau orang yang punya kuasa atas korban.', 1),
 (9, 1, 9, 'Siapa yang bisa menjadi korban kekerasan seksual?', 'Kekerasan seksual bisa menimpa siapa saja. Kesalahan selalu ada pada pelaku, bukan pada pakaian atau perilaku korban.', 1),
 (10, 1, 10, 'Nomor layanan pengaduan SAPA dari Kementerian PPPA adalah…', 'SAPA 129 menerima pengaduan kekerasan terhadap perempuan dan anak. Dalam keadaan darurat, hubungi 112.', 2);

INSERT INTO quiz_options (question_id, position, label, is_correct) VALUES
 (1, 1, 'Hanya pemerkosaan', 0), (1, 2, 'Komentar bernuansa seksual yang merendahkan seseorang', 1), (1, 3, 'Hanya kekerasan yang meninggalkan luka fisik', 0), (1, 4, 'Hanya kejadian di tempat umum', 0),
 (2, 1, 'Diberikan sekali dan berlaku seterusnya', 0), (2, 2, 'Dianggap ada selama orang itu diam', 0), (2, 3, 'Diberikan secara sadar, tanpa tekanan, dan bisa ditarik kapan saja', 1), (2, 4, 'Tetap sah meski orang itu sedang mabuk berat', 0),
 (3, 1, 'Urusan pribadi yang tidak bisa dilaporkan', 0), (3, 2, 'Kekerasan seksual berbasis elektronik', 1), (3, 3, 'Hanya pelanggaran etika', 0), (3, 4, 'Bukan kekerasan jika fotonya dibuat atas kemauan korban', 0),
 (4, 1, 'Bertanya kenapa ia tidak melawan', 0), (4, 2, 'Mendengarkan tanpa menghakimi dan mengatakan bahwa kamu percaya', 1), (4, 3, 'Langsung membagikan ceritanya ke grup agar banyak yang membantu', 0), (4, 4, 'Menyarankan untuk melupakannya saja', 0),
 (5, 1, 'Merawat penampilan diri', 0), (5, 2, 'Proses pelaku membangun kedekatan dan kepercayaan untuk memanipulasi korban', 1), (5, 3, 'Pertemanan biasa antara orang dewasa dan anak', 0), (5, 4, 'Bimbingan belajar dari guru', 0),
 (6, 1, 'Menghapus semua pesan agar tidak teringat', 0), (6, 2, 'Menyimpan tangkapan layar dan pakaian dalam kantong kertas, lalu mencari layanan kesehatan', 1), (6, 3, 'Menunggu beberapa bulan sebelum memeriksakan diri', 0), (6, 4, 'Mencuci pakaian yang dikenakan', 0),
 (7, 1, 'Penanganan, pelindungan, dan pemulihan', 1), (7, 2, 'Hanya ganti rugi uang', 0), (7, 3, 'Hanya pendampingan saat sidang', 0), (7, 4, 'Tidak ada hak khusus', 0),
 (8, 1, 'Orang asing di jalan', 0), (8, 2, 'Orang yang dikenal korban', 1), (8, 3, 'Hanya orang dengan gangguan jiwa', 0), (8, 4, 'Tidak ada pola', 0),
 (9, 1, 'Hanya perempuan muda', 0), (9, 2, 'Hanya orang yang berpakaian terbuka', 0), (9, 3, 'Siapa pun, apa pun gender, usia, dan pakaiannya', 1), (9, 4, 'Hanya anak-anak', 0),
 (10, 1, '110', 0), (10, 2, '119', 0), (10, 3, '129', 1), (10, 4, '1500', 0);
