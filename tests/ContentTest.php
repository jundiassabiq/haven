<?php
declare(strict_types=1);

App::setBasePath('');

test('markup: headings, paragraphs, lists, callout', function () {
    $html = Markup::render("Paragraf satu\nbaris dua.\n\n## Judul\n\n- a\n- b\n\n1. x\n2. y\n\n> Ingat\n> Ini bukan salahmu.");
    assert_true(str_contains($html, '<p>Paragraf satu baris dua.</p>'));
    assert_true(str_contains($html, '<h2>Judul</h2>'));
    assert_true(str_contains($html, '<ul><li>a</li><li>b</li></ul>'));
    assert_true(str_contains($html, '<ol><li>x</li><li>y</li></ol>'));
    assert_true(str_contains($html, '<div class="callout"><strong>Ingat</strong>Ini bukan salahmu.</div>'));
});

test('markup: HTML in the source is escaped, never rendered', function () {
    $html = Markup::render('<script>alert(1)</script> <img src=x onerror=alert(1)>');
    assert_false(str_contains($html, '<script'));
    assert_false(str_contains($html, '<img'));
    assert_true(str_contains($html, '&lt;script&gt;'));
});

test('markup: only http(s) and local links survive', function () {
    $html = Markup::render('[baik](https://komnasperempuan.go.id/) [lokal](/lapor) [jahat](javascript:alert(1)) [data](data:text/html,x)');
    assert_true(str_contains($html, 'href="https://komnasperempuan.go.id/" rel="noopener noreferrer"'));
    assert_true(str_contains($html, 'href="/lapor"'));
    assert_false(str_contains($html, 'javascript:'));
    assert_false(str_contains($html, 'data:text'));
});

test('markup: bold, and attribute injection through links is impossible', function () {
    assert_true(str_contains(Markup::render('**tebal**'), '<strong>tebal</strong>'));
    $html = Markup::render('[x](https://a.id/"onmouseover="alert(1))');
    assert_false(str_contains($html, '"onmouseover="'));
});

test('sources: "Title | URL" lines', function () {
    $s = Markup::sources("UU TPKS | https://peraturan.bpk.go.id/\nTanpa tautan\n\n");
    assert_same(2, count($s));
    assert_same('https://peraturan.bpk.go.id/', $s[0]['url']);
    assert_same(null, $s[1]['url']);
});

test('reading time is at least one minute and grows with length', function () {
    assert_same(1, Markup::readingMinutes('pendek'));
    assert_same(5, Markup::readingMinutes(str_repeat('kata ', 1000)));
});

test('slugify', function () {
    assert_same('hak-hakmu-sebagai-korban', Article::slugify('  Hak-hakmu sebagai Korban! '));
    assert_same('uu-tpks-2022', Article::slugify('UU TPKS (2022)'));
});

function quiz_fixture(): array
{
    return [
        ['id' => 1, 'options' => [['id' => 10, 'is_correct' => 0], ['id' => 11, 'is_correct' => 1]]],
        ['id' => 2, 'options' => [['id' => 20, 'is_correct' => 1], ['id' => 21, 'is_correct' => 0]]],
        ['id' => 3, 'options' => [['id' => 30, 'is_correct' => 0], ['id' => 31, 'is_correct' => 1]]],
    ];
}

test('quiz score: counts correct answers', function () {
    $r = Quiz::score(quiz_fixture(), [1 => 11, 2 => 21, 3 => 31]);
    assert_same(2, $r['score']);
    assert_same(3, $r['total']);
    assert_same([true, false, true], array_column($r['items'], 'right'));
    assert_same(20, $r['items'][1]['correct']);
});

test('quiz score: string keys from $_POST work', function () {
    assert_same(3, Quiz::score(quiz_fixture(), ['1' => '11', '2' => '20', '3' => '31'])['score']);
});

test('quiz score: missing answers are wrong, not errors', function () {
    $r = Quiz::score(quiz_fixture(), [1 => 11]);
    assert_same(1, $r['score']);
    assert_same(null, $r['items'][2]['chosen']);
});

test("quiz score: an option from another question cannot be used to cheat", function () {
    $r = Quiz::score(quiz_fixture(), [1 => 20, 2 => 11, 3 => 'x']);
    assert_same(0, $r['score']);
    assert_same([null, null, null], array_column($r['items'], 'chosen'));
});

test('quiz question validation', function () {
    $ok = ['question' => 'Apa?', 'explanation' => 'Karena.', 'position' => '1', 'correct' => '1', 'options' => ['A', 'B', '', '']];
    assert_same([], Quiz::questionErrors($ok));
    assert_true(isset(Quiz::questionErrors(['options' => ['A', '', '', '']] + $ok)['options']));
    assert_true(isset(Quiz::questionErrors(['correct' => '2'] + $ok)['options']), 'correct answer must be a filled option');
});

test('stats: categories under 5 are merged into "Lainnya"', function () {
    $m = Stats::mergeSmall(['A' => 20, 'B' => 7, 'C' => 3, 'D' => 2, 'E' => 1]);
    assert_same(['A', 'B', 'Lainnya (< 5 digabung)'], $m['labels']);
    assert_same([20, 7, 6], $m['values']);
    assert_same(0, $m['hidden']);
});

test('stats: a merged group that is still under 5 is hidden entirely', function () {
    $m = Stats::mergeSmall(['A' => 9, 'B' => 2, 'C' => 1]);
    assert_same(['A'], $m['labels']);
    assert_same(3, $m['hidden']);
});

test('stats: no category with a count between 1 and 4 is ever shown', function () {
    for ($i = 0; $i < 50; $i++) {
        $counts = [];
        foreach (range('A', 'H') as $l) {
            $counts[$l] = random_int(0, 12);
        }
        foreach (Stats::mergeSmall($counts)['values'] as $v) {
            assert_true($v >= 5, "value $v leaked");
        }
    }
});

test('stats: month keys are 12 consecutive months ending this month', function () {
    $k = Stats::monthKeys(12);
    assert_same(12, count($k));
    assert_same(date('Y-m'), array_key_last($k));
});
