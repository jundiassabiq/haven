<?php
declare(strict_types=1);

/** Module 8 (admin): questions, options, answer key, and explanations. */
final class AdminQuizController
{
    private static function quiz(): array
    {
        $q = Database::one('SELECT * FROM quizzes ORDER BY id LIMIT 1');
        if (!$q) {
            throw new HttpException(404, 'Belum ada kuis. Jalankan bin/db-reset.php untuk memuat kuis bawaan.');
        }
        return $q;
    }

    public function index(): void
    {
        $quiz = self::quiz();
        View::show('pages/admin/quiz', [
            'title' => 'Kuis | Admin Haven',
            'active' => 'kuis',
            'quiz' => $quiz,
            'questions' => Quiz::questions((int) $quiz['id']),
            'rates' => Quiz::correctRates((int) $quiz['id']),
            'stats' => Quiz::stats((int) $quiz['id']),
        ], 'app');
    }

    private function form(?array $question): void
    {
        $quiz = self::quiz();
        View::show('pages/admin/quiz-form', [
            'title' => ($question ? 'Ubah soal' : 'Tambah soal') . ' | Admin Haven',
            'active' => 'kuis',
            'quiz' => $quiz,
            'question' => $question,
            'nextPosition' => (int) Database::value('SELECT COALESCE(MAX(position), 0) + 1 FROM quiz_questions WHERE quiz_id = ?', [$quiz['id']]),
            'articles' => Database::all("SELECT id, title FROM articles WHERE status = 'terbit' ORDER BY title"),
        ], 'app');
    }

    public function create(): void
    {
        $this->form(null);
    }

    private static function questionOr404(int $id): array
    {
        $quiz = self::quiz();
        foreach (Quiz::questions((int) $quiz['id'], true) as $q) {
            if ((int) $q['id'] === $id) {
                return $q;
            }
        }
        throw new HttpException(404);
    }

    public function edit(array $p): void
    {
        $this->form(self::questionOr404((int) $p['id']));
    }

    private static function input(): array
    {
        $raw = is_array($_POST['options'] ?? null) ? $_POST['options'] : [];
        // Read by index: the "correct" radio refers to these positions, so field order must not matter.
        $opts = [];
        for ($i = 0; $i < max(4, min(6, count($raw))); $i++) {
            $opts[$i] = trim((string) ($raw[$i] ?? $raw[(string) $i] ?? ''));
        }
        return [
            'question' => input('question'),
            'explanation' => input('explanation'),
            'position' => input('position'),
            'related' => input('related'),
            'correct' => input('correct'),
            'options' => $opts,
        ];
    }

    private static function old(array $in): array
    {
        $old = ['question' => $in['question'], 'explanation' => $in['explanation'], 'position' => $in['position'], 'related' => $in['related'], 'correct' => $in['correct']];
        foreach ($in['options'] as $i => $o) {
            $old["option_$i"] = $o;
        }
        return $old;
    }

    public function store(): void
    {
        $quiz = self::quiz();
        $in = self::input();
        $errors = Quiz::questionErrors($in);
        if ($errors) {
            back_with('/admin/kuis/soal/baru', $errors, self::old($in));
        }
        $id = Quiz::saveQuestion((int) $quiz['id'], null, $in);
        Audit::log('quiz.save', 'quiz', (string) $id, 'Menambah soal');
        Session::flash('success', 'Soal ditambahkan.');
        redirect('/admin/kuis');
    }

    public function update(array $p): void
    {
        $q = self::questionOr404((int) $p['id']);
        $in = self::input();
        $errors = Quiz::questionErrors($in);
        if ($errors) {
            back_with('/admin/kuis/soal/' . $q['id'], $errors, self::old($in));
        }
        Quiz::saveQuestion((int) self::quiz()['id'], (int) $q['id'], $in);
        Audit::log('quiz.save', 'quiz', (string) $q['id'], 'Mengubah soal ' . $q['position']);
        Session::flash('success', 'Soal disimpan.');
        redirect('/admin/kuis');
    }

    public function delete(array $p): void
    {
        $q = self::questionOr404((int) $p['id']);
        Database::run('DELETE FROM quiz_questions WHERE id = ?', [$q['id']]);
        Audit::log('quiz.delete', 'quiz', (string) $q['id'], 'Menghapus soal ' . $q['position']);
        Session::flash('success', 'Soal dihapus. Riwayat skor lama tetap tersimpan.');
        redirect('/admin/kuis');
    }
}
