<?php
require_once __DIR__ . '/koneksi.php';

$redirect = trim((string)($_GET['redirect'] ?? 'index.php'));

if (!empty($_SESSION['user_id'])) {
    $target = $redirect !== '' ? $redirect : 'index.php';
    header('Location: ' . $target);
    exit;
}

$judulHalaman = 'Login';
$halaman = 'login';
require __DIR__ . '/includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim((string)($_POST['identity'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($identity === '' || $password === '') {
        $error = 'Email/username dan password wajib diisi.';
    } else {
        try {
            $user = satu($koneksi, 'SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1', [$identity, $identity]);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                $target = $redirect !== '' ? $redirect : 'index.php';
                header('Location: ' . $target);
                exit;
            }

            $error = 'Email/username atau password salah.';
        } catch (Throwable $e) {
            $error = 'Login gagal karena tabel user belum dibuat. Silakan import database.sql atau refresh database.';
        }
    }
}
?>

<section class="section">
  <div class="auth-card">
    <p class="eyebrow">Masuk</p>
    <h1>Login ke PassGo</h1>
    <p class="lead">Gunakan akun Anda untuk lanjut ke proses pembelian tiket.</p>

    <?php if ($error !== ''): ?>
      <div class="alert-gagal" style="margin-bottom: 18px;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="post" action="login.php?redirect=<?= e(urlencode($redirect)) ?>">
      <div>
        <label for="identity">Email atau username</label>
        <input id="identity" name="identity" type="text" placeholder="admin@passgo.com atau admin" value="" required>
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="submit-btn">Masuk</button>
    </form>

    <p class="auth-note">Demo login: <strong>admin</strong> / <strong>admin123</strong> atau <strong>rina</strong> / <strong>rina123</strong></p>
    <p class="auth-note">Belum punya akun? <a href="register.php?redirect=<?= e(urlencode($redirect)) ?>">Daftar di sini</a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
