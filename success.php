<?php
require_once __DIR__ . '/koneksi.php';

if (empty($_SESSION['user_id'])) {
    $redirect = rawurlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
    header('Location: register.php?redirect=' . $redirect);
    exit;
}

$judulHalaman = 'Pembayaran Berhasil';
require __DIR__ . '/includes/header.php';

$bookingNumber = trim((string)($_GET['booking'] ?? ''));
$booking = $bookingNumber ? satu($koneksi, "SELECT b.*, m.title, c.name AS cinema_name, s.hall, s.showtime
    FROM bookings b
    JOIN movies m ON m.id = b.movie_id
    JOIN cinemas c ON c.id = b.cinema_id
    JOIN screenings s ON s.id = b.screening_id
    WHERE b.booking_number = ?", [$bookingNumber]) : null;

if (!$booking) {
    header('Location: index.php');
    exit;
}
?>

<section class="section">
  <div class="success-card">
    <p class="eyebrow">Pembayaran berhasil</p>
    <h1>Terima kasih, <?= e($booking['customer_name']) ?>!</h1>
    <p class="lead">Tiket Anda sudah berhasil dibayar dan siap digunakan.</p>
    <div class="detail">
      <div>
        <strong>Nomor pemesanan</strong><br>
        <?= e($booking['booking_number']) ?>
      </div>
      <div>
        <strong>Film</strong><br>
        <?= e($booking['title']) ?>
      </div>
      <div>
        <strong>Jadwal</strong><br>
        <?= date('d M Y, H:i', strtotime($booking['showtime'])) ?>
      </div>
      <div>
        <strong>Studio</strong><br>
        <?= e($booking['cinema_name']) ?> · <?= e($booking['hall']) ?>
      </div>
      <div>
        <strong>Jumlah tiket</strong><br>
        <?= angka((int)$booking['quantity']) ?> tiket
      </div>
      <div>
        <strong>Kursi pilihan</strong><br>
        <?= e($booking['seat_number'] ?? 'A1') ?>
      </div>
      <div>
        <strong>Total bayar</strong><br>
        <?= rupiah((float)$booking['total_amount']) ?>
      </div>
    </div>
    <p><a class="tombol" href="index.php">Kembali ke beranda</a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
