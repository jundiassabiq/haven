<?php
declare(strict_types=1);

/**
 * Module 10: aggregate statistics. No report content is ever read here.
 * Privacy rule: any category with fewer than MIN_GROUP reports is merged into "Lainnya";
 * if the merged group is itself below MIN_GROUP it is not shown at all.
 */
final class Stats
{
    public const MIN_GROUP = 5;
    public const RANGES = ['30h' => '30 hari terakhir', '12b' => '12 bulan terakhir', 'tahun' => 'Tahun ini'];

    /**
     * @param array<string,int> $counts label => n
     * @return array{labels:string[], values:int[], hidden:int}
     */
    public static function mergeSmall(array $counts, int $min = self::MIN_GROUP, string $other = 'Lainnya'): array
    {
        arsort($counts);
        $labels = [];
        $values = [];
        $small = 0;
        foreach ($counts as $label => $n) {
            if ($n >= $min) {
                $labels[] = (string) $label;
                $values[] = (int) $n;
            } else {
                $small += (int) $n;
            }
        }
        $hidden = 0;
        if ($small >= $min) {
            $labels[] = "$other (< $min digabung)";
            $values[] = $small;
        } else {
            $hidden = $small;
        }
        return ['labels' => $labels, 'values' => $values, 'hidden' => $hidden];
    }

    public static function since(string $range): string
    {
        return match ($range) {
            '30h' => date('Y-m-d H:i:s', time() - 30 * 86400),
            'tahun' => date('Y') . '-01-01 00:00:00',
            default => date('Y-m-01 00:00:00', strtotime('first day of -11 months')),
        };
    }

    /** ['2026-01' => 'Jan', …] for the last $n months, oldest first. */
    public static function monthKeys(int $n): array
    {
        $out = [];
        for ($i = $n - 1; $i >= 0; $i--) {
            $t = strtotime("first day of -$i months");
            $out[date('Y-m', $t)] = ID_MONTHS[(int) date('n', $t)];
        }
        return $out;
    }

    private static function perMonth(string $sql, int $months): array
    {
        $keys = self::monthKeys($months);
        $rows = Database::all($sql, [array_key_first($keys) . '-01 00:00:00']);
        $map = array_column($rows, 'n', 'ym');
        return array_map(static fn ($k) => (int) ($map[$k] ?? 0), array_keys($keys));
    }

    public static function charts(string $range): array
    {
        $since = self::since($range);
        $m12 = self::monthKeys(12);
        $trend = self::perMonth("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS n FROM reports WHERE created_at >= ? GROUP BY ym", 12);

        $m6 = self::monthKeys(6);
        $new = self::perMonth("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS n FROM reports WHERE created_at >= ? GROUP BY ym", 6);
        $done = self::perMonth("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS n FROM report_status_logs WHERE to_status = 'selesai' AND created_at >= ? GROUP BY ym", 6);

        $cats = [];
        foreach (Database::all('SELECT category, COUNT(*) AS n FROM reports WHERE created_at >= ? GROUP BY category', [$since]) as $r) {
            $cats[Report::CATEGORIES[$r['category']] ?? $r['category']] = (int) $r['n'];
        }
        $merged = self::mergeSmall($cats);
        $catText = implode(', ', array_map(static fn ($l, $v) => "$l $v", $merged['labels'], $merged['values']));

        return [
            'trend' => [
                'type' => 'line', 'labels' => array_values($m12), 'series' => [['name' => 'Laporan', 'values' => $trend]],
                'label' => 'Grafik garis laporan per bulan selama 12 bulan terakhir: ' . implode(', ', array_map(static fn ($l, $v) => "$l $v", $m12, $trend)) . '.',
            ],
            'categories' => [
                'type' => 'hbar', 'labels' => $merged['labels'], 'series' => [['name' => 'Kasus', 'values' => $merged['values']]], 'hidden' => $merged['hidden'],
                'label' => 'Jenis kejadian, ' . self::RANGES[$range] . ': ' . ($catText ?: 'belum ada data') . '.',
            ],
            'status' => [
                'type' => 'bar', 'labels' => array_values($m6), 'series' => [['name' => 'Baru', 'values' => $new], ['name' => 'Selesai', 'values' => $done]],
                'label' => 'Grafik batang kasus baru dan selesai per bulan, 6 bulan terakhir. Baru: ' . implode(', ', $new) . '. Selesai: ' . implode(', ', $done) . '.',
            ],
        ];
    }

    public static function kpis(string $range): array
    {
        $since = self::since($range);
        $avgMin = Database::value(
            "SELECT AVG(TIMESTAMPDIFF(MINUTE, l.created_at,
                (SELECT MIN(m.created_at) FROM messages m WHERE m.report_id = l.report_id AND m.sender_role = 'companion' AND m.created_at >= l.created_at)))
             FROM report_status_logs l WHERE l.to_status = 'didampingi' AND l.created_at >= ?",
            [$since]
        );
        $oldestWaiting = Database::value("SELECT MIN(created_at) FROM reports WHERE status = 'diterima'");
        return [
            'total' => (int) Database::value('SELECT COUNT(*) FROM reports WHERE created_at >= ?', [$since]),
            'thisMonth' => (int) Database::value('SELECT COUNT(*) FROM reports WHERE created_at >= ?', [date('Y-m-01 00:00:00')]),
            'waiting' => (int) Database::value("SELECT COUNT(*) FROM reports WHERE status = 'diterima'"),
            'oldestWaiting' => $oldestWaiting,
            'active' => (int) Database::value("SELECT COUNT(*) FROM reports WHERE status IN ('didampingi','dirujuk')"),
            'companions' => (int) Database::value("SELECT COUNT(DISTINCT assigned_to) FROM reports WHERE status IN ('didampingi','dirujuk')"),
            'avgHours' => $avgMin === null ? null : round((float) $avgMin / 60, 1),
        ];
    }

    public static function workload(): array
    {
        return Database::all(
            "SELECT u.id, u.name, u.region,
               (SELECT COUNT(*) FROM reports r WHERE r.assigned_to = u.id AND r.status IN ('didampingi','dirujuk')) AS active,
               (SELECT COUNT(*) FROM report_status_logs l JOIN reports r ON r.id = l.report_id
                  WHERE r.assigned_to = u.id AND l.to_status = 'didampingi' AND l.created_at >= ?) AS this_month
             FROM users u WHERE u.role = 'companion' AND u.is_active = 1 ORDER BY active DESC, u.name",
            [date('Y-m-01 00:00:00')]
        );
    }

    /** Numbers on the public home page. */
    public static function publicNumbers(): array
    {
        return [
            'reports' => (int) Database::value('SELECT COUNT(*) FROM reports'),
            'companions' => (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'companion' AND is_active = 1"),
            'sessions' => (int) Database::value(
                "SELECT COUNT(*) FROM bookings b JOIN companion_slots s ON s.id = b.slot_id WHERE b.status = 'aktif' AND DATE_ADD(s.starts_at, INTERVAL s.duration_min MINUTE) <= ?",
                [now()]
            ),
        ];
    }

    public static function companion(int $id): array
    {
        return [
            'active' => Report::activeCountFor($id),
            'unanswered' => (int) Database::value(
                "SELECT COUNT(*) FROM reports r WHERE r.assigned_to = ? AND r.status IN ('didampingi','dirujuk')
                   AND NOT EXISTS (SELECT 1 FROM messages m WHERE m.report_id = r.id AND m.sender_role = 'companion')",
                [$id]
            ),
            'today' => (int) Database::value(
                "SELECT COUNT(*) FROM bookings b JOIN companion_slots s ON s.id = b.slot_id WHERE s.companion_id = ? AND b.status = 'aktif' AND DATE(s.starts_at) = ?",
                [$id, date('Y-m-d')]
            ),
            'unread' => (int) Database::value(
                "SELECT COUNT(*) FROM messages m JOIN reports r ON r.id = m.report_id WHERE r.assigned_to = ? AND m.sender_role = 'survivor' AND m.read_at IS NULL",
                [$id]
            ),
        ];
    }

    public static function survivor(int $id): array
    {
        return [
            'active' => (int) Database::value("SELECT COUNT(*) FROM reports WHERE user_id = ? AND status NOT IN ('selesai','ditutup')", [$id]),
            'upcoming' => (int) Database::value(
                "SELECT COUNT(*) FROM bookings b JOIN companion_slots s ON s.id = b.slot_id JOIN reports r ON r.id = b.report_id WHERE r.user_id = ? AND b.status = 'aktif' AND s.starts_at > ?",
                [$id, now()]
            ),
            'unread' => (int) Database::value(
                "SELECT COUNT(*) FROM messages m JOIN reports r ON r.id = m.report_id WHERE r.user_id = ? AND m.sender_role IN ('companion','system') AND m.read_at IS NULL",
                [$id]
            ),
        ];
    }
}
