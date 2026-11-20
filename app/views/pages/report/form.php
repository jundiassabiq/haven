<?php
/** @var bool $accountAllowed  @var string $defaultIdentity */
$errs = form_errors();
$identity = old('identitas', $defaultIdentity);
?>
<main id="main">
  <section class="section-sm" style="background:var(--cream)">
    <div class="container grid-2">
      <form class="panel panel-body" style="padding:clamp(24px,4vw,48px)" method="post" action="<?= e(url('/lapor')) ?>" enctype="multipart/form-data" novalidate data-stepper aria-label="Formulir laporan">
        <?= csrf_field() ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= Upload::MAX_BYTES ?>">
        <ol class="stepper" aria-label="Langkah laporan">
          <li><span>Apa yang terjadi</span></li>
          <li><span>Ceritakan</span></li>
          <li><span>Waktu &amp; tempat</span></li>
          <li><span>Kontak &amp; kirim</span></li>
        </ol>
        <p class="step-count" data-step-count aria-live="polite">Langkah 1 dari 4</p>

        <?php if ($errs): ?>
          <div class="alert alert-danger" role="alert" style="margin-bottom:28px">
            <?= icon('alert') ?>
            <div><strong>Ada isian yang perlu diperbaiki.</strong>Kami membuka langkah pertama yang perlu diperbaiki. Isian lain masih tersimpan, tetapi file bukti harus dipilih ulang.</div>
          </div>
        <?php endif; ?>

        <!-- STEP 1 -->
        <section data-step>
          <h1 class="step-title">Apa yang kamu alami?</h1>
          <p class="step-lede">Pilih yang paling dekat dengan kejadianmu. Jika tidak ada yang cocok, pilih Belum yakin. Pilihan ini hanya membantu admin memilih pendamping, dan bisa diluruskan nanti.</p>
          <div class="step-body">
            <fieldset class="field">
              <legend class="label">Jenis kejadian</legend>
              <div class="choice-grid choice-grid-2">
                <?php $first = true; foreach (Report::CATEGORIES as $k => $label): ?>
                  <label class="choice"><input type="radio" name="jenis" value="<?= e($k) ?>"<?= checked(old('jenis') === $k) ?><?= $first ? ' required data-msg="Pilih jenis kejadian, atau pilih &quot;Belum yakin&quot;."' : '' ?><?= field_aria('jenis') ?>><span class="choice-body"><span class="choice-title"><?= e($label) ?></span><span class="choice-desc"><?= e(Report::CATEGORY_DESC[$k]) ?></span></span></label>
                <?php $first = false; endforeach; ?>
              </div>
              <?= field_error('jenis') ?>
            </fieldset>

            <fieldset class="field">
              <legend class="label">Apakah kamu dalam bahaya saat ini?</legend>
              <div class="choice-grid choice-grid-2">
                <label class="choice"><input type="radio" name="bahaya" value="ya" required<?= checked(old('bahaya') === 'ya') ?><?= field_aria('bahaya') ?>><span class="choice-body"><span class="choice-title">Ya, saya butuh bantuan segera</span></span></label>
                <label class="choice"><input type="radio" name="bahaya" value="tidak"<?= checked(old('bahaya') === 'tidak') ?><?= field_aria('bahaya') ?>><span class="choice-body"><span class="choice-title">Tidak saat ini</span></span></label>
              </div>
              <?= field_error('bahaya') ?>
            </fieldset>
            <div class="alert alert-danger" data-show-when="bahaya=ya" role="alert">
              <?= icon('phone') ?>
              <div><strong>Hubungi <a href="tel:112">112</a> sekarang jika kamu terancam.</strong>Laporan di Haven tidak dipantau 24 jam, jadi jangan menunggu balasan kami. Setelah kamu aman, laporan ini tetap bisa dikirim, dan akan muncul paling atas di antrean admin.</div>
            </div>
          </div>
        </section>

        <!-- STEP 2 -->
        <section data-step hidden>
          <h2 class="step-title">Apa yang terjadi?</h2>
          <p class="step-lede">Tulis dengan bahasamu sendiri. Detail yang berat boleh dilewati. Admin tidak bisa membaca bagian ini, hanya pendamping yang nanti ditugaskan.</p>
          <div class="step-body">
            <div class="field">
              <label class="label" for="kronologi">Kronologi</label>
              <p class="hint" id="kronologi-hint">Misalnya: di mana kejadiannya, siapa pelakunya bagimu (tanpa perlu nama), dan apa yang kamu butuhkan sekarang.</p>
              <textarea class="textarea" id="kronologi" name="kronologi" rows="8" minlength="30" maxlength="5000" required data-counter="kronologi-count" data-msg="Ceritakan sedikit tentang kejadiannya, minimal satu atau dua kalimat."<?= field_aria('kronologi', 'kronologi-hint') ?>><?= e(old('kronologi')) ?></textarea>
              <span class="counter" id="kronologi-count" aria-live="off"></span>
              <?= field_error('kronologi') ?>
            </div>
            <fieldset class="field">
              <legend class="label">Apakah kejadian ini masih berlangsung?</legend>
              <div class="choice-grid">
                <?php $first = true; foreach (Report::ONGOING as $k => $label): ?>
                  <label class="choice"><input type="radio" name="berlangsung" value="<?= e($k) ?>"<?= $first ? ' required' : '' ?><?= checked(old('berlangsung') === $k) ?><?= field_aria('berlangsung') ?>><span class="choice-body"><span class="choice-title"><?= e($label) ?></span></span></label>
                <?php $first = false; endforeach; ?>
              </div>
              <?= field_error('berlangsung') ?>
            </fieldset>
          </div>
        </section>

        <!-- STEP 3 -->
        <section data-step hidden>
          <h2 class="step-title">Kapan dan di mana?</h2>
          <p class="step-lede">Wilayah dipakai untuk memilih pendamping dan layanan terdekat. Semua isian di langkah ini boleh dikosongkan.</p>
          <div class="step-body">
            <div class="field-row">
              <div class="field">
                <label class="label" for="tanggal">Perkiraan tanggal <span class="opt">(opsional)</span></label>
                <input class="input" type="date" id="tanggal" name="tanggal" max="<?= date('Y-m-d') ?>" value="<?= e(old('tanggal')) ?>"<?= field_aria('tanggal') ?>>
                <?= field_error('tanggal') ?>
              </div>
              <div class="field">
                <label class="label" for="tempat">Jenis tempat <span class="opt">(opsional)</span></label>
                <select class="select" id="tempat" name="tempat"<?= field_aria('tempat') ?>>
                  <option value="">Pilih jenis tempat</option>
                  <?php foreach (Report::PLACES as $p): ?><option<?= selected(old('tempat'), $p) ?>><?= e($p) ?></option><?php endforeach; ?>
                </select>
                <?= field_error('tempat') ?>
              </div>
            </div>
            <div class="field-row">
              <div class="field">
                <label class="label" for="provinsi">Provinsi <span class="opt">(opsional)</span></label>
                <select class="select" id="provinsi" name="provinsi"<?= field_aria('provinsi') ?>>
                  <option value="">Pilih provinsi</option>
                  <?php foreach (Report::PROVINCES as $p): ?><option<?= selected(old('provinsi'), $p) ?>><?= e($p) ?></option><?php endforeach; ?>
                </select>
                <?= field_error('provinsi') ?>
              </div>
              <div class="field">
                <label class="label" for="kota">Kota/kabupaten <span class="opt">(opsional)</span></label>
                <input class="input" id="kota" name="kota" maxlength="80" placeholder="Contoh: Banyumas" autocomplete="off" value="<?= e(old('kota')) ?>"<?= field_aria('kota') ?>>
                <?= field_error('kota') ?>
              </div>
            </div>
            <div class="field">
              <label class="label" for="pelaku">Hubunganmu dengan pelaku <span class="opt">(opsional)</span></label>
              <select class="select" id="pelaku" name="pelaku"<?= field_aria('pelaku') ?>>
                <option value="">Pilih</option>
                <?php foreach (Report::RELATIONS as $p): ?><option<?= selected(old('pelaku'), $p) ?>><?= e($p) ?></option><?php endforeach; ?>
              </select>
              <?= field_error('pelaku') ?>
            </div>
          </div>
        </section>

        <!-- STEP 4 -->
        <section data-step hidden>
          <h2 class="step-title">Lewat mana pendamping boleh menghubungimu?</h2>
          <p class="step-lede">Pesan di Haven selalu aktif. Centang email atau WhatsApp hanya jika tidak ada orang lain yang membukanya.</p>
          <div class="step-body">
            <fieldset class="field">
              <legend class="label">Kirim laporan sebagai</legend>
              <div class="choice-grid choice-grid-2">
                <label class="choice"><input type="radio" name="identitas" value="anonim"<?= checked($identity !== 'akun') ?><?= field_aria('identitas') ?>><span class="choice-body"><span class="choice-title">Anonim</span><span class="choice-desc">Kamu mendapat kode laporan dan PIN. Simpan keduanya untuk membaca dan membalas pesan pendamping.</span></span></label>
                <?php if ($accountAllowed): ?>
                  <label class="choice"><input type="radio" name="identitas" value="akun"<?= checked($identity === 'akun') ?><?= field_aria('identitas') ?>><span class="choice-body"><span class="choice-title">Dengan akun <?= e(Auth::user()['name']) ?></span><span class="choice-desc">Laporan muncul di dasbormu, jadi kamu tidak perlu mengingat kode dan PIN.</span></span></label>
                <?php endif; ?>
              </div>
              <?= field_error('identitas') ?>
            </fieldset>
            <?php if (!$accountAllowed): ?>
              <div class="alert alert-info">
                <?= icon('info') ?>
                <div>Ingin laporan masuk ke akun? <a href="<?= e(url('/masuk')) ?>">Masuk</a> atau <a href="<?= e(url('/daftar')) ?>">buat akun</a> dulu. Kamu juga bisa kirim anonim sekarang, lalu menautkannya ke akun nanti dengan kode dan PIN.</div>
              </div>
            <?php endif; ?>

            <fieldset class="field">
              <legend class="label">Cara dihubungi</legend>
              <div class="check-list">
                <label class="check"><input type="checkbox" checked disabled> <span>Pesan aman di Haven (selalu aktif)</span></label>
                <label class="check"><input type="checkbox" name="kontak_email" value="1"<?= checked(old('kontak_email') === '1') ?>> <span>Email</span></label>
                <label class="check"><input type="checkbox" name="kontak_wa" value="1"<?= checked(old('kontak_wa') === '1') ?>> <span>WhatsApp</span></label>
              </div>
            </fieldset>
            <div class="field" data-show-when="kontak_email=1">
              <label class="label" for="email">Email aman</label>
              <input class="input" type="email" id="email" name="email" autocomplete="email" placeholder="nama@email.com" data-cond-required value="<?= e(old('email')) ?>" data-msg="Tulis email, atau hapus centang Email."<?= field_aria('email', 'email-hint') ?>>
              <p class="hint" id="email-hint">Pendamping hanya mengirim pemberitahuan ke sini, tanpa isi pesan.</p>
              <?= field_error('email') ?>
            </div>
            <div class="field" data-show-when="kontak_wa=1">
              <label class="label" for="wa">Nomor WhatsApp</label>
              <input class="input" type="tel" id="wa" name="wa" inputmode="tel" placeholder="08xx-xxxx-xxxx" pattern="[0-9+\- ]{9,16}" data-msg-pattern="Tulis nomor 9 sampai 16 digit, contoh 0812-3456-7890." data-cond-required value="<?= e(old('wa')) ?>" data-msg="Tulis nomor WhatsApp, atau hapus centang WhatsApp."<?= field_aria('wa', 'wa-hint') ?>>
              <p class="hint" id="wa-hint">Tulis nomor yang hanya kamu pegang. Pendamping tidak akan menyebut Haven di kalimat pertama pesannya.</p>
              <?= field_error('wa') ?>
            </div>

            <div class="field">
              <span class="label" id="bukti-label">Bukti pendukung <span class="opt">(opsional)</span></span>
              <label class="dropzone" data-dropzone="bukti-list">
                <input type="file" name="bukti[]" multiple accept=".jpg,.jpeg,.png,.pdf" aria-labelledby="bukti-label"<?= field_aria('bukti', 'bukti-hint') ?>>
                <?= icon('upload') ?>
                <strong>Pilih file atau seret ke sini</strong>
                <span class="hint" id="bukti-hint">Tangkapan layar, foto, atau dokumen dalam JPG, PNG, atau PDF. Maksimal <?= Upload::MAX_FILES ?> file, masing-masing 5 MB. Nama file aslinya dienkripsi.</span>
              </label>
              <ul class="file-list" id="bukti-list" aria-live="polite"></ul>
              <?= field_error('bukti') ?>
            </div>

            <div class="panel panel-cream panel-body">
              <h3 class="h4" style="margin-bottom:14px">Periksa sebelum mengirim</h3>
              <dl class="kv">
                <div><dt>Jenis</dt><dd data-summary="jenis"></dd></div>
                <div><dt>Wilayah</dt><dd data-summary="provinsi"></dd></div>
                <div><dt>Tanggal</dt><dd data-summary="tanggal"></dd></div>
                <div><dt>Hubungan dengan pelaku</dt><dd data-summary="pelaku"></dd></div>
                <div class="full"><dt>Kronologi</dt><dd data-summary="kronologi"></dd></div>
              </dl>
            </div>

            <div class="field">
              <label class="check">
                <input type="checkbox" id="setuju" name="setuju" value="1" required data-msg="Centang ini agar kami bisa memproses laporanmu."<?= checked(old('setuju') === '1') ?><?= field_aria('setuju') ?>>
                <span>Saya paham kronologi ini hanya bisa dibaca pendamping yang ditugaskan, dan saya bisa meminta laporan dihapus lewat pendamping.</span>
              </label>
              <?= field_error('setuju') ?>
            </div>
          </div>
        </section>

        <div class="step-actions">
          <button class="btn btn-ghost" type="button" data-prev hidden><?= icon('arrow-left') ?> Kembali</button>
          <span class="spacer"></span>
          <button class="btn btn-ink" type="button" data-next>Lanjut <?= icon('arrow-right') ?></button>
          <button class="btn btn-yellow" type="submit" data-submit hidden><span>Kirim laporan</span> <?= icon('send') ?></button>
        </div>
      </form>

      <aside class="stack">
        <div class="panel panel-body stack-sm">
          <h2 class="h4">Sebelum mulai</h2>
          <ul class="stack-sm small" style="color:var(--ink-2)">
            <li class="row" style="flex-wrap:nowrap;align-items:flex-start"><?= icon('clock') ?><span>Biasanya 5 sampai 10 menit. Tombol Kembali tidak menghapus isianmu.</span></li>
            <li class="row" style="flex-wrap:nowrap;align-items:flex-start"><?= icon('lock') ?><span>Admin hanya melihat jenis kejadian dan wilayah. Kronologi dan kontakmu tidak.</span></li>
            <li class="row" style="flex-wrap:nowrap;align-items:flex-start"><?= icon('exit') ?><span>Jika ada yang mendekat, tekan Esc dua kali. Halaman langsung berganti ke pencarian cuaca.</span></li>
          </ul>
        </div>
        <div class="alert alert-warn">
          <?= icon('alert') ?>
          <div><strong>Dalam bahaya?</strong>Hubungi <a href="tel:112">112</a> atau SAPA <a href="tel:129">129</a>. Laporan di Haven tidak dipantau 24 jam.</div>
        </div>
      </aside>
    </div>
  </section>
</main>
