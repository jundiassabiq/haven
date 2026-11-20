<?php
declare(strict_types=1);

/**
 * The case status machine. The server refuses every transition not listed in NEXT.
 *   diterima → ditinjau → didampingi → (dirujuk →) selesai
 *              ditinjau → ditutup (reason required)
 */
final class CaseWorkflow
{
    public const NEXT = [
        'diterima' => ['ditinjau'],
        'ditinjau' => ['didampingi', 'ditutup'],
        'didampingi' => ['dirujuk', 'selesai'],
        'dirujuk' => ['selesai'],
        'selesai' => [],
        'ditutup' => [],
    ];

    /** Statuses in which a companion actively works the case. */
    public const ACTIVE = ['didampingi', 'dirujuk'];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::NEXT[$from] ?? [], true);
    }

    public static function allowedNext(string $from): array
    {
        return self::NEXT[$from] ?? [];
    }

    public static function isClosed(string $status): bool
    {
        return self::NEXT[$status] === [];
    }

    /**
     * Pure check of a requested transition and its required data.
     * @param array{companion_id?:int|null, service_id?:int|null, reason?:string|null} $ctx
     * @return string|null error message
     */
    public static function check(string $from, string $to, array $ctx = []): ?string
    {
        if (!isset(self::NEXT[$from]) || !array_key_exists($to, self::NEXT)) {
            return 'Status tidak dikenal.';
        }
        if (!self::canTransition($from, $to)) {
            return 'Status ' . Report::STATUS[$from] . ' tidak bisa diubah menjadi ' . Report::STATUS[$to] . '.';
        }
        return match ($to) {
            'didampingi' => empty($ctx['companion_id']) ? 'Pilih pendamping yang akan menangani.' : null,
            'dirujuk' => empty($ctx['service_id']) ? 'Pilih layanan tujuan rujukan.' : null,
            'ditutup' => mb_strlen(trim((string) ($ctx['reason'] ?? ''))) < 10 ? 'Tulis alasan penutupan, minimal 10 karakter.' : null,
            default => null,
        };
    }

    /**
     * Applies one transition: updates the report, writes the status log, audits, and notifies.
     * @param array{companion_id?:int|null, service_id?:int|null, reason?:string|null, note?:string|null} $ctx
     * @throws DomainException with a user-facing message
     */
    public static function transition(array $report, string $to, array $ctx = []): array
    {
        $from = $report['status'];
        $err = self::check($from, $to, $ctx);
        if ($err !== null) {
            throw new DomainException($err);
        }

        $pdo = Database::pdo();
        $own = !$pdo->inTransaction();
        if ($own) {
            $pdo->beginTransaction();
        }
        try {
            $set = ['status = ?', 'updated_at = ?'];
            $params = [$to, now()];
            if ($to === 'didampingi') {
                $set[] = 'assigned_to = ?';
                $params[] = (int) $ctx['companion_id'];
            }
            if ($to === 'dirujuk') {
                $set[] = 'service_id = ?';
                $params[] = (int) $ctx['service_id'];
            }
            if ($to === 'ditutup') {
                $set[] = 'closed_reason = ?';
                $params[] = trim((string) $ctx['reason']);
            }
            $params[] = (int) $report['id'];
            $params[] = $from;
            // "AND status = ?" makes two people changing the same case at once fail safely.
            $st = Database::run('UPDATE reports SET ' . implode(', ', $set) . ' WHERE id = ? AND status = ?', $params);
            if ($st->rowCount() !== 1) {
                throw new DomainException('Status kasus baru saja diubah orang lain. Muat ulang halaman.');
            }

            $note = $to === 'ditutup' ? trim((string) $ctx['reason']) : trim((string) ($ctx['note'] ?? ''));
            Database::run(
                'INSERT INTO report_status_logs (report_id, from_status, to_status, actor_id, note_enc, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                [(int) $report['id'], $from, $to, Auth::id(), Crypto::enc($note === '' ? null : $note), now()]
            );

            $updated = Report::find((int) $report['id']);
            self::sideEffects($updated, $from, $to);
            if ($own) {
                $pdo->commit();
            }
            return $updated;
        } catch (Throwable $e) {
            if ($own && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private static function sideEffects(array $r, string $from, string $to): void
    {
        $detail = Report::STATUS[$from] . ' → ' . Report::STATUS[$to];
        if ($to === 'didampingi') {
            $c = User::find((int) $r['assigned_to']);
            $detail .= ' (pendamping: ' . $c['name'] . ')';
            Message::system((int) $r['id'], short_name($c['name']) . ' bergabung sebagai pendampingmu.');
            Notifier::user((int) $c['id'], (int) $r['id'], 'tugas', 'Kasus baru ditugaskan: ' . $r['code'], '/pendamping/kasus/' . $r['code']);
        }
        if ($to === 'dirujuk') {
            $s = Service::find((int) $r['service_id']);
            $detail .= ' (' . ($s['name'] ?? '-') . ')';
            Notifier::survivor($r, 'rujukan', 'Rujukan baru tersedia untukmu', '#rujukan');
        }
        Audit::log('report.status', 'report', $r['code'], $detail);
        Notifier::survivor($r, 'status', 'Status laporan: ' . Report::STATUS[$to], '#progres');
    }
}
