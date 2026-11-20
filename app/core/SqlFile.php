<?php
declare(strict_types=1);

/** Runs our own .sql files statement by statement (used by bin/db-reset.php and the MySQL tests). */
final class SqlFile
{
    /** Splits on ";" outside quotes and "--" comments. */
    public static function split(string $sql): array
    {
        $out = [];
        $buf = '';
        $len = strlen($sql);
        $quote = null;
        for ($i = 0; $i < $len; $i++) {
            $c = $sql[$i];
            if ($quote !== null) {
                $buf .= $c;
                if ($c === '\\') {
                    $buf .= $sql[++$i] ?? '';
                } elseif ($c === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($c === '-' && ($sql[$i + 1] ?? '') === '-') {
                while ($i < $len && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') {
                $quote = $c;
            }
            if ($c === ';') {
                if (trim($buf) !== '') {
                    $out[] = trim($buf);
                }
                $buf = '';
                continue;
            }
            $buf .= $c;
        }
        if (trim($buf) !== '') {
            $out[] = trim($buf);
        }
        return $out;
    }

    /** Executes a file, replacing the database name "haven_v2" with $dbName. Returns statements run. */
    public static function run(PDO $pdo, string $file, string $dbName): int
    {
        $sql = (string) file_get_contents($file);
        $sql = preg_replace('/\b(DATABASE IF NOT EXISTS|USE) haven_v2\b/', '$1 ' . $dbName, $sql);
        $n = 0;
        foreach (self::split($sql) as $stmt) {
            $pdo->exec($stmt);
            $n++;
        }
        return $n;
    }
}
