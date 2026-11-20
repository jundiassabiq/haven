-- Haven demo data. Import after schema.sql.
-- All demo accounts use the password: haven123
USE haven_v2;

-- ---------- Users (Task 3) ----------
INSERT INTO users (id, name, email, password_hash, role, region, allow_email, allow_whatsapp, safe_time, created_at, updated_at) VALUES
 (1, 'Admin Haven',     'admin@demo.id', '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'admin',     NULL,          1, 0, NULL, '2026-08-01 08:00:00', '2026-08-01 08:00:00'),
 (2, 'Dewi Anggraini',  'dewi@demo.id',  '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'companion', 'Banyumas',    1, 0, NULL, '2026-08-01 08:00:00', '2026-08-01 08:00:00'),
 (3, 'Rani',            'rani@demo.id',  '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'survivor',  NULL,          1, 0, 'Hari kerja setelah pukul 19.00', '2026-09-10 20:00:00', '2026-09-10 20:00:00'),
 (4, 'Sari Pratiwi',    'sari@demo.id',  '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'companion', 'Purbalingga', 1, 0, NULL, '2026-08-01 08:00:00', '2026-08-01 08:00:00'),
 (5, 'Bayu Nugroho',    'bayu@demo.id',  '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'companion', 'Banyumas',    1, 0, NULL, '2026-08-01 08:00:00', '2026-08-01 08:00:00'),
 (6, 'Laras Wulandari', 'laras@demo.id', '$2y$10$0BUzX6IVDmwuOmiHaXo71uNPudbhIETCLKAwIxet5zNdm4vK31kxW', 'companion', 'Semarang',    1, 0, NULL, '2026-08-01 08:00:00', '2026-08-01 08:00:00');

-- ---------- Services (Task 10) ----------
-- Only the two national numbers carry contacts. Every regional entry is UNVERIFIED and has no phone:
-- the team must call each institution and fill in contacts via Admin > Layanan before the demo.
INSERT INTO services (name, type, province, city, address, phone, whatsapp, hours, is_24h, tags, is_verified, verified_at, verified_note, created_at, updated_at) VALUES
 ('SAPA 129 (Kementerian PPPA)', 'hotline', 'Nasional', 'Nasional', NULL, '129', '08111-129-129', '24 jam', 1, 'Pengaduan, Rujukan', 1, '2026-11-20', 'Hotline nasional resmi KemenPPPA. Cek ulang sebelum demo.', NOW(), NOW()),
 ('Layanan darurat nasional', 'hotline', 'Nasional', 'Nasional', NULL, '112', NULL, '24 jam', 1, 'Darurat', 1, '2026-11-20', 'Nomor darurat nasional. Cek ulang sebelum demo.', NOW(), NOW()),
 ('UPTD PPA Kabupaten Banyumas', 'uptd', 'Jawa Tengah', 'Banyumas', NULL, NULL, NULL, 'Senin sampai Jumat', 0, 'Pendampingan, Psikologis, Hukum', 0, NULL, NULL, NOW(), NOW()),
 ('Unit PPA Satreskrim Polresta Banyumas', 'polisi', 'Jawa Tengah', 'Banyumas', NULL, NULL, NULL, '24 jam', 1, 'Laporan polisi', 0, NULL, NULL, NOW(), NOW()),
 ('RSUD Prof. Dr. Margono Soekarjo', 'rs', 'Jawa Tengah', 'Banyumas', NULL, NULL, NULL, '24 jam (IGD)', 1, 'Medis, Visum', 0, NULL, NULL, NOW(), NOW()),
 ('Satgas PPKPT Universitas Jenderal Soedirman', 'kampus', 'Jawa Tengah', 'Banyumas', NULL, NULL, NULL, 'Hari kerja', 0, 'Kampus, Pendampingan', 0, NULL, NULL, NOW(), NOW()),
 ('Layanan psikologi kampus (contoh)', 'psikolog', 'Jawa Tengah', 'Banyumas', NULL, NULL, NULL, 'Dengan janji', 0, 'Konseling', 0, NULL, NULL, NOW(), NOW()),
 ('UPTD PPA Kabupaten Purbalingga', 'uptd', 'Jawa Tengah', 'Purbalingga', NULL, NULL, NULL, 'Senin sampai Jumat', 0, 'Pendampingan', 0, NULL, NULL, NOW(), NOW()),
 ('UPTD PPA Kabupaten Cilacap', 'uptd', 'Jawa Tengah', 'Cilacap', NULL, NULL, NULL, 'Senin sampai Jumat', 0, 'Pendampingan', 0, NULL, NULL, NOW(), NOW()),
 ('UPTD PPA Provinsi Jawa Tengah', 'uptd', 'Jawa Tengah', 'Semarang', NULL, NULL, NULL, 'Senin sampai Jumat', 0, 'Pendampingan, Rumah aman', 0, NULL, NULL, NOW(), NOW()),
 ('LBH APIK Semarang', 'lbh', 'Jawa Tengah', 'Semarang', NULL, NULL, NULL, 'Hari kerja', 0, 'Hukum, Gratis', 0, NULL, NULL, NOW(), NOW()),
 ('UPTD PPA Provinsi DI Yogyakarta', 'uptd', 'DI Yogyakarta', 'Yogyakarta', NULL, NULL, NULL, 'Senin sampai Jumat', 0, 'Pendampingan', 0, NULL, NULL, NOW(), NOW());
