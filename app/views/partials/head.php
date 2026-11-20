<?php
/** @var string|null $title  @var string|null $description */
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="referrer" content="no-referrer">
<meta name="robots" content="noindex">
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
<meta name="theme-color" content="#FFC907">
<link rel="icon" href="<?= e(url('/favicon.svg')) ?>" type="image/svg+xml">
<title><?= e($title ?? 'Haven | Ruang aman untuk melapor dan pulih') ?></title>
<meta name="description" content="<?= e($description ?? 'Haven membantu penyintas kekerasan seksual melapor dengan aman, mendapat pendamping, dan menemukan layanan bantuan.') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anonymous+Pro:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/fonts.css') ?>">
<link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script src="<?= asset('js/app.js') ?>" defer></script>
