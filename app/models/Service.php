<?php
declare(strict_types=1);

/** Module 9: help-service directory. Contacts are public only after the team verifies them. */
final class Service
{
    public const TYPES = [
        'hotline' => 'Hotline', 'uptd' => 'UPTD PPA', 'polisi' => 'Kepolisian (Unit PPA)', 'rs' => 'Rumah sakit',
        'lbh' => 'Lembaga bantuan hukum', 'psikolog' => 'Psikolog', 'kampus' => 'Satgas kampus',
    ];

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM services WHERE id = ?', [$id]);
    }

    public static function all(): array
    {
        return Database::all(
            "SELECT s.*, (SELECT COUNT(*) FROM reports r WHERE r.service_id = s.id) AS referrals
             FROM services s ORDER BY s.is_verified DESC, (s.province = 'Nasional') DESC, s.province, s.city, s.name"
        );
    }

    /** National services always match a regional filter. */
    public static function search(array $f, int $limit = 200): array
    {
        $w = ['1=1'];
        $p = [];
        if (!empty($f['provinsi'])) {
            $w[] = "(s.province = ? OR s.province = 'Nasional')";
            $p[] = $f['provinsi'];
        }
        if (!empty($f['kota'])) {
            $w[] = "(s.city = ? OR s.province = 'Nasional')";
            $p[] = $f['kota'];
        }
        if (!empty($f['jenis']) && isset(self::TYPES[$f['jenis']])) {
            $w[] = 's.type = ?';
            $p[] = $f['jenis'];
        }
        if (!empty($f['h24'])) {
            $w[] = 's.is_24h = 1';
        }
        if (!empty($f['q'])) {
            $like = '%' . addcslashes(mb_substr($f['q'], 0, 80), '%_\\') . '%';
            $w[] = '(s.name LIKE ? OR s.tags LIKE ? OR s.city LIKE ?)';
            array_push($p, $like, $like, $like);
        }
        return Database::all(
            'SELECT s.* FROM services s WHERE ' . implode(' AND ', $w) .
            " ORDER BY (s.province = 'Nasional') DESC, s.is_verified DESC, s.province, s.city, s.name LIMIT " . max(1, $limit),
            $p
        );
    }

    public static function provinces(): array
    {
        return array_column(Database::all("SELECT DISTINCT province FROM services WHERE province <> 'Nasional' ORDER BY province"), 'province');
    }

    public static function cities(?string $province): array
    {
        return array_column(Database::all(
            "SELECT DISTINCT city FROM services WHERE province <> 'Nasional' AND (? = '' OR province = ?) ORDER BY city",
            [(string) $province, (string) $province]
        ), 'city');
    }

    /** Public JSON shape. Unverified services never expose a phone number. */
    public static function publicArray(array $s): array
    {
        $verified = (bool) $s['is_verified'];
        return [
            'id' => (int) $s['id'],
            'name' => $s['name'],
            'type' => $s['type'],
            'area' => $s['province'] === 'Nasional' ? 'Seluruh Indonesia' : $s['city'] . ', ' . $s['province'],
            'hours' => $s['hours'],
            'tags' => array_values(array_filter(array_map('trim', explode(',', (string) $s['tags'])))),
            'verified' => $verified,
            'phone' => $verified ? $s['phone'] : null,
            'whatsapp' => $verified ? $s['whatsapp'] : null,
        ];
    }

    public static function validationErrors(array $in): array
    {
        $e = Validator::make($in, [
            'name' => 'required|max:160',
            'type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'province' => 'required|max:60',
            'city' => 'required|max:80',
            'hours' => 'max:80',
            'phone' => ['regex:/^[0-9+\-() ]{3,30}$/'],
            'whatsapp' => ['regex:/^[0-9+\- ]{9,30}$/'],
            'tags' => 'max:255',
            'address' => 'max:500',
            'verified_at' => 'date',
            'verified_note' => 'max:255',
        ], [
            'name.required' => 'Tulis nama layanan.',
            'phone' => 'Nomor telepon hanya boleh angka, spasi, +, -, dan tanda kurung.',
            'whatsapp' => 'Tulis nomor WhatsApp 9 sampai 30 digit.',
        ]);
        if (!empty($in['is_verified'])) {
            if (($in['verified_at'] ?? '') === '') {
                $e['verified_at'] = 'Isi tanggal verifikasi.';
            }
            if (trim((string) ($in['verified_note'] ?? '')) === '') {
                $e['verified_note'] = 'Catat cara verifikasinya.';
            }
            if (trim((string) ($in['phone'] ?? '')) === '' && trim((string) ($in['whatsapp'] ?? '')) === '') {
                $e['phone'] = 'Layanan terverifikasi perlu minimal satu kontak.';
            }
        }
        return $e;
    }

    public static function save(array $in, ?int $id): int
    {
        $verified = !empty($in['is_verified']);
        $vals = [
            trim($in['name']), $in['type'], trim($in['province']), trim($in['city']), trim($in['address'] ?? '') ?: null,
            trim($in['phone'] ?? '') ?: null, trim($in['whatsapp'] ?? '') ?: null, trim($in['hours'] ?? ''),
            (int) !empty($in['is_24h']), implode(', ', array_filter(array_map('trim', explode(',', (string) ($in['tags'] ?? ''))))),
            (int) $verified, $verified ? $in['verified_at'] : null, $verified ? trim($in['verified_note']) : null, now(),
        ];
        if ($id === null) {
            return Database::insert(
                'INSERT INTO services (name, type, province, city, address, phone, whatsapp, hours, is_24h, tags, is_verified, verified_at, verified_note, updated_at, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [...$vals, now()]
            );
        }
        Database::run(
            'UPDATE services SET name = ?, type = ?, province = ?, city = ?, address = ?, phone = ?, whatsapp = ?, hours = ?, is_24h = ?, tags = ?,
               is_verified = ?, verified_at = ?, verified_note = ?, updated_at = ? WHERE id = ?',
            [...$vals, $id]
        );
        return $id;
    }

    /** @return bool false when reports still refer to it */
    public static function delete(int $id): bool
    {
        if ((int) Database::value('SELECT COUNT(*) FROM reports WHERE service_id = ?', [$id]) > 0) {
            return false;
        }
        Database::run('DELETE FROM services WHERE id = ?', [$id]);
        return true;
    }
}
