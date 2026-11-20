<?php
/**
 * One directory entry. Same markup as serviceItem() in app.js.
 * @var array $s  services row  @var bool|null $showContactUnverified  survivors with a referral see contacts even if unverified
 */
$pub = Service::publicArray($s);
if (!empty($showContactUnverified)) {
    $pub['phone'] = $s['phone'];
    $pub['whatsapp'] = $s['whatsapp'];
}
?>
<li class="service">
  <div class="service-title"><h3><?= e($pub['name']) ?></h3><?= $pub['verified'] ? '<span class="badge badge-ok">Terverifikasi</span>' : '<span class="badge badge-verify">Perlu verifikasi</span>' ?></div>
  <div class="service-meta">
    <span><?= icon('building') ?><?= e(Service::TYPES[$pub['type']]) ?></span>
    <span><?= icon('pin') ?><?= e($pub['area']) ?></span>
    <span><?= icon('clock') ?><?= e($pub['hours'] ?: 'Jam layanan belum dicatat') ?></span>
  </div>
  <div class="service-tags"><?php foreach ($pub['tags'] as $t): ?><span class="badge badge-plain"><?= e($t) ?></span><?php endforeach; ?></div>
  <div class="service-actions">
    <?php if ($pub['phone']): ?>
      <a class="btn btn-ink btn-xs" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $pub['phone'])) ?>"><?= icon('phone') ?> Telepon <span class="mono"><?= e($pub['phone']) ?></span></a>
    <?php else: ?>
      <span class="small muted">Nomor belum dicek tim</span>
    <?php endif; ?>
    <?php if ($pub['whatsapp']): ?>
      <a class="btn btn-ghost btn-xs" href="https://wa.me/<?= e(preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pub['whatsapp']))) ?>" rel="noopener noreferrer"><?= icon('message') ?> <span class="mono"><?= e($pub['whatsapp']) ?></span></a>
    <?php endif; ?>
  </div>
</li>
