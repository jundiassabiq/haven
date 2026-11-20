<?php /** @var array $quiz  @var int $count  @var array $history */ ?>
<main id="main">
  <section class="section" style="background:var(--cream)">
    <div class="container proof-grid">
      <div>
        <h1 class="h2"><?= e($quiz['title']) ?></h1>
        <p class="lede" style="margin-top:18px"><?= e($quiz['description']) ?></p>
        <dl class="kv" style="margin-top:36px;max-width:440px">
          <div><dt>Jumlah soal</dt><dd class="mono"><?= $count ?></dd></div>
          <div><dt>Perkiraan waktu</dt><dd class="mono"><?= max(1, (int) ceil($count / 2)) ?> MENIT</dd></div>
          <div><dt>Perlu akun?</dt><dd>Tidak. Masuk dulu jika ingin skornya tersimpan.</dd></div>
          <div><dt>Rujukan</dt><dd>UU No. 12 Tahun 2022</dd></div>
        </dl>
        <div class="row" style="margin-top:36px">
          <a class="btn btn-yellow btn-lg" href="<?= e(url('/kuis/' . $quiz['slug'] . '/kerjakan')) ?>">Mulai kuis <?= icon('arrow-right') ?></a>
          <a class="text-link" href="<?= e(url('/edukasi')) ?>">Baca artikelnya dulu <?= icon('arrow-right') ?></a>
        </div>
        <?php if ($history): ?>
          <div class="panel panel-body" style="margin-top:36px;max-width:440px">
            <h2 class="h4" style="margin-bottom:10px">Riwayatmu</h2>
            <ul class="stack-sm small">
              <?php foreach ($history as $h): ?>
                <li class="row-between"><span class="mono"><?= e(mb_strtoupper(id_date($h['created_at'], true))) ?></span><a class="link-btn" href="<?= e(url('/kuis/hasil/' . $h['id'])) ?>"><?= (int) $h['score'] ?> / <?= (int) $h['total'] ?> benar</a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
      <div class="media" style="aspect-ratio:4/5;border-radius:6px"><img src="<?= asset('img/laptop.jpg') ?>" alt="Tangan mengetik di laptop" width="1600" height="1067" loading="lazy"></div>
    </div>
  </section>
</main>
