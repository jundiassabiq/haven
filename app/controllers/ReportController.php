<?php
declare(strict_types=1);

/** Module 2: submitting a report (anonymous or with a survivor account). */
final class ReportController
{
    private static function accountAllowed(): bool
    {
        return Auth::role() === 'survivor';
    }

    public function create(): void
    {
        View::show('pages/report/form', [
            'title' => 'Buat laporan | Haven',
            'active' => 'lapor',
            'accountAllowed' => self::accountAllowed(),
            'defaultIdentity' => self::accountAllowed() ? 'akun' : 'anonim',
        ]);
    }

    public function store(): void
    {
        $fields = ['jenis', 'bahaya', 'kronologi', 'berlangsung', 'tanggal', 'tempat', 'provinsi', 'kota', 'pelaku', 'identitas', 'email', 'wa', 'kontak_email', 'kontak_wa', 'setuju'];
        $in = [];
        foreach ($fields as $f) {
            $in[$f] = input($f);
        }

        $limiter = RateLimiter::default();
        $key = 'report:ip:' . client_ip();
        if ($limiter->tooManyAttempts($key, 5)) {
            throw new HttpException(429, 'Terlalu banyak laporan dari jaringan ini dalam satu jam. Coba lagi dalam ' . RateLimiter::waitText($limiter->availableIn($key)) . ', atau hubungi SAPA 129.');
        }

        $errors = Report::validationErrors($in, self::accountAllowed());

        $files = [];
        $uploads = Upload::normalize($_FILES['bukti'] ?? null);
        if (count($uploads) > Upload::MAX_FILES) {
            $errors['bukti'] = 'Maksimal ' . Upload::MAX_FILES . ' file bukti.';
        } else {
            foreach ($uploads as $i => $u) {
                $err = Upload::check((string) $u['tmp_name'], (int) $u['size'], (int) $u['error'], 'File ke-' . ($i + 1));
                if ($err !== null) {
                    $errors['bukti'] = $err;
                    break;
                }
                $files[] = ['tmp_name' => $u['tmp_name'], 'size' => (int) $u['size'], 'name' => mb_substr((string) $u['name'], 0, 200), 'mime' => Upload::mime($u['tmp_name'])];
            }
        }

        if ($errors) {
            back_with('/lapor', $errors, $in);
        }

        $limiter->hit($key, 3600);
        $userId = ($in['identitas'] === 'akun' && self::accountAllowed()) ? Auth::id() : null;
        $created = Report::create($in, $userId, $files);

        Audit::log('report.create', 'report', $created['code'], Report::CATEGORIES[$in['jenis']] . ($userId ? ' · berakun' : ' · anonim') . ($files ? ' · ' . count($files) . ' bukti' : ''), $userId ? Auth::user() : null, $userId ? null : 'Anonim');
        Notifier::admins($created['id'], 'laporan', 'Laporan baru: ' . $created['code'] . ($in['bahaya'] === 'ya' ? ' (menyatakan dalam bahaya)' : ''), '/admin/kasus/' . $created['code']);

        CaseAccess::grant($created['id']);
        Session::flash('new_report', ['code' => $created['code'], 'pin' => $created['pin']]);
        redirect('/lapor/terkirim');
    }

    /** Shows code + PIN exactly once (flash data). */
    public function sent(): void
    {
        $r = Session::getFlash('new_report');
        if (!is_array($r)) {
            redirect('/lacak');
        }
        View::show('pages/report/sent', ['title' => 'Laporan terkirim | Haven', 'active' => 'lapor', 'code' => $r['code'], 'pin' => $r['pin']]);
    }
}
