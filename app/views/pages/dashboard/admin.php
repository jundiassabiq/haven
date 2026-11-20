<?php /** @var string $range  @var array $k  @var array $workload  @var int $hidden */
$src = e(url('/api/statistik', ['rentang' => $range]));
$num = static fn (int $n) => number_format($n, 0, ',', '.');
?>
<div class="app-page-head">
  <div>
    <h1 class="app-title">Statistik</h1>
    <p class="app-sub">Data agregat tanpa isi laporan. Kategori dengan kurang dari <?= Stats::MIN_GROUP ?> kasus digabung ke "Lainnya" agar penyintas tidak bisa dikenali.</p>
  </div>
  <form method="get" action="<?= e(url('/admin')) ?>" data-autosubmit class="field" style="min-width:200px">
    <label class="label" for="range">Rentang</label>
    <select class="select select-sm" id="range" name="rentang"><?php foreach (Stats::RANGES as $key => $label): ?><option value="<?= e($key) ?>"<?= selected($range, $key) ?>><?= e($label) ?></option><?php endforeach; ?></select>
    <noscript><button class="btn btn-ink btn-xs" type="submit">Terapkan</button></noscript>
  </form>
</div>

<dl class="kpis" style="margin-bottom:24px">
  <div class="kpi"><dt>Laporan masuk</dt><dd><?= $num($k['total']) ?></dd><span class="kpi-note">+<?= $num($k['thisMonth']) ?> bulan ini</span></div>
  <div class="kpi"><dt>Menunggu ditinjau</dt><dd><?= $num($k['waiting']) ?></dd><span class="kpi-note"><?= $k['oldestWaiting'] ? 'Tertua ' . e(Notifier::ago($k['oldestWaiting'])) : 'Antrean bersih' ?></span></div>
  <div class="kpi"><dt>Sedang didampingi</dt><dd><?= $num($k['active']) ?></dd><span class="kpi-note">oleh <?= $num($k['companions']) ?> pendamping</span></div>
  <div class="kpi"><dt>Rata-rata sampai disapa</dt><dd><?= $k['avgHours'] === null ? '-' : e(str_replace('.', ',', (string) $k['avgHours'])) . ' j' ?></dd><span class="kpi-note">Dari penugasan ke pesan pertama</span></div>
</dl>

<div class="grid-2" style="margin-bottom:24px">
  <section class="panel">
    <div class="panel-head"><h2 class="panel-title">Laporan per bulan</h2><span class="small muted">12 bulan terakhir</span></div>
    <div class="panel-body"><div class="chart"><canvas role="img" aria-label="Grafik laporan per bulan, sedang dimuat" data-chart-src="<?= $src ?>" data-chart-key="trend"></canvas></div></div>
  </section>
  <section class="panel">
    <div class="panel-head"><h2 class="panel-title">Jenis kejadian</h2><span class="small muted"><?= e(Stats::RANGES[$range]) ?></span></div>
    <div class="panel-body">
      <div class="chart"><canvas role="img" aria-label="Grafik jenis kejadian, sedang dimuat" data-chart-src="<?= $src ?>" data-chart-key="categories"></canvas></div>
      <?php if ($hidden > 0): ?><p class="small muted" style="margin-top:10px"><?= $hidden ?> laporan dari kategori kecil tidak ditampilkan demi privasi.</p><?php endif; ?>
    </div>
  </section>
</div>

<div class="grid-2">
  <section class="panel">
    <div class="panel-head"><h2 class="panel-title">Kasus baru dan selesai</h2><span class="small muted">6 bulan terakhir</span></div>
    <div class="panel-body">
      <div class="chart"><canvas role="img" aria-label="Grafik kasus baru dan selesai, sedang dimuat" data-chart-src="<?= $src ?>" data-chart-key="status"></canvas></div>
      <div class="chart-legend"><span><i style="background:#1C1B19"></i>Kasus baru</span><span><i style="background:#FFC907"></i>Kasus selesai</span></div>
    </div>
  </section>
  <section class="panel">
    <div class="panel-head"><h2 class="panel-title">Beban pendamping</h2><a class="link-btn" href="<?= e(url('/admin/pengguna')) ?>">Kelola</a></div>
    <?php if ($workload): ?>
      <div class="table-wrap" style="border:0;border-radius:0 0 6px 6px">
        <table class="table">
          <thead><tr><th scope="col">Pendamping</th><th scope="col" class="num">Aktif</th><th scope="col" class="num">Baru bulan ini</th></tr></thead>
          <tbody>
            <?php foreach ($workload as $w): ?>
              <tr><td><a class="row-link" href="<?= e(url('/admin/antrean', ['pendamping' => $w['id']])) ?>"><?= e($w['name']) ?></a><?= $w['region'] ? '<span class="cell-sub">' . e($w['region']) . '</span>' : '' ?></td><td class="num"><?= (int) $w['active'] ?></td><td class="num"><?= (int) $w['this_month'] ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="panel-body small muted">Belum ada pendamping aktif. Laporan baru tidak bisa ditugaskan sampai ada. <a href="<?= e(url('/admin/pengguna')) ?>">Undang pendamping</a>.</p>
    <?php endif; ?>
  </section>
</div>
