<?php /** @var string|null $formError */ ?>
<main id="main">
  <section class="page-head">
    <div class="container">
      <h1 class="h2">Buka laporan anonim</h1>
      <p class="lede">Masukkan kode dan PIN dari halaman setelah kamu mengirim laporan. Di dalamnya ada status laporan, pesan pendamping, dan jadwal panggilan.</p>
    </div>
  </section>

  <section class="section-sm">
    <div class="container grid-2">
      <form class="panel panel-body form-stack" style="padding:clamp(24px,4vw,40px)" method="post" action="<?= e(url('/lacak')) ?>" novalidate data-validate>
        <?= csrf_field() ?>
        <?php if (!empty($formError)): ?>
          <div class="alert alert-danger" role="alert"><?= icon('alert') ?><div><?= e($formError) ?></div></div>
        <?php endif; ?>
        <div class="field">
          <label class="label" for="kode">Kode laporan</label>
          <input class="input mono" id="kode" name="kode" placeholder="HV-XXXXXX" autocomplete="off" spellcheck="false" maxlength="9" required pattern="HV-[A-Z0-9]{6}" data-uppercase value="<?= e(old('kode')) ?>" data-msg="Masukkan kode laporanmu." data-msg-pattern="Kode diawali HV- lalu 6 huruf atau angka, contoh HV-9B2X6P."<?= field_aria('kode') ?>>
          <?= field_error('kode') ?>
        </div>
        <div class="field">
          <label class="label" for="pin">PIN</label>
          <div class="input-group">
            <input class="input mono" id="pin" name="pin" type="password" inputmode="numeric" maxlength="6" autocomplete="off" required pattern="[0-9]{6}" data-digits data-msg="Masukkan PIN 6 digit." data-msg-pattern="PIN terdiri dari 6 angka."<?= field_aria('pin') ?>>
            <button class="reveal" type="button" data-reveal="pin" aria-pressed="false"><?= icon('eye') ?><span class="sr-only">Tampilkan</span></button>
          </div>
          <?= field_error('pin') ?>
        </div>
        <div class="alert alert-info">
          <?= icon('shield') ?>
          <div>Setelah 5 kali salah, halaman ini dikunci 15 menit. Ini mencegah orang lain menebak PIN-mu.</div>
        </div>
        <button class="btn btn-ink btn-lg" type="submit">Buka laporan <?= icon('arrow-right') ?></button>
      </form>

      <aside class="stack">
        <div class="panel panel-body stack-sm">
          <h2 class="h4">Lupa kode atau PIN?</h2>
          <p class="small" style="color:var(--ink-2)">Kami tidak bisa memulihkannya, karena kami hanya menyimpan versi acak dari PIN. Buat laporan baru dan tulis di kronologi bahwa ini lanjutan dari laporan lama.</p>
          <a class="text-link" href="<?= e(url('/lapor')) ?>">Buat laporan baru <?= icon('arrow-right') ?></a>
        </div>
        <div class="panel panel-body stack-sm">
          <h2 class="h4">Punya akun?</h2>
          <p class="small" style="color:var(--ink-2)">Laporan yang dikirim dengan akun bisa dibuka dari dasbor tanpa kode dan PIN.</p>
          <a class="text-link" href="<?= e(url('/masuk')) ?>">Masuk <?= icon('arrow-right') ?></a>
        </div>
      </aside>
    </div>
  </section>
</main>
