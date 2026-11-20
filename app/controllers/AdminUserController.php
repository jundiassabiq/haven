<?php
declare(strict_types=1);

/** Module 1 (admin): staff accounts. Survivors are never listed individually. */
final class AdminUserController
{
    public function index(): void
    {
        View::show('pages/admin/users', [
            'title' => 'Pengguna | Admin Haven',
            'active' => 'pengguna',
            'staff' => User::staff(),
            'survivors' => User::survivorCount(),
            'me' => Auth::user(),
        ], 'app');
    }

    public function store(): void
    {
        $in = [
            'name' => input('name'),
            'email' => User::normalizeEmail(input('email')),
            'role' => input('role'),
            'region' => input('region'),
        ];
        $errors = Validator::make($in, [
            'name' => 'required|max:80',
            'email' => 'required|email',
            'role' => 'required|in:companion,admin',
            'region' => 'max:100',
        ], ['name.required' => 'Tulis nama lengkap.', 'email.required' => 'Masukkan email untuk undangan.']);
        if (!isset($errors['email']) && User::emailTaken($in['email'])) {
            $errors['email'] = 'Email ini sudah terdaftar.';
        }
        if ($errors) {
            back_with('/admin/pengguna', $errors, $in);
        }

        // Random password nobody knows; the invitee sets their own through the invite link.
        $id = User::create($in['name'], $in['email'], bin2hex(random_bytes(24)), $in['role'], $in['region'] ?: null);
        $user = User::find($id);
        AuthController::sendResetMail($user, PasswordReset::issue($id, PasswordReset::INVITE_MINUTES), true);
        Audit::log('user.create', 'user', (string) $id, 'Membuat akun ' . Auth::ROLE_LABEL[$in['role']] . ': ' . $in['name']);

        Session::flash('success', 'Akun dibuat. Undangan untuk membuat kata sandi dikirim ke ' . $in['email'] . '.');
        redirect('/admin/pengguna');
    }

    public function setStatus(array $p): void
    {
        $target = $this->staffOr404((int) $p['id']);
        $active = input('active') === '1';
        User::setActive((int) $target['id'], $active);
        Audit::log($active ? 'user.activate' : 'user.deactivate', 'user', (string) $target['id'], ($active ? 'Mengaktifkan ' : 'Menonaktifkan ') . $target['name']);
        Session::flash('success', 'Akun ' . $target['name'] . ($active ? ' diaktifkan kembali.' : ' dinonaktifkan.'));
        redirect('/admin/pengguna');
    }

    public function sendReset(array $p): void
    {
        $target = $this->staffOr404((int) $p['id']);
        AuthController::sendResetMail($target, PasswordReset::issue((int) $target['id']), false);
        Audit::log('user.reset_link', 'user', (string) $target['id'], 'Mengirim tautan reset ke ' . $target['name']);
        Session::flash('success', 'Tautan reset kata sandi dikirim ke ' . $target['email'] . '.');
        redirect('/admin/pengguna');
    }

    /** Admin may act on companions and other admins, never on themselves or on survivors. */
    private function staffOr404(int $id): array
    {
        $u = User::find($id);
        if (!$u || $u['role'] === 'survivor') {
            throw new HttpException(404);
        }
        if ((int) $u['id'] === Auth::id()) {
            throw new HttpException(403, 'Kamu tidak bisa mengubah status akunmu sendiri dari halaman ini.');
        }
        return $u;
    }
}
