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
  <script>
    (function () {
      const savedTheme = localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const isDark = savedTheme ? savedTheme === 'dark' : prefersDark;
      document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    })();
  </script>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="topbar-in wrap">
    <a class="brand" href="index.php">
      <img src="assets/logo.svg" width="36" height="36" alt="Logo <?= e(APP_NAME) ?>">
      <span><?= e(APP_NAME) ?><small>Cinema Ticketing</small></span>
    </a>
    <div class="mascot-wrap" aria-label="Mascot PassGo">
      <div class="mascot-scene">
        <div class="pixel-mascot mascot" aria-hidden="true" data-mood="idle">
          <span class="pixel-shadow"></span>
          <span class="pixel-arm pixel-arm-left"></span>
          <span class="pixel-arm pixel-arm-right"></span>
          <span class="pixel-body"></span>
          <span class="pixel-head">
            <span class="pixel-face"></span>
            <span class="pixel-brow pixel-brow-left"></span>
            <span class="pixel-brow pixel-brow-right"></span>
            <span class="pixel-eye pixel-eye-left"></span>
            <span class="pixel-eye pixel-eye-right"></span>
            <span class="pixel-mouth"></span>
          </span>
          <span class="pixel-leg pixel-leg-left"></span>
          <span class="pixel-leg pixel-leg-right"></span>
        </div>
        <div class="mascot-bubble" aria-live="polite">Halo! Selamat datang di PassGo ✨</div>
      </div>
    </div>
    <div class="header-actions">
      <nav>
        <a href="index.php" class="<?= $halaman === 'beranda' ? 'aktif' : '' ?>">Beranda</a>
        <a href="laporan.php" class="<?= $halaman === 'laporan' ? 'aktif' : '' ?>">Laporan</a>
        <a href="tentang.php" class="<?= $halaman === 'tentang' ? 'aktif' : '' ?>">Tentang</a>
      </nav>
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Ganti tema" aria-pressed="false" title="Ganti tema">
        <span class="theme-icon theme-icon-sun" aria-hidden="true">☀</span>
        <span class="theme-icon theme-icon-moon" aria-hidden="true">☾</span>
      </button>
      <?php if (!empty($_SESSION['user_name'])): ?>
        <div class="account-menu">
          <button type="button" class="account-button" aria-expanded="false">
            <span class="account-avatar"><?= e(substr($_SESSION['user_name'], 0, 1)) ?></span>
            <span class="account-label">Halo, <?= e($_SESSION['user_name']) ?></span>
            <span class="caret">▾</span>
          </button>
          <div class="account-dropdown">
            <form class="dropdown-form" action="logout.php" method="post">
              <input type="hidden" name="redirect" value="login.php">
              <button type="submit" class="dropdown-link" data-confirm="Apakah Anda ingin mengganti akun?">Ganti akun</button>
            </form>
            <form class="dropdown-form" action="logout.php" method="post">
              <button type="submit" class="dropdown-link danger-link" data-confirm="Apakah Anda yakin ingin keluar dari akun ini?">Keluar</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <div class="auth-links">
          <a class="login-link" href="login.php">Login</a>
          <a class="login-link" href="register.php">Daftar</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="confirm-modal" id="confirmModal" hidden>
  <div class="confirm-modal-card">
    <h3>Konfirmasi</h3>
    <p id="confirmMessage">Apakah Anda yakin?</p>
    <div class="confirm-actions">
      <button type="button" class="secondary-btn" id="cancelConfirm">Batal</button>
      <button type="button" class="submit-btn" id="confirmAction">Ya, lanjut</button>
    </div>
  </div>
</div>

<main class="wrap">
