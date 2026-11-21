<?php
declare(strict_types=1);

/** Role home pages. Lists real data now; charts and KPIs arrive in Task 13. */
final class DashboardController
{
    private function lastLogin(int $userId): ?array
    {
        // The newest login is the current one; show the one before it.
        return User::recentLogins($userId, 2)[1] ?? null;
    }

    public function survivor(): void
    {
        $u = Auth::user();
        [$upcoming] = Booking::split(Booking::forUser((int) $u['id']));
        View::show('pages/dashboard/survivor', [
            'title' => 'Beranda saya | Haven',
            'active' => 'dashboard',
            'user' => $u,
            'reports' => array_slice(Report::forUser((int) $u['id']), 0, 3),
            'upcoming' => array_slice($upcoming, 0, 2),
            'k' => Stats::survivor((int) $u['id']),
            'lastLogin' => $this->lastLogin((int) $u['id']),
        ], 'app');
    }

    public function companion(): void
    {
        $u = Auth::user();
        $cases = array_values(array_filter(Report::assignedTo((int) $u['id'], []), static fn ($r) => in_array($r['status'], CaseWorkflow::ACTIVE, true)));
        $today = array_values(array_filter(Slot::upcomingFor((int) $u['id']), static fn ($s) => $s['booking_id'] && substr($s['starts_at'], 0, 10) === date('Y-m-d')));
        View::show('pages/dashboard/companion', [
            'title' => 'Ringkasan pendamping | Haven',
            'active' => 'dashboard',
            'user' => $u,
            'cases' => $cases,
            'today' => $today,
            'k' => Stats::companion((int) $u['id']),
            'lastLogin' => $this->lastLogin((int) $u['id']),
        ], 'app');
    }

    public function admin(): void
    {
        $u = Auth::user();
        $range = isset(Stats::RANGES[input('rentang')]) ? input('rentang') : '12b';
        View::show('pages/dashboard/admin', [
            'title' => 'Statistik | Admin Haven',
            'active' => 'dashboard',
            'user' => $u,
            'range' => $range,
            'k' => Stats::kpis($range),
            'workload' => Stats::workload(),
            'hidden' => Stats::charts($range)['categories']['hidden'],
            'lastLogin' => $this->lastLogin((int) $u['id']),
        ], 'app');
    }

    /** Chart data for the admin dashboard (drawn on <canvas> by app.js). */
    public function adminApi(): void
    {
        $range = isset(Stats::RANGES[input('rentang')]) ? input('rentang') : '12b';
        json_response(Stats::charts($range));
    }
}
