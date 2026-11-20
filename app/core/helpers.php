<?php
declare(strict_types=1);

/* ---------- Output ---------- */

/** Escape for HTML text and attribute context. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** App URL respecting the base path (empty for `php -S`, e.g. /haven-v2/public under Apache). */
function url(string $path = '/', array $query = []): string
{
    $u = App::basePath() . '/' . ltrim($path, '/');
    if ($u === '') {
        $u = '/';
    }
    return $query ? $u . '?' . http_build_query($query) : $u;
}

/** Asset URL with a cache-busting version from the file's mtime. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/public/assets/' . ltrim($path, '/');
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return url('assets/' . ltrim($path, '/')) . $v;
}

function icon(string $name, string $class = 'icon'): string
{
    return '<svg class="' . e($class) . '" aria-hidden="true"><use href="#i-' . e($name) . '"/></svg>';
}

/** Marks a nav link as current. */
function current_attr(string $key, string $active): string
{
    return $key === $active ? ' aria-current="page"' : '';
}

/* ---------- Request ---------- */

function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function input_bool(string $key): bool
{
    return isset($_POST[$key]) && $_POST[$key] !== '' && $_POST[$key] !== '0';
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function user_agent(): string
{
    return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
}

/** "Chrome · Windows" from a user agent, for login activity lists. */
function ua_summary(?string $ua): string
{
    $ua = (string) $ua;
    $browser = match (true) {
        str_contains($ua, 'Edg/') => 'Edge',
        str_contains($ua, 'OPR/') => 'Opera',
        str_contains($ua, 'Firefox/') => 'Firefox',
        str_contains($ua, 'Chrome/') => 'Chrome',
        str_contains($ua, 'Safari/') => 'Safari',
        default => 'Browser lain',
    };
    $os = match (true) {
        str_contains($ua, 'Windows') => 'Windows',
        str_contains($ua, 'Android') => 'Android',
        str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
        str_contains($ua, 'Mac OS') => 'macOS',
        str_contains($ua, 'Linux') => 'Linux',
        default => 'perangkat lain',
    };
    return "$browser · $os";
}

/* ---------- Responses ---------- */

function redirect(string $path, int $status = 303): never
{
    $target = preg_match('#^https?://#', $path) ? $path : url($path);
    header('Location: ' . $target, true, $status);
    exit;
}

/** Redirect back to the form with errors and old input (password fields are never kept). */
function back_with(string $path, array $errors, array $old = []): never
{
    foreach (array_keys($old) as $k) {
        if (str_contains((string) $k, 'password') || $k === '_csrf') {
            unset($old[$k]);
        }
    }
    Session::flash('errors', $errors);
    Session::flash('old', $old);
    redirect($path);
}

function json_response(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Forms ---------- */

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

function old(string $key, string $default = ''): string
{
    $old = Session::getFlash('old') ?? [];
    return isset($old[$key]) && is_string($old[$key]) ? $old[$key] : $default;
}

function form_errors(): array
{
    return Session::getFlash('errors') ?? [];
}

/** Error paragraph for a field; id matches what app.js uses so it can clear it on input. */
function field_error(string $field): string
{
    $msg = form_errors()[$field] ?? null;
    if ($msg === null) {
        return '';
    }
    return '<p class="error" id="' . e($field) . '-err">'
        . '<svg class="icon" aria-hidden="true" style="width:16px;height:16px;margin-top:2px"><use href="#i-alert"/></svg>'
        . '<span>' . e($msg) . '</span></p>';
}

/** aria attributes for an invalid field. Pass the hint id to keep it described too. */
function field_aria(string $field, ?string $hintId = null): string
{
    $invalid = isset(form_errors()[$field]);
    $ids = array_filter([$hintId, $invalid ? "$field-err" : null]);
    $out = $ids ? ' aria-describedby="' . e(implode(' ', $ids)) . '"' : '';
    return $out . ($invalid ? ' aria-invalid="true"' : '');
}

/* ---------- Dates ---------- */

const ID_MONTHS = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const ID_MONTHS_LONG = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const ID_DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

/** "20 Nov 2026" or with time "20 Nov 2026 · 08.40". */
function id_date(?string $datetime, bool $withTime = false, bool $long = false): string
{
    if (!$datetime) {
        return '-';
    }
    $t = strtotime($datetime);
    $m = $long ? ID_MONTHS_LONG[(int) date('n', $t)] : ID_MONTHS[(int) date('n', $t)];
    $s = date('j', $t) . ' ' . $m . ' ' . date('Y', $t);
    return $withTime ? $s . ' · ' . date('H.i', $t) : $s;
}

function id_today_long(): string
{
    return ID_DAYS[(int) date('w')] . ', ' . id_date(date('Y-m-d'), false, true);
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/** Masks an email for display: ra***@demo.id */
function mask_email(string $email): string
{
    [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
    return mb_substr($local, 0, 2) . '***@' . $domain;
}

/* ---------- Case labels ---------- */

function status_badge(string $status, string $suffix = ''): string
{
    return '<span class="badge" data-status="' . e($status) . '">' . e((Report::STATUS[$status] ?? $status) . $suffix) . '</span>';
}

function urgency_badge(string $urgency, string $prefix = ''): string
{
    return '<span class="badge" data-urgency="' . e($urgency) . '">' . e($prefix . (Report::URGENCY[$urgency] ?? $urgency)) . '</span>';
}

/** "Dewi Anggraini" → "Dewi A." (what survivors see of their companion). */
function short_name(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    return count($parts) > 1 ? $parts[0] . ' ' . mb_strtoupper(mb_substr($parts[1], 0, 1)) . '.' : $parts[0];
}

function initials(string $name): string
{
    return mb_strtoupper(implode('', array_map(static fn ($w) => mb_substr($w, 0, 1), array_slice(preg_split('/\s+/', trim($name)), 0, 2))));
}

/* ---------- Pagination ---------- */

/** @return array{page:int, per:int, offset:int, total:int, pages:int} */
function paginate(int $total, int $per = 20): array
{
    $pages = max(1, (int) ceil($total / $per));
    $page = min($pages, max(1, (int) ($_GET['halaman'] ?? 1)));
    return ['page' => $page, 'per' => $per, 'offset' => ($page - 1) * $per, 'total' => $total, 'pages' => $pages];
}

/** Current GET query with some keys replaced (for filter and page links). */
function query_with(array $changes): string
{
    $q = array_merge($_GET, $changes);
    $q = array_filter($q, static fn ($v) => $v !== null && $v !== '');
    return $q ? '?' . http_build_query($q) : '?';
}

function selected(string $a, string $b): string
{
    return $a === $b ? ' selected' : '';
}

function checked(bool $on): string
{
    return $on ? ' checked' : '';
}
