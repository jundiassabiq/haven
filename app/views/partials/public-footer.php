<footer class="site-footer">
  <div class="container">
    <div class="footer-top">
      <a class="wordmark wordmark-light" href="<?= e(url('/')) ?>" aria-label="Haven, ke beranda">haven</a>
      <p class="footer-emergency">Dalam bahaya sekarang? Hubungi <a href="tel:112" class="mono">112</a> atau SAPA <a href="tel:129" class="mono">129</a>.</p>
    </div>
    <div class="footer-cols">
      <nav aria-label="Layanan"><h2 class="footer-h">Layanan</h2><ul>
        <li><a href="<?= e(url('/lapor')) ?>">Buat laporan</a></li>
        <li><a href="<?= e(url('/lacak')) ?>">Lacak laporan</a></li>
        <li><a href="<?= e(url('/layanan')) ?>">Direktori layanan</a></li>
      </ul></nav>
      <nav aria-label="Belajar"><h2 class="footer-h">Belajar</h2><ul>
        <li><a href="<?= e(url('/edukasi')) ?>">Edukasi</a></li>
        <li><a href="<?= e(url('/kuis')) ?>">Kuis</a></li>
        <li><a href="<?= e(url('/tips-aman')) ?>">Tips menjelajah aman</a></li>
      </ul></nav>
      <nav aria-label="Tentang"><h2 class="footer-h">Tentang</h2><ul>
        <li><a href="<?= e(url('/tentang')) ?>">Tentang &amp; SDGs 5</a></li>
        <li><a href="<?= e(url('/masuk')) ?>">Masuk</a></li>
        <li><a href="<?= e(url('/daftar')) ?>">Daftar</a></li>
      </ul></nav>
      <div><h2 class="footer-h">Kerahasiaan</h2>
        <p class="footer-note">Kronologi dan pesan dienkripsi. Admin tidak bisa membacanya. Pendampingmu bisa.</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> Haven. Proyek Responsi Pemrograman Web I, Informatika Unsoed.</p>
      <p>Laporan di Haven tidak dipantau 24 jam.</p>
    </div>
  </div>
</footer>
