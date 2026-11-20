<?php
/** @var array $quiz  @var array $attempt  @var array $questions  @var array $result  @var array $articles */
$items = [];
foreach ($result['items'] as $it) {
    $items[$it['question_id']] = $it;
}
$opt = static function (array $q, ?int $id): string {
    foreach ($q['options'] as $o) {
        if ((int) $o['id'] === $id) {
            return $o['label'];
        }
    }
    return 'Tidak dijawab';
};
$wrongArticles = [];
?>
<main id="main">
  <section class="section-sm" style="background:var(--yellow)">
    <div class="container row-between" style="align-items:flex-end">
      <div>
        <h1 class="h2">Hasil kuismu</h1>
        <p style="margin-top:12px;max-width:36em"><?= e(Quiz::message((int) $result['score'], (int) $result['total'])) ?></p>
      </div>
      <p class="score"><b><?= (int) $result['score'] ?></b><span>/ <?= (int) $result['total'] ?> benar</span></p>
    </div>
  </section>

  <section class="section-sm">
    <div class="container grid-2">
      <div>
        <h2 class="h3" style="margin-bottom:24px">Pembahasan</h2>
        <ol class="review">
          <?php foreach ($questions as $i => $q): $it = $items[(int) $q['id']]; if (!$it['right'] && $q['related_article_id'] && isset($articles[(int) $q['related_article_id']])) { $wrongArticles[(int) $q['related_article_id']] = $articles[(int) $q['related_article_id']]; } ?>
            <li class="<?= $it['right'] ? 'is-right' : 'is-wrong' ?>">
              <p class="review-q"><?= icon($it['right'] ? 'check-circle' : 'x-circle') ?><span><span class="sr-only"><?= $it['right'] ? 'Benar' : 'Kurang tepat' ?>: </span><?= $i + 1 ?>. <?= e($q['question']) ?></span></p>
              <div class="review-a">
                <?php if (!$it['right']): ?><span>Jawabanmu: <?= e($opt($q, $it['chosen'])) ?></span><?php endif; ?>
                <span><b>Jawaban tepat:</b> <?= e($opt($q, $it['correct'])) ?></span>
                <span><?= e($q['explanation']) ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
      <aside class="stack" style="position:sticky;top:96px">
        <?php if (!Auth::check()): ?>
          <div class="panel panel-body stack-sm">
            <h2 class="h4">Skor ini tidak tersimpan</h2>
            <p class="small" style="color:var(--ink-2)">Hasil kuis tanpa akun hanya bisa dibuka dari browser ini. Masuk sebelum mengerjakan lagi jika ingin riwayatnya ada di dasbor.</p>
            <a class="btn btn-ink btn-sm" href="<?= e(url('/masuk')) ?>">Masuk</a>
          </div>
        <?php endif; ?>
        <div class="panel panel-body stack-sm">
          <h2 class="h4">Artikel terkait</h2>
          <?php foreach (($wrongArticles ?: array_slice($articles, 0, 2, true)) as $a): ?>
            <a class="text-link" href="<?= e(url('/edukasi/' . $a['slug'])) ?>"><?= e($a['title']) ?> <?= icon('arrow-right') ?></a>
          <?php endforeach; ?>
        </div>
        <a class="btn btn-ghost" href="<?= e(url('/kuis/' . $quiz['slug'] . '/kerjakan')) ?>"><?= icon('refresh') ?> Ulangi kuis</a>
      </aside>
    </div>
  </section>
</main>
