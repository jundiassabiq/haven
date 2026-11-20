<?php
/** @var string $content  @var string|null $active  @var bool|null $noFooter */
?>
<!doctype html>
<html lang="id">
<head>
<?php include APP_PATH . '/views/partials/head.php'; ?>
</head>
<body>
<?php include APP_PATH . '/views/partials/icons.php'; ?>
<?php include APP_PATH . '/views/partials/public-header.php'; ?>
<?php include APP_PATH . '/views/partials/quick-exit.php'; ?>
<?php include APP_PATH . '/views/partials/flash.php'; ?>
<?= $content ?>
<?php if (empty($noFooter)) include APP_PATH . '/views/partials/public-footer.php'; ?>
</body>
</html>
