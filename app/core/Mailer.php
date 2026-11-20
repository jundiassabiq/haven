<?php
declare(strict_types=1);

/**
 * Local-only mail: messages are appended to storage/mail.log instead of being sent.
 * Swap send() for PHPMailer/SMTP when deploying.
 */
final class Mailer
{
    public static function send(string $to, string $subject, string $body): void
    {
        $entry = str_repeat('=', 60) . "\n"
            . 'Tanggal : ' . date('Y-m-d H:i:s') . "\n"
            . "Kepada  : $to\n"
            . "Subjek  : $subject\n\n"
            . $body . "\n\n";
        file_put_contents(STORAGE_PATH . '/mail.log', $entry, FILE_APPEND | LOCK_EX);
    }

    /** Absolute URL for links inside emails. */
    public static function absoluteUrl(string $path, array $query = []): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        return $scheme . '://' . $host . url($path, $query);
    }
}
