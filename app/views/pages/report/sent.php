<?php /** @var string $code  @var string $pin */ ?>
<main id="main">
  <section class="section-sm" style="background:var(--cream)">
    <div class="container" style="max-width:880px">
      <h1 class="h2">Laporanmu terkirim.</h1>
      <p class="lede" style="margin-top:16px">Laporan ini masuk ke antrean admin. Pendamping akan membalas di halaman laporan, jadi simpan kode dan PIN di bawah. Tanpa keduanya, laporan anonim tidak bisa dibuka lagi.</p>

      <dl class="secret" style="margin-top:40px">
        <div>
          <dt>Kode laporan</dt>
          <dd><?= e($code) ?></dd>
          <button class="btn btn-ghost btn-xs" type="button" data-copy="<?= e($code) ?>"><?= icon('copy') ?><span>Salin kode</span></button>
        </div>
        <div>
          <dt>PIN</dt>
          <dd><?= e($pin) ?></dd>
          <button class="btn btn-ghost btn-xs" type="button" data-copy="<?= e($pin) ?>"><?= icon('copy') ?><span>Salin PIN</span></button>
        </div>
      </dl>

      <div class="alert alert-warn" style="margin-top:20px">
        <?= icon('alert') ?>
        <div><strong>PIN ini hanya muncul sekali.</strong>Kami hanya menyimpan versi acaknya, jadi kami pun tidak bisa memberitahumu PIN ini lagi. Salin ke tempat yang tidak dibuka orang lain, atau unduh sebagai teks.</div>
      </div>

      <div class="row" style="margin-top:28px">
        <a class="btn btn-yellow" href="<?= e(url('/kasus/' . $code)) ?>">Buka laporan saya <?= icon('arrow-right') ?></a>
        <button class="btn btn-ghost" type="button" data-download="<?= e("Haven\\nKode laporan: $code\\nPIN: $pin\\nLacak di: " . Mailer::absoluteUrl('/lacak')) ?>" data-filename="catatan.txt"><?= icon('download') ?> Unduh sebagai teks</button>
      </div>
    </div>
  </section>

  <section class="section-sm">
    <div class="container grid-halves" style="max-width:880px">
      <div>
        <h2 class="h3">Setelah ini</h2>
        <ol class="timeline" style="margin-top:28px">
          <li class="is-current"><span class="tl-dot"></span><div><p class="tl-title">Diterima</p><p class="tl-meta">Baru saja</p><p class="tl-body"><?= e(Report::STATUS_HELP['diterima']) ?></p></div></li>
          <li class="is-pending"><span class="tl-dot"></span><div><p class="tl-title">Ditinjau</p><p class="tl-body"><?= e(Report::STATUS_HELP['ditinjau']) ?></p></div></li>
          <li class="is-pending"><span class="tl-dot"></span><div><p class="tl-title">Didampingi</p><p class="tl-body"><?= e(Report::STATUS_HELP['didampingi']) ?></p></div></li>
        </ol>
      </div>
      <div class="stack">
        <h2 class="h3">Bacaan untuk sekarang</h2>
        <a class="text-link" href="<?= e(url('/edukasi/langkah-setelah-kejadian')) ?>">Menjaga bukti dan memeriksakan kesehatan <?= icon('arrow-right') ?></a>
        <a class="text-link" href="<?= e(url('/layanan')) ?>">Lembaga yang bisa kamu hubungi langsung <?= icon('arrow-right') ?></a>
        <a class="text-link" href="<?= e(url('/tips-aman')) ?>">Menghapus Haven dari riwayat browser <?= icon('arrow-right') ?></a>
      </div>
    </div>
  </section>
</main>
