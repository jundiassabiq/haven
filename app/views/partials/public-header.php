<?php
/** @var string $active */
$active ??= '';
$nav = [
    ['lapor', '/lapor', 'Lapor'],
    ['lacak', '/lacak', 'Lacak laporan'],
    ['edukasi', '/edukasi', 'Edukasi'],
    ['kuis', '/kuis', 'Kuis'],
    ['layanan', '/layanan', 'Layanan bantuan'],
    ['tentang', '/tentang', 'Tentang'],
];
$user = Auth::user();
$links = '';
foreach ($nav as [$k, $href, $label]) {
    $links .= '<li><a href="' . e(url($href)) . '"' . current_attr($k, $active) . '>' . e($label) . '</a></li>';
}
$accountHref = $user ? url(Auth::homePath($user)) : url('/masuk');
$accountLabel = $user ? 'Akun saya' : 'Masuk';
?>
<a class="skip-link" href="#main">Lewati ke konten</a>
<header class="site-header" data-header>
  <div class="container header-inner">
    <a class="wordmark" href="<?= e(url('/')) ?>" aria-label="Haven, ke beranda">haven</a>
    <nav class="primary-nav" aria-label="Utama"><ul><?= $links ?></ul></nav>
    <div class="header-actions">
      <a class="link-quiet" href="<?= e($accountHref) ?>"><?= e($accountLabel) ?></a>
      <a class="btn btn-yellow btn-sm" href="<?= e(url('/lapor')) ?>">Buat laporan</a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-nav" data-menu-toggle>
        <span class="sr-only">Buka menu</span>
        <?= icon('menu', 'icon icon-menu') ?><?= icon('close', 'icon icon-close') ?>
      </button>
    </div>
  </div>
  <nav class="mobile-nav" id="mobile-nav" aria-label="Menu seluler" hidden data-mobile-nav>
    <ul><?= $links ?><li><a href="<?= e($accountHref) ?>"><?= e($accountLabel) ?></a></li></ul>
    <a class="btn btn-yellow btn-block" href="<?= e(url('/lapor')) ?>">Buat laporan</a>
  </nav>
</header>
