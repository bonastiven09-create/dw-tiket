<?php
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
