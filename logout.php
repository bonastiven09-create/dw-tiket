<?php
require __DIR__ . '/koneksi.php';

$redirect = trim((string)($_GET['redirect'] ?? 'login.php'));

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

$target = $redirect !== '' ? $redirect : 'login.php';
header('Location: ' . $target);
exit;
