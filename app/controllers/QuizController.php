<?php
declare(strict_types=1);

/** Module 8 (public): take the quiz; answers are scored on the server. */
final class QuizController
{
    private static function quizOr404(?string $slug = null): array
    {
        $q = $slug ? Quiz::findBySlug($slug) : Quiz::main();
        if (!$q) {
            throw new HttpException(404);
        }
        return $q;
    }

    public function intro(): void
    {
        $q = self::quizOr404();
        $u = Auth::user();
        View::show('pages/quiz/intro', [
            'title' => 'Kuis | Haven',
            'active' => 'kuis',
            'quiz' => $q,
            'count' => count(Quiz::questions((int) $q['id'])),
            'history' => $u ? Quiz::attemptsFor((int) $u['id'], 3) : [],
        ]);
    }

    public function take(array $p): void
    {
        $q = self::quizOr404($p['slug']);
        View::show('pages/quiz/take', [
            'title' => 'Mengerjakan kuis | Haven',
            'active' => 'kuis',
            'quiz' => $q,
            'questions' => Quiz::questions((int) $q['id']), // no answer key
        ]);
    }

    public function submit(array $p): void
    {
        $q = self::quizOr404($p['slug']);
        $questions = Quiz::questions((int) $q['id'], true);
        $answers = is_array($_POST['jawaban'] ?? null) ? $_POST['jawaban'] : [];
        $result = Quiz::score($questions, $answers);

        $missing = array_filter($result['items'], static fn ($it) => $it['chosen'] === null);
        if ($missing) {
            $old = [];
            foreach ($result['items'] as $it) {
                if ($it['chosen'] !== null) {
                    $old['q' . $it['question_id']] = (string) $it['chosen'];
                }
            }
            $n = count($missing);
            Session::flash('error', "Masih ada $n soal yang belum dijawab.");
            back_with('/kuis/' . $q['slug'] . '/kerjakan', ['q' . reset($missing)['question_id'] => 'Pilih salah satu jawaban.'], $old);
        }

        $id = Quiz::saveAttempt((int) $q['id'], Auth::id(), $result, $answers);
        $mine = Session::get('quiz_attempts', []);
        $mine[] = $id;
        Session::put('quiz_attempts', array_slice($mine, -20));
        redirect('/kuis/hasil/' . $id);
    }

    public function result(array $p): void
    {
        $att = Quiz::attempt((int) $p['id']);
        $own = $att && ((Auth::id() !== null && (int) $att['user_id'] === Auth::id()) || in_array((int) $att['id'], Session::get('quiz_attempts', []), true));
        if (!$own) {
            throw new HttpException(404);
        }
        $quiz = Quiz::find((int) $att['quiz_id']);
        $questions = Quiz::questions((int) $quiz['id'], true);
        $result = Quiz::score($questions, json_decode($att['answers'], true) ?: []);
        $articles = [];
        foreach (Database::all("SELECT id, title, slug FROM articles WHERE status = 'terbit'") as $a) {
            $articles[(int) $a['id']] = $a;
        }
        View::show('pages/quiz/result', [
            'title' => 'Hasil kuis | Haven',
            'active' => 'kuis',
            'quiz' => $quiz,
            'attempt' => $att,
            'questions' => $questions,
            'result' => $result,
            'articles' => $articles,
        ]);
    }
}
