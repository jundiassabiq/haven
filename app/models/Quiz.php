<?php
declare(strict_types=1);

/** Module 8: quizzes. The answer key never leaves the server; scoring happens in score(). */
final class Quiz
{
    public static function main(): ?array
    {
        return Database::one("SELECT * FROM quizzes WHERE status = 'terbit' ORDER BY id LIMIT 1");
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one("SELECT * FROM quizzes WHERE slug = ? AND status = 'terbit'", [$slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM quizzes WHERE id = ?', [$id]);
    }

    /**
     * Questions with options. $withKey adds is_correct (server-side only).
     * @return array<int, array{id:int, question:string, explanation:string, related_article_id:?int, options:array}>
     */
    public static function questions(int $quizId, bool $withKey = false): array
    {
        $qs = Database::all('SELECT id, position, question, explanation, related_article_id FROM quiz_questions WHERE quiz_id = ? ORDER BY position, id', [$quizId]);
        if (!$qs) {
            return [];
        }
        $ids = array_column($qs, 'id');
        $opts = Database::all(
            'SELECT id, question_id, position, label' . ($withKey ? ', is_correct' : '') . ' FROM quiz_options WHERE question_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ') ORDER BY position, id',
            $ids
        );
        $byQ = [];
        foreach ($opts as $o) {
            $byQ[(int) $o['question_id']][] = $o;
        }
        foreach ($qs as &$q) {
            $q['options'] = $byQ[(int) $q['id']] ?? [];
        }
        return $qs;
    }

    /**
     * Pure scoring.
     * @param array $questions from questions(…, true)
     * @param array<int|string, int|string> $answers question_id => option_id
     * @return array{score:int, total:int, items:array<int, array{question_id:int, chosen:?int, correct:?int, right:bool}>}
     */
    public static function score(array $questions, array $answers): array
    {
        $score = 0;
        $items = [];
        foreach ($questions as $q) {
            $qid = (int) $q['id'];
            $valid = array_map(static fn ($o) => (int) $o['id'], $q['options']);
            $correct = null;
            foreach ($q['options'] as $o) {
                if ((int) $o['is_correct']) {
                    $correct = (int) $o['id'];
                }
            }
            $raw = $answers[$qid] ?? $answers[(string) $qid] ?? null;
            $chosen = is_numeric($raw) && in_array((int) $raw, $valid, true) ? (int) $raw : null;
            $right = $chosen !== null && $chosen === $correct;
            $score += (int) $right;
            $items[] = ['question_id' => $qid, 'chosen' => $chosen, 'correct' => $correct, 'right' => $right];
        }
        return ['score' => $score, 'total' => count($questions), 'items' => $items];
    }

    public static function message(int $score, int $total): string
    {
        $ratio = $total > 0 ? $score / $total : 0;
        return match (true) {
            $ratio >= 0.9 => 'Hampir semua benar. Kalau ada teman yang bercerita, artikel tentang cara mendampingi teman bisa jadi bacaan berikutnya.',
            $ratio >= 0.6 => 'Sebagian besar benar. Lihat pembahasan untuk soal bertanda silang.',
            default => 'Banyak soal di kuis ini memang menyangkut hal yang jarang dibahas. Setiap soal di bawah punya pembahasan dan tautan ke artikelnya.',
        };
    }

    public static function saveAttempt(int $quizId, ?int $userId, array $result, array $answers): int
    {
        $clean = [];
        foreach ($result['items'] as $it) {
            $clean[$it['question_id']] = $it['chosen'];
        }
        return Database::insert(
            'INSERT INTO quiz_attempts (quiz_id, user_id, score, total, answers, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$quizId, $userId, $result['score'], $result['total'], json_encode($clean), now()]
        );
    }

    public static function attempt(int $id): ?array
    {
        return Database::one('SELECT * FROM quiz_attempts WHERE id = ?', [$id]);
    }

    public static function attemptsFor(int $userId, int $limit = 5): array
    {
        return Database::all('SELECT a.*, q.title FROM quiz_attempts a JOIN quizzes q ON q.id = a.quiz_id WHERE a.user_id = ? ORDER BY a.id DESC LIMIT ' . max(1, $limit), [$userId]);
    }

    /** Per-question share of correct answers across all attempts. @return array<int, ?int> question_id => percent */
    public static function correctRates(int $quizId): array
    {
        $qs = self::questions($quizId, true);
        $counts = array_fill_keys(array_map(static fn ($q) => (int) $q['id'], $qs), [0, 0]);
        foreach (Database::all('SELECT answers FROM quiz_attempts WHERE quiz_id = ?', [$quizId]) as $row) {
            $ans = json_decode($row['answers'], true) ?: [];
            foreach (self::score($qs, $ans)['items'] as $it) {
                $counts[$it['question_id']][0] += (int) $it['right'];
                $counts[$it['question_id']][1]++;
            }
        }
        return array_map(static fn ($c) => $c[1] ? (int) round($c[0] * 100 / $c[1]) : null, $counts);
    }

    public static function stats(int $quizId): array
    {
        return Database::one('SELECT COUNT(*) AS n, AVG(score) AS avg_score FROM quiz_attempts WHERE quiz_id = ?', [$quizId]) ?? ['n' => 0, 'avg_score' => null];
    }

    /** @return array<string,string> errors */
    public static function questionErrors(array $in): array
    {
        $e = Validator::make($in, ['question' => 'required|max:500', 'explanation' => 'required|max:1000', 'position' => 'required|between:1,100'], [
            'question.required' => 'Tulis pertanyaannya.',
            'explanation.required' => 'Tulis pembahasan agar peserta tetap belajar.',
        ]);
        $filled = array_filter($in['options'], static fn ($o) => trim((string) $o) !== '');
        if (count($filled) < 2) {
            $e['options'] = 'Isi minimal dua pilihan jawaban.';
        } elseif (!isset($filled[(int) $in['correct']]) || $in['correct'] === '') {
            $e['options'] = 'Tandai satu jawaban benar di antara pilihan yang terisi.';
        }
        foreach ($filled as $o) {
            if (mb_strlen($o) > 255) {
                $e['options'] = 'Setiap pilihan maksimal 255 karakter.';
            }
        }
        return $e;
    }

    public static function saveQuestion(int $quizId, ?int $id, array $in): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $related = $in['related'] !== '' ? (int) $in['related'] : null;
            if ($id === null) {
                $id = Database::insert('INSERT INTO quiz_questions (quiz_id, position, question, explanation, related_article_id) VALUES (?, ?, ?, ?, ?)', [$quizId, (int) $in['position'], trim($in['question']), trim($in['explanation']), $related]);
            } else {
                Database::run('UPDATE quiz_questions SET position = ?, question = ?, explanation = ?, related_article_id = ? WHERE id = ?', [(int) $in['position'], trim($in['question']), trim($in['explanation']), $related, $id]);
                Database::run('DELETE FROM quiz_options WHERE question_id = ?', [$id]);
            }
            $pos = 1;
            foreach ($in['options'] as $i => $label) {
                if (trim((string) $label) === '') {
                    continue;
                }
                Database::run('INSERT INTO quiz_options (question_id, position, label, is_correct) VALUES (?, ?, ?, ?)', [$id, $pos++, trim($label), (int) ((int) $in['correct'] === (int) $i)]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
        return $id;
    }
}
