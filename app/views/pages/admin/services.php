<?php
/** @var array $services */
$unverified = count(array_filter($services, static fn ($s) => !(int) $s['is_verified']));
?>
<div data-filter-list>
  <div class="app-page-head">
    <div><h1 class="app-title">Layanan</h1><p class="app-sub">Direktori untuk publik dan rujukan kasus. Kontak hanya tampil di halaman publik setelah ditandai terverifikasi.</p></div>
    <a class="btn btn-yellow btn-sm" href="<?= e(url('/admin/layanan/baru')) ?>"><?= icon('plus') ?> Tambah layanan</a>
  </div>
  <?php if ($unverified): ?>
    <div class="alert alert-warn" style="margin-bottom:20px">
      <?= icon('alert') ?>
      <div><strong><?= $unverified ?> layanan belum diverifikasi.</strong>Hubungi tiap layanan untuk memastikan nomor dan jam operasional sebelum demo, lalu tandai sebagai terverifikasi.</div>
    </div>
  <?php endif; ?>
  <div class="filter-bar">
    <div class="search"><?= icon('search') ?><label class="sr-only" for="q">Cari layanan</label><input class="input" id="q" type="search" placeholder="Cari nama atau kota" data-search></div>
    <div class="field"><label class="label" for="fv">Verifikasi</label><select class="select select-sm" id="fv" data-filter-select="verified"><option value="">Semua</option><option value="ya">Terverifikasi</option><option value="tidak">Perlu verifikasi</option></select></div>
  </div>
  <div class="table-wrap table-cards">
    <table class="table">
      <thead><tr><th scope="col">Layanan</th><th scope="col">Jenis</th><th scope="col">Wilayah</th><th scope="col">Verifikasi</th><th scope="col" class="num">Rujukan</th><th scope="col"><span class="sr-only">Aksi</span></th></tr></thead>
      <tbody>
        <?php foreach ($services as $s): $v = (int) $s['is_verified']; ?>
          <tr data-item data-verified="<?= $v ? 'ya' : 'tidak' ?>">
            <td data-label="Layanan"><strong><?= e($s['name']) ?></strong></td>
            <td data-label="Jenis"><?= e(Service::TYPES[$s['type']]) ?></td>
            <td data-label="Wilayah"><?= e($s['province'] === 'Nasional' ? 'Nasional' : $s['city']) ?></td>
            <td data-label="Verifikasi"><?= $v ? '<span class="badge badge-ok">Terverifikasi</span>' : '<span class="badge badge-verify">Perlu verifikasi</span>' ?></td>
            <td data-label="Rujukan" class="num"><?= (int) $s['referrals'] ?></td>
            <td class="actions" data-label="Aksi"><a class="btn <?= $v ? 'btn-ghost' : 'btn-ink' ?> btn-xs" href="<?= e(url('/admin/layanan/' . $s['id'])) ?>"><?= $v ? 'Ubah' : 'Verifikasi' ?></a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="empty" data-empty hidden style="margin-top:12px"><?= icon('building') ?><h3>Tidak ada layanan</h3><p>Ubah filter atau tambah layanan baru.</p></div>
</div>
