<?php /** @var array $quiz  @var array $questions  @var array $rates  @var array $stats */ ?>
<div class="app-page-head">
  <div>
    <h1 class="app-title"><?= e($quiz['title']) ?></h1>
    <p class="app-sub"><?= count($questions) ?> soal · <?= $quiz['status'] === 'terbit' ? 'terbit' : 'draf' ?> · <?= number_format((int) $stats['n'], 0, ',', '.') ?> kali dikerjakan<?= $stats['avg_score'] !== null ? ' · rata-rata skor <span class="mono">' . e(number_format((float) $stats['avg_score'], 1, ',', '.')) . '</span>' : '' ?></p>
  </div>
  <div class="row">
    <a class="btn btn-ghost btn-sm" href="<?= e(url('/kuis')) ?>" target="_blank" rel="noopener"><?= icon('eye') ?> Lihat kuis</a>
    <a class="btn btn-yellow btn-sm" href="<?= e(url('/admin/kuis/soal/baru')) ?>"><?= icon('plus') ?> Tambah soal</a>
  </div>
</div>
<div class="table-wrap table-cards">
  <table class="table">
    <thead><tr><th scope="col" class="num">No</th><th scope="col">Pertanyaan</th><th scope="col" class="num">Dijawab benar</th><th scope="col"><span class="sr-only">Aksi</span></th></tr></thead>
    <tbody>
      <?php foreach ($questions as $q): $rate = $rates[(int) $q['id']] ?? null; ?>
        <tr>
          <td class="num" data-label="No"><?= (int) $q['position'] ?></td>
          <td data-label="Pertanyaan"><a class="row-link" href="<?= e(url('/admin/kuis/soal/' . $q['id'])) ?>"><?= e($q['question']) ?></a></td>
          <td class="num" data-label="Dijawab benar"><?= $rate === null ? '<span class="muted">-</span>' : ($rate < 60 ? '<span class="badge" data-urgency="tinggi">' . $rate . '%</span>' : $rate . '%') ?></td>
          <td class="actions" data-label="Aksi"><a class="btn btn-ghost btn-xs" href="<?= e(url('/admin/kuis/soal/' . $q['id'])) ?>">Ubah</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div class="alert alert-info" style="margin-top:20px">
  <?= icon('info') ?>
  <div>Soal dengan tingkat benar di bawah 60% bisa jadi tanda pertanyaannya kurang jelas, atau topiknya perlu artikel tambahan.</div>
</div>
