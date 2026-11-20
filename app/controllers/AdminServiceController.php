<?php
declare(strict_types=1);

/** Module 9 (admin): create, edit, verify, and delete directory entries. */
final class AdminServiceController
{
    public function index(): void
    {
        View::show('pages/admin/services', ['title' => 'Layanan | Admin Haven', 'active' => 'layanan', 'services' => Service::all()], 'app');
    }

    public function create(): void
    {
        View::show('pages/admin/service-form', ['title' => 'Tambah layanan | Admin Haven', 'active' => 'layanan', 'service' => null, 'referrals' => 0], 'app');
    }

    private static function loadOr404(int $id): array
    {
        $s = Service::find($id);
        if (!$s) {
            throw new HttpException(404);
        }
        return $s;
    }

    public function edit(array $p): void
    {
        $s = self::loadOr404((int) $p['id']);
        View::show('pages/admin/service-form', [
            'title' => $s['name'] . ' | Admin Haven',
            'active' => 'layanan',
            'service' => $s,
            'referrals' => (int) Database::value('SELECT COUNT(*) FROM reports WHERE service_id = ?', [$s['id']]),
        ], 'app');
    }

    private static function input(): array
    {
        $keys = ['name', 'type', 'province', 'city', 'address', 'phone', 'whatsapp', 'hours', 'tags', 'verified_at', 'verified_note', '_sent'];
        $in = [];
        foreach ($keys as $k) {
            $in[$k] = input($k);
        }
        $in['is_verified'] = input_bool('is_verified') ? '1' : '';
        $in['is_24h'] = input_bool('is_24h') ? '1' : '';
        if ($in['province'] === 'Nasional') {
            $in['city'] = 'Nasional';
        }
        return $in;
    }

    public function store(): void
    {
        $in = self::input();
        $errors = Service::validationErrors($in);
        if ($errors) {
            back_with('/admin/layanan/baru', $errors, $in);
        }
        $id = Service::save($in, null);
        Audit::log('service.save', 'service', (string) $id, 'Menambah ' . $in['name']);
        Session::flash('success', 'Layanan ' . $in['name'] . ' ditambahkan.');
        redirect('/admin/layanan');
    }

    public function update(array $p): void
    {
        $s = self::loadOr404((int) $p['id']);
        $in = self::input();
        $errors = Service::validationErrors($in);
        if ($errors) {
            back_with('/admin/layanan/' . $s['id'], $errors, $in);
        }
        Service::save($in, (int) $s['id']);
        $verifiedNow = $in['is_verified'] && !(int) $s['is_verified'];
        Audit::log('service.save', 'service', (string) $s['id'], ($verifiedNow ? 'Memverifikasi ' : 'Mengubah ') . $in['name']);
        Session::flash('success', 'Layanan disimpan.' . ($verifiedNow ? ' Kontaknya sekarang tampil di direktori publik.' : ''));
        redirect('/admin/layanan');
    }

    public function delete(array $p): void
    {
        $s = self::loadOr404((int) $p['id']);
        if (!Service::delete((int) $s['id'])) {
            Session::flash('error', 'Layanan ini dipakai untuk rujukan kasus, jadi tidak bisa dihapus.');
            redirect('/admin/layanan/' . $s['id']);
        }
        Audit::log('service.delete', 'service', (string) $s['id'], 'Menghapus ' . $s['name']);
        Session::flash('success', 'Layanan ' . $s['name'] . ' dihapus.');
        redirect('/admin/layanan');
    }
}
