<?php
declare(strict_types=1);

/*
 * Fills a freshly reset database with realistic demo activity for the presentation video:
 * ~45 reports over the last 12 months, messages, notes, referrals, and sessions.
 * Report content is encrypted with this installation's APP_KEY, which is why this is a PHP script and not SQL.
 *
 *   php bin/db-reset.php && php bin/seed-demo.php
 *
 * Fixed demo cases (see docs/demo-script.md):
 *   HV-9B2X6P / PIN 482915  anonymous, "in danger", waiting in the admin queue
 *   HV-7Q4K2M               Rani's account case, accompanied by Dewi, with messages and a session tomorrow
 */

require dirname(__DIR__) . '/app/bootstrap.php';

if ((int) Database::value('SELECT COUNT(*) FROM reports') > 0 && !in_array('--force', $argv, true)) {
    fwrite(STDERR, "Database sudah berisi laporan. Jalankan php bin/db-reset.php dulu, atau tambahkan --force.\n");
    exit(1);
}
mt_srand(20261120);

$admin = User::find(1);
$companions = array_map(static fn ($id) => User::find($id), [2, 4, 5, 6]);
$rani = User::find(3);

$stories = [
    'nonfisik' => [
        'Atasan sering berkomentar tentang tubuh saya di depan rekan kerja dan menganggapnya candaan. Saya sudah meminta berhenti tapi tidak digubris.',
        'Seseorang di grup angkatan terus mengirim stiker dan pesan bernuansa seksual ke saya. Saya merasa tidak nyaman datang ke kelas.',
        'Saat menunggu bus, sekelompok orang bersiul dan mengomentari pakaian saya setiap hari di halte yang sama.',
    ],
    'ksbe' => [
        'Mantan pasangan mengancam akan menyebarkan foto pribadi saya kalau saya tidak mau kembali kepadanya.',
        'Akun tanpa nama mengirim gambar tidak senonoh berkali-kali dan tahu alamat kos saya.',
        'Foto saya diedit menjadi konten tidak pantas dan disebar di grup kampus.',
    ],
    'fisik' => [
        'Di kereta yang padat, seseorang sengaja meraba saya. Saya membeku dan tidak sempat melihat wajahnya dengan jelas.',
        'Rekan kerja beberapa kali menyentuh pinggang saya saat lembur, padahal saya sudah menghindar.',
    ],
    'pemaksaan' => ['Keluarga memaksa saya menikah dengan orang yang tidak saya kenal untuk melunasi utang.', 'Pasangan memaksa saya melakukan hal yang tidak saya setujui dan mengancam akan pergi.'],
    'eksploitasi' => ['Saya dijanjikan pekerjaan, tetapi kemudian dipaksa melayani tamu dan uangnya diambil.'],
    'belum-yakin' => ['Saya tidak yakin apakah yang terjadi termasuk kekerasan. Dosen pembimbing sering mengajak bertemu berdua di luar kampus.'],
];
$plan = ['nonfisik' => 15, 'ksbe' => 11, 'fisik' => 9, 'pemaksaan' => 5, 'eksploitasi' => 2, 'belum-yakin' => 3];
$cities = [['Jawa Tengah', 'Banyumas'], ['Jawa Tengah', 'Banyumas'], ['Jawa Tengah', 'Purbalingga'], ['Jawa Tengah', 'Cilacap'], ['Jawa Tengah', 'Semarang'], ['DI Yogyakarta', 'Yogyakarta']];
$companionLines = ['Halo, terima kasih sudah percaya bercerita. Aku pendampingmu. Apakah sekarang kamu berada di tempat yang aman?', 'Pelan-pelan saja, tidak ada yang perlu dijelaskan ulang kalau belum siap.', 'Aku sudah membuka beberapa jadwal kalau kamu ingin bicara lewat panggilan.', 'Kalau kamu setuju, aku bisa bantu hubungkan dengan layanan psikologis di wilayahmu.'];
$survivorLines = ['Iya, aku aman sekarang. Terima kasih sudah membalas.', 'Aku masih bingung harus mulai dari mana.', 'Boleh, aku mau coba sesi minggu ini.', 'Terima kasih, aku merasa lebih tenang setelah bercerita.'];
$services = array_column(Database::all("SELECT id, city FROM services WHERE type IN ('uptd','psikolog','lbh','rs')"), 'id');

$at = static fn (int $ts) => date('Y-m-d H:i:s', $ts);
$created = 0;

/** Creates one report and walks it to $final, backdating every timestamp from $t0. */
$make = static function (string $cat, int $t0, string $final, ?int $userId, ?array $companion, array $extra = []) use ($stories, $cities, $admin, $companionLines, $survivorLines, $services, $at): array {
    [$prov, $city] = $cities[mt_rand(0, count($cities) - 1)];
    $story = $stories[$cat][mt_rand(0, count($stories[$cat]) - 1)];
    $in = $extra + [
        'jenis' => $cat, 'bahaya' => mt_rand(0, 9) === 0 ? 'ya' : 'tidak', 'kronologi' => $story, 'berlangsung' => ['ya', 'tidak', 'tidak-tahu'][mt_rand(0, 2)],
        'tanggal' => date('Y-m-d', $t0 - mt_rand(1, 20) * 86400), 'tempat' => Report::PLACES[mt_rand(0, count(Report::PLACES) - 1)], 'provinsi' => $prov, 'kota' => $city,
        'pelaku' => Report::RELATIONS[mt_rand(0, 6)], 'identitas' => $userId ? 'akun' : 'anonim', 'kontak_email' => '', 'email' => '', 'kontak_wa' => '', 'wa' => '',
    ];
    $c = Report::create($in, $userId, []);
    $id = $c['id'];
    $r = Report::find($id);
    $times = ['diterima' => $t0];
    if ($final !== 'diterima') {
        Auth::actAs($admin);
        $r = CaseWorkflow::transition($r, 'ditinjau');
        $times['ditinjau'] = $t0 + mt_rand(1, 6) * 3600;
        if ($final === 'ditutup') {
            $r = CaseWorkflow::transition($r, 'ditutup', ['reason' => 'Laporan duplikat dari laporan sebelumnya dengan kronologi yang sama.']);
            $times['ditutup'] = $times['ditinjau'] + 3600;
        } else {
            $r = CaseWorkflow::transition($r, 'didampingi', ['companion_id' => (int) $companion['id']]);
            $times['didampingi'] = $times['ditinjau'] + mt_rand(1, 3) * 3600;
            Auth::actAs($companion);
            $first = min($times['didampingi'] + mt_rand(1, 8) * 3600, time() - 3600);
            $n = mt_rand(2, 4);
            for ($i = 0; $i < $n; $i++) {
                Message::create($id, 'companion', (int) $companion['id'], $companionLines[$i % count($companionLines)]);
                Message::create($id, 'survivor', $userId, $survivorLines[$i % count($survivorLines)]);
            }
            CaseNote::add($id, (int) $companion['id'], 'Kontak pertama', 'Penyintas dalam keadaan aman. Sepakat untuk sesi lanjutan.');
            if (in_array($final, ['dirujuk', 'selesai'], true) && ($final === 'dirujuk' || mt_rand(0, 2) === 0)) {
                $r = CaseWorkflow::transition($r, 'dirujuk', ['service_id' => $services[mt_rand(0, count($services) - 1)], 'note' => 'Kami hubungkan dengan layanan lanjutan sesuai persetujuanmu.']);
                $times['dirujuk'] = $first + mt_rand(5, 12) * 86400;
            }
            if ($final === 'selesai') {
                $r = CaseWorkflow::transition($r, 'selesai', ['note' => 'Terima kasih sudah berjalan bersama. Kamu bisa kembali kapan saja.']);
                $times['selesai'] = max($first, $times['dirujuk'] ?? $first) + mt_rand(5, 25) * 86400;
            }
            // Backdate messages between first contact and the last step.
            $end = min(time() - 600, max($times) + 86400);
            $msgs = Database::all('SELECT id FROM messages WHERE report_id = ? ORDER BY id', [$id]);
            $step = min(86400, max(60, (int) (($end - $first) / max(1, count($msgs)))));
            foreach ($msgs as $k => $m) {
                Database::run('UPDATE messages SET created_at = ?, read_at = ? WHERE id = ?', [$at($first + $k * $step), $at($first + $k * $step + 600), $m['id']]);
            }
        }
    }
    foreach ($times as $status => $ts) {
        $times[$status] = min($ts, time() - 300);
        Database::run('UPDATE report_status_logs SET created_at = ? WHERE report_id = ? AND to_status = ?', [$at($times[$status]), $id, $status]);
    }
    Database::run('UPDATE reports SET created_at = ?, updated_at = ? WHERE id = ?', [$at($t0), $at(max($times)), $id]);
    Database::run('UPDATE case_notes SET created_at = ? WHERE report_id = ?', [$at(($times['didampingi'] ?? $t0) + 7200), $id]);
    Auth::actAs(null);
    return Report::find($id);
};

// ---------- Historical cases: more recent months are busier ----------
$queue = [];
foreach ($plan as $cat => $n) {
    for ($i = 0; $i < $n; $i++) {
        $queue[] = $cat;
    }
}
shuffle($queue);
$total = count($queue);
foreach ($queue as $i => $cat) {
    $ageDays = (int) round(((1 - ($i / $total)) ** 1.4) * 355) + 4; // spread over ~12 months, denser recently
    $t0 = time() - $ageDays * 86400 - mt_rand(0, 20) * 3600;
    $final = match (true) {
        $ageDays < 30 => ['didampingi', 'didampingi', 'dirujuk'][mt_rand(0, 2)],
        $ageDays < 70 => ['didampingi', 'dirujuk', 'selesai'][mt_rand(0, 2)],
        $i % 17 === 0 => 'ditutup',
        default => 'selesai',
    };
    $make($cat, $t0, $final, null, $companions[$i % count($companions)]);
    $created++;
}
// Two fresh reports waiting for triage.
foreach (['nonfisik', 'ksbe'] as $k => $cat) {
    $make($cat, time() - (5 + $k * 20) * 3600, 'diterima', null, null);
    $created++;
}

// Completed sessions for the public "sessions completed" number.
foreach (Database::all("SELECT id, assigned_to, created_at FROM reports WHERE status IN ('dirujuk','selesai') AND assigned_to IS NOT NULL") as $k => $r) {
    $ts = strtotime($r['created_at']) + (3 + $k % 5) * 86400 + 10 * 3600;
    if ($ts > time() - 86400) {
        continue;
    }
    $slot = Slot::create((int) $r['assigned_to'], $at($ts), 45, 'suara', null);
    Database::run("INSERT INTO bookings (slot_id, report_id, status, created_at) VALUES (?, ?, 'aktif', ?)", [$slot, $r['id'], $at($ts - 2 * 86400)]);
}

// Older notifications are history; mark them read so the demo starts with a clean bell.
Database::run('UPDATE notifications SET read_at = created_at');

// ---------- Fixed demo case 1: Rani's account case with Dewi ----------
$dewi = $companions[0];
$rCase = $make('nonfisik', time() - 8 * 86400, 'didampingi', (int) $rani['id'], $dewi, [
    'kronologi' => "Seorang teman satu angkatan sering mengirim pesan dan komentar bernuansa seksual di grup kelas dan lewat pesan pribadi. Saya sudah meminta berhenti, tapi ia menganggapnya bercanda dan teman lain ikut tertawa.\n\nSaya jadi enggan datang ke kelas yang sama dengannya dan susah tidur. Saya belum tahu apakah ingin melapor ke kampus.",
    'provinsi' => 'Jawa Tengah', 'kota' => 'Banyumas', 'tempat' => 'Kampus atau sekolah', 'pelaku' => 'Teman kuliah atau rekan kerja', 'berlangsung' => 'ya', 'bahaya' => 'tidak',
    'kontak_email' => '1', 'email' => 'rani.aman@contoh.id',
]);
Database::run('UPDATE reports SET code = ? WHERE id = ?', ['HV-7Q4K2M', $rCase['id']]);
Database::run('UPDATE notifications SET link = REPLACE(link, ?, ?) WHERE report_id = ?', [$rCase['code'], 'HV-7Q4K2M', $rCase['id']]);
Message::create((int) $rCase['id'], 'companion', (int) $dewi['id'], 'Itu wajar sekali setelah mengalami hal seperti ini. Aku sudah membuka beberapa jadwal minggu ini kalau kamu mau bicara lewat panggilan. Tidak ada paksaan ya.');
Notifier::survivor(Report::find((int) $rCase['id']), 'pesan', 'Pesan baru dari pendamping', '#pesan');
$tomorrow = date('Y-m-d', time() + 86400);
$booked = Slot::create((int) $dewi['id'], "$tomorrow 10:00:00", 45, 'suara', null);
Slot::create((int) $dewi['id'], "$tomorrow 15:30:00", 45, 'video', null);
Slot::create((int) $dewi['id'], date('Y-m-d', time() + 3 * 86400) . ' 09:00:00', 60, 'tatap', 'Ruang konseling UPTD PPA Banyumas');
Slot::create((int) $dewi['id'], date('Y-m-d', time() + 3 * 86400) . ' 19:00:00', 45, 'suara', null);
Booking::book($booked, Report::find((int) $rCase['id']));
$created++;

// ---------- Fixed demo case 2: anonymous, in danger, waiting ----------
$aCase = $make('fisik', time() - 25 * 60, 'diterima', null, null, [
    'bahaya' => 'ya', 'berlangsung' => 'ya', 'provinsi' => 'Jawa Tengah', 'kota' => 'Banyumas', 'tempat' => 'Tempat kerja', 'pelaku' => 'Teman kuliah atau rekan kerja',
    'kronologi' => 'Rekan kerja beberapa kali menyentuh saya tanpa izin saat lembur, dan hari ini ia menunggu di depan kos. Saya takut pulang sendirian.',
    'kontak_wa' => '1', 'wa' => '0812-0000-0000',
]);
Database::run('UPDATE reports SET code = ?, pin_hash = ?, urgency = ? WHERE id = ?', ['HV-9B2X6P', password_hash('482915', PASSWORD_DEFAULT), 'tinggi', $aCase['id']]);
Notifier::admins((int) $aCase['id'], 'laporan', 'Laporan baru: HV-9B2X6P (menyatakan dalam bahaya)', '/admin/kasus/HV-9B2X6P');
$created++;

echo "Data demo dibuat: $created laporan, " . Database::value('SELECT COUNT(*) FROM messages') . ' pesan, '
    . Database::value('SELECT COUNT(*) FROM bookings') . " sesi.\n";
echo "  HV-9B2X6P / PIN 482915  anonim, dalam bahaya, menunggu di antrean admin\n";
echo "  HV-7Q4K2M               laporan berakun Rani (rani@demo.id), didampingi Dewi, sesi besok 10.00\n";
