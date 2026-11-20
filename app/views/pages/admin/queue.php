<?php /** @var array $rows  @var array $pg  @var array $f  @var array $companions */ ?>
<div class="app-page-head">
  <div><h1 class="app-title">Antrean kasus</h1><p class="app-sub">Admin melihat jenis, wilayah, dan urgensi untuk menugaskan pendamping. Isi kronologi hanya bisa dibaca pendamping yang ditugaskan.</p></div>
</div>

<form class="filter-bar" method="get" action="<?= e(url('/admin/antrean')) ?>" data-autosubmit role="search">
  <div class="search"><?= icon('search') ?><label class="sr-only" for="q">Cari kode atau wilayah</label><input class="input" id="q" name="q" type="search" placeholder="Cari kode atau wilayah" value="<?= e($f['q']) ?>"></div>
  <div class="field"><label class="label" for="fs">Status</label><select class="select select-sm" id="fs" name="status"><option value="">Semua status</option><?php foreach (Report::STATUS as $k => $v): ?><option value="<?= e($k) ?>"<?= selected($f['status'], $k) ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label class="label" for="fu">Urgensi</label><select class="select select-sm" id="fu" name="urgensi"><option value="">Semua urgensi</option><?php foreach (Report::URGENCY as $k => $v): ?><option value="<?= e($k) ?>"<?= selected($f['urgensi'], $k) ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label class="label" for="fj">Jenis</label><select class="select select-sm" id="fj" name="jenis"><option value="">Semua jenis</option><?php foreach (Report::CATEGORIES as $k => $v): ?><option value="<?= e($k) ?>"<?= selected($f['jenis'], $k) ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label class="label" for="fp">Pendamping</label><select class="select select-sm" id="fp" name="pendamping"><option value="">Semua</option><?php foreach ($companions as $c): ?><option value="<?= (int) $c['id'] ?>"<?= selected($f['pendamping'], (string) $c['id']) ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
  <button class="btn btn-ink btn-sm" type="submit">Terapkan</button>
</form>

<?php if ($rows): ?>
  <div class="table-wrap table-cards">
    <table class="table">
      <thead><tr><th scope="col">Kode</th><th scope="col">Masuk</th><th scope="col">Jenis</th><th scope="col">Wilayah</th><th scope="col">Urgensi</th><th scope="col">Status</th><th scope="col">Pendamping</th><th scope="col"><span class="sr-only">Aksi</span></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): $new = $r['status'] === 'diterima'; ?>
          <tr class="<?= $new ? 'is-unread-row' : '' ?>">
            <td data-label="Kode" class="mono"><a class="row-link" href="<?= e(url('/admin/kasus/' . $r['code'])) ?>"><?= e($r['code']) ?></a><span class="cell-sub"><?= $r['user_id'] ? 'Akun' : 'Anonim' ?><?= (int) $r['in_danger'] ? ' · dalam bahaya' : '' ?></span></td>
            <td data-label="Masuk" class="mono"><?= e(mb_strtoupper(Notifier::ago($r['created_at']))) ?></td>
            <td data-label="Jenis"><?= e(Report::CATEGORIES[$r['category']]) ?></td>
            <td data-label="Wilayah"><?= e($r['city'] ?: ($r['province'] ?: '-')) ?></td>
            <td data-label="Urgensi"><?= urgency_badge($r['urgency']) ?></td>
            <td data-label="Status"><?= status_badge($r['status']) ?></td>
            <td data-label="Pendamping"><?= $r['companion_name'] ? e($r['companion_name']) : '<span class="muted">Belum ada</span>' ?></td>
            <td class="actions" data-label="Aksi"><a class="btn <?= $new ? 'btn-yellow' : 'btn-ghost' ?> btn-xs" href="<?= e(url('/admin/kasus/' . $r['code'])) ?>"><?= $new ? 'Tinjau' : 'Buka' ?></a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php $shown = count($rows); include APP_PATH . '/views/partials/pagination.php'; ?>
<?php else: ?>
  <div class="empty">
    <?= icon('inbox') ?>
    <h3><?= array_filter($f) ? 'Tidak ada kasus di filter ini' : 'Antrean kosong' ?></h3>
    <p><?= array_filter($f) ? 'Ubah atau hapus filter untuk melihat kasus lain.' : 'Laporan baru akan muncul di sini dan kamu akan menerima notifikasi.' ?></p>
    <?php if (array_filter($f)): ?><a class="btn btn-ink btn-sm" href="<?= e(url('/admin/antrean')) ?>">Hapus filter</a><?php endif; ?>
  </div>
<?php endif; ?>
