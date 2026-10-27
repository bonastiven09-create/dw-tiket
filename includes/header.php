<?php
require_once __DIR__ . '/../koneksi.php';
$judulHalaman = $judulHalaman ?? APP_NAME;
$halaman      = $halaman ?? '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judulHalaman) ?> · <?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="topbar-in wrap">
    <a class="brand" href="index.php">
      <img src="assets/logo.svg" width="36" height="36" alt="Logo <?= e(APP_NAME) ?>">
      <span><?= e(APP_NAME) ?><small>Cinema Ticketing</small></span>
    </a>
    <nav>
      <a href="index.php" class="<?= $halaman === 'beranda' ? 'aktif' : '' ?>">Beranda</a>
      <a href="laporan.php" class="<?= $halaman === 'laporan' ? 'aktif' : '' ?>">Laporan</a>
      <a href="tentang.php" class="<?= $halaman === 'tentang' ? 'aktif' : '' ?>">Tentang</a>
    </nav>
  </div>
</header>
<main class="wrap">
