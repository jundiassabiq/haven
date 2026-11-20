<?php
/**
 * All questions are in one form. Without JS they show as one long page; app.js turns them into one-at-a-time steps.
 * @var array $quiz  @var array $questions (no answer key)
 */
$total = count($questions);
?>
<main id="main" class="section-sm" style="background:var(--cream);min-height:80vh">
  <div class="container">
    <form class="quiz-shell panel panel-body" style="padding:clamp(24px,4vw,48px)" method="post" action="<?= e(url('/kuis/' . $quiz['slug'])) ?>" novalidate data-quiz>
      <?= csrf_field() ?>
      <h1 class="sr-only"><?= e($quiz['title']) ?></h1>
      <div class="quiz-top"><span data-quiz-pos aria-live="polite"><?= $total ?> soal</span><a class="back-link small" href="<?= e(url('/kuis')) ?>">Keluar dari kuis</a></div>
      <div class="progress" aria-hidden="true"><span style="width:0"></span></div>

      <?php foreach ($questions as $i => $q): $name = 'q' . $q['id']; ?>
        <div class="quiz-q" data-quiz-step>
          <fieldset>
            <legend tabindex="-1"><span class="sr-only">Soal <?= $i + 1 ?> dari <?= $total ?>: </span><?= e($q['question']) ?></legend>
            <div class="options">
              <?php foreach ($q['options'] as $k => $o): ?>
                <label class="choice"><input type="radio" name="jawaban[<?= (int) $q['id'] ?>]" value="<?= (int) $o['id'] ?>"<?= $k === 0 ? ' required data-msg="Pilih salah satu jawaban."' : '' ?><?= checked(old($name) === (string) $o['id']) ?><?= field_aria($name) ?>><span class="choice-body"><span class="choice-title"><?= e($o['label']) ?></span></span></label>
              <?php endforeach; ?>
            </div>
            <?= field_error($name) ?>
          </fieldset>
        </div>
      <?php endforeach; ?>

      <div class="quiz-nav">
        <button class="btn btn-ghost" type="button" data-quiz-prev hidden><?= icon('arrow-left') ?> Sebelumnya</button>
        <span class="spacer"></span>
        <button class="btn btn-ink" type="button" data-quiz-next hidden>Berikutnya <?= icon('arrow-right') ?></button>
        <button class="btn btn-yellow" type="submit" data-quiz-finish>Lihat hasil</button>
      </div>
    </form>
  </div>
</main>
