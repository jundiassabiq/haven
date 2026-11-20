<?php
/** @var array $quiz  @var ?array $question  @var int $nextPosition  @var array $articles */
$opts = $question ? array_map(static fn ($o) => $o['label'], $question['options']) : [];
$opts = array_pad($opts, 4, '');
$correctIdx = '';
foreach ($question['options'] ?? [] as $i => $o) {
    if ((int) $o['is_correct']) {
        $correctIdx = (string) $i;
    }
}
$correct = old('correct', $correctIdx);
$action = $question ? url('/admin/kuis/soal/' . $question['id']) : url('/admin/kuis/soal');
?>
<a class="back-link" href="<?= e(url('/admin/kuis')) ?>"><?= icon('arrow-left') ?> Daftar soal</a>
<form method="post" action="<?= e($action) ?>" novalidate data-validate>
  <?= csrf_field() ?>
  <div class="app-page-head">
    <h1 class="app-title"><?= $question ? 'Ubah soal ' . (int) $question['position'] : 'Tambah soal' ?></h1>
    <button class="btn btn-yellow btn-sm" type="submit">Simpan soal</button>
  </div>
  <div class="grid-2">
    <div class="panel panel-body form-stack">
      <div class="field"><label class="label" for="q">Pertanyaan</label><textarea class="textarea textarea-sm" id="q" name="question" maxlength="500" required data-msg="Tulis pertanyaannya."<?= field_aria('question') ?>><?= e(old('question', (string) ($question['question'] ?? ''))) ?></textarea><?= field_error('question') ?></div>
      <fieldset class="field">
        <legend class="label">Pilihan jawaban</legend>
        <p class="hint" id="opt-hint">Tandai satu jawaban yang benar. Pilihan yang dikosongkan tidak dipakai. Kunci jawaban tidak pernah dikirim ke browser peserta.</p>
        <div class="stack-sm">
          <?php foreach ($opts as $i => $label): $L = chr(65 + $i); ?>
            <div class="row" style="flex-wrap:nowrap">
              <label class="check"><input type="radio" name="correct" value="<?= $i ?>"<?= checked($correct === (string) $i) ?><?= $i === 0 ? ' required data-msg="Tandai satu jawaban yang benar."' : '' ?>><span class="sr-only">Jawaban benar: pilihan <?= $L ?></span></label>
              <label class="sr-only" for="o<?= $i ?>">Pilihan <?= $L ?></label>
              <input class="input" id="o<?= $i ?>" name="options[<?= $i ?>]" maxlength="255" value="<?= e(old("option_$i", $label)) ?>" placeholder="Pilihan <?= $L ?>"<?= $i < 2 ? ' required data-msg="Isi minimal dua pilihan."' : '' ?>>
            </div>
          <?php endforeach; ?>
        </div>
        <?= field_error('options') ?>
      </fieldset>
      <div class="field"><label class="label" for="ex">Pembahasan</label><textarea class="textarea textarea-sm" id="ex" name="explanation" maxlength="1000" required data-msg="Tulis pembahasan agar peserta tetap belajar."<?= field_aria('explanation') ?>><?= e(old('explanation', (string) ($question['explanation'] ?? ''))) ?></textarea><?= field_error('explanation') ?></div>
    </div>
    <aside class="panel panel-body form-stack">
      <div class="field"><label class="label" for="ord">Urutan</label><input class="input mono" id="ord" name="position" type="number" min="1" max="100" required value="<?= e(old('position', (string) ($question['position'] ?? $nextPosition))) ?>"<?= field_aria('position') ?>><?= field_error('position') ?></div>
      <div class="field">
        <label class="label" for="rel">Artikel terkait</label>
        <select class="select" id="rel" name="related"><option value="">Tidak ada</option><?php foreach ($articles as $a): ?><option value="<?= (int) $a['id'] ?>"<?= selected(old('related', (string) ($question['related_article_id'] ?? '')), (string) $a['id']) ?>><?= e($a['title']) ?></option><?php endforeach; ?></select>
        <p class="hint">Ditautkan di halaman hasil kuis bila jawaban peserta kurang tepat.</p>
      </div>
      <?php if ($question): ?>
        <button class="btn btn-ghost btn-sm" type="button" data-open-dialog="dlg-del" style="justify-self:start"><?= icon('trash') ?> Hapus soal</button>
      <?php endif; ?>
    </aside>
  </div>
</form>
<?php if ($question): ?>
  <dialog class="modal" id="dlg-del" aria-labelledby="dlg-del-t">
    <form method="post" action="<?= e(url('/admin/kuis/soal/' . $question['id'] . '/hapus')) ?>">
      <?= csrf_field() ?>
      <div class="modal-body"><h2 id="dlg-del-t">Hapus soal ini?</h2><p>Riwayat skor peserta lama tetap tersimpan, tetapi soal ini tidak muncul lagi.</p></div>
      <div class="modal-actions"><button class="btn btn-ghost btn-sm" type="button" data-close-dialog>Batal</button><button class="btn btn-danger btn-sm" type="submit">Hapus soal</button></div>
    </form>
  </dialog>
<?php endif; ?>
