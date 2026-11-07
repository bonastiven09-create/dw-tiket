<?php
require_once __DIR__ . '/koneksi.php';

$redirect = trim((string)($_GET['redirect'] ?? 'index.php'));

if (!empty($_SESSION['user_id'])) {
    $target = $redirect !== '' ? $redirect : 'index.php';
    header('Location: ' . $target);
    exit;
}

$judulHalaman = 'Daftar';
$halaman = 'register';
require __DIR__ . '/includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $email === '' || $username === '' || $password === '') {
        $error = 'Semua field wajib diisi.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        try {
            $existing = satu($koneksi, 'SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1', [$email, $username]);
            if ($existing) {
                $error = 'Email atau username sudah dipakai.';
            } else {
                $stmt = $koneksi->prepare('INSERT INTO users (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)');
                $stmt->execute([
                    $fullName,
                    $email,
                    $username,
                    password_hash($password, PASSWORD_DEFAULT),
                ]);

                $user = satu($koneksi, 'SELECT * FROM users WHERE username = ? LIMIT 1', [$username]);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                $target = $redirect !== '' ? $redirect : 'index.php';
                header('Location: ' . $target);
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Pendaftaran gagal. Silakan coba lagi.';
        }
    }
}
?>

<section class="section">
  <div class="auth-card">
    <p class="eyebrow">Daftar akun</p>
    <h1>Buat akun baru</h1>
    <p class="lead">Sebelum masuk ke halaman web, Anda harus membuat akun atau login terlebih dahulu.</p>

    <?php if ($error !== ''): ?>
      <div class="alert-gagal" style="margin-bottom: 18px;">
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="post" action="register.php?redirect=<?= e(urlencode($redirect)) ?>">
      <div>
        <label for="full_name">Nama lengkap</label>
        <input id="full_name" name="full_name" type="text" placeholder="Masukkan nama lengkap" required>
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="nama@email.com" required>
      </div>
      <div>
        <label for="username">Username</label>
        <input id="username" name="username" type="text" placeholder="username Anda" required>
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="Minimal 6 karakter" required>
      </div>
      <div>
        <label for="confirm_password">Konfirmasi password</label>
        <input id="confirm_password" name="confirm_password" type="password" placeholder="Ulangi password" required>
      </div>
      <button type="submit" class="submit-btn">Daftar</button>
    </form>

    <p class="auth-note">Sudah punya akun? <a href="login.php?redirect=<?= e(urlencode($redirect)) ?>">Login di sini</a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
