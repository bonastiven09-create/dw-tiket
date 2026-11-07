<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

const APP_NAME    = 'PassGo';
const APP_TAGLINE = 'Tiket Bioskop & Pembayaran';

$dbHost = 'localhost';
$dbName = 'db_dw_tiket';
$dbUser = 'root';
$dbPass = '';

try {
    $koneksi = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    $koneksi->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(120) NOT NULL,
        email VARCHAR(160) NOT NULL UNIQUE,
        username VARCHAR(80) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
    $rinaHash = password_hash('rina123', PASSWORD_DEFAULT);

    $adminExists = $koneksi->prepare("SELECT 1 FROM users WHERE username = ? LIMIT 1");
    $adminExists->execute(['admin']);
    if (!$adminExists->fetch()) {
        $stmt = $koneksi->prepare("INSERT INTO users (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin PassGo', 'admin@passgo.com', 'admin', $adminHash]);
    } else {
        $stmt = $koneksi->prepare("UPDATE users SET password_hash = ?, full_name = ?, email = ? WHERE username = ?");
        $stmt->execute([$adminHash, 'Admin PassGo', 'admin@passgo.com', 'admin']);
    }

    $rinaExists = $koneksi->prepare("SELECT 1 FROM users WHERE username = ? LIMIT 1");
    $rinaExists->execute(['rina']);
    if (!$rinaExists->fetch()) {
        $stmt = $koneksi->prepare("INSERT INTO users (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Rina Sari', 'rina@mail.com', 'rina', $rinaHash]);
    } else {
        $stmt = $koneksi->prepare("UPDATE users SET password_hash = ?, full_name = ?, email = ? WHERE username = ?");
        $stmt->execute([$rinaHash, 'Rina Sari', 'rina@mail.com', 'rina']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    die('Koneksi database gagal. Pastikan MySQL aktif dan database db_dw_tiket sudah dibuat dengan database.sql. Detail: '
        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

function e($teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function angka($nilai): string
{
    return number_format((int)$nilai, 0, ',', '.');
}

function ambil(PDO $db, string $sql, array $param = []): array
{
    $stmt = $db->prepare($sql);
    $stmt->execute($param);
    return $stmt->fetchAll();
}

function satu(PDO $db, string $sql, array $param = []): ?array
{
    $stmt = $db->prepare($sql);
    $stmt->execute($param);
    $row = $stmt->fetch();
    return $row ?: null;
}
