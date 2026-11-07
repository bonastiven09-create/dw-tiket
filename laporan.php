<?php
require_once __DIR__ . '/koneksi.php';

if (empty($_SESSION['user_id'])) {
    $redirect = rawurlencode($_SERVER['REQUEST_URI'] ?? 'laporan.php');
    header('Location: register.php?redirect=' . $redirect);
    exit;
}

$judulHalaman = 'Laporan';
$halaman      = 'laporan';
require __DIR__ . '/includes/header.php';

$summary = satu($koneksi, "SELECT COUNT(*) AS total_booking,
        COALESCE(SUM(CASE WHEN status = 'paid' THEN quantity ELSE 0 END), 0) AS tiket_terjual,
        COALESCE(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END), 0) AS pendapatan
    FROM bookings");

$topFilm = ambil($koneksi, "SELECT m.title, SUM(b.quantity) AS tickets, SUM(b.total_amount) AS revenue
    FROM bookings b
    JOIN movies m ON m.id = b.movie_id
    WHERE b.status = 'paid'
    GROUP BY m.id, m.title
    ORDER BY revenue DESC, tickets DESC
    LIMIT 5");

$byPayment = ambil($koneksi, "SELECT b.payment_method, COUNT(*) AS transaksi, SUM(b.total_amount) AS pendapatan
    FROM bookings b
    WHERE b.status = 'paid'
    GROUP BY b.payment_method
    ORDER BY pendapatan DESC");

$lastBookings = ambil($koneksi, "SELECT b.booking_number, b.customer_name, m.title, c.name AS cinema_name, s.hall,
        b.quantity, b.total_amount, b.payment_method, b.status, b.created_at
    FROM bookings b
    JOIN movies m ON m.id = b.movie_id
    JOIN cinemas c ON c.id = b.cinema_id
    JOIN screenings s ON s.id = b.screening_id
    ORDER BY b.created_at DESC
    LIMIT 10");
?>

<section class="hero">
  <p class="eyebrow">Laporan penjualan</p>
  <h1>Analisis tiket yang sudah terjual</h1>
  <p class="lead">Semua angka berasal dari data pemesanan dan pembayaran tiket bioskop yang masuk melalui sistem PassGo.</p>
</section>

<section class="section">
  <div class="grid-kpi">
    <div class="kpi">
      <p class="label">Transaksi</p>
      <p class="nilai"><?= angka((int)($summary['total_booking'] ?? 0)) ?></p>
      <p class="sub">Semua pesanan masuk</p>
    </div>
    <div class="kpi">
      <p class="label">Tiket terjual</p>
      <p class="nilai"><?= angka((int)($summary['tiket_terjual'] ?? 0)) ?></p>
      <p class="sub">Hanya yang sudah dibayar</p>
    </div>
    <div class="kpi">
      <p class="label">Pendapatan</p>
      <p class="nilai"><?= rupiah((float)($summary['pendapatan'] ?? 0)) ?></p>
      <p class="sub">Total hasil pembayaran</p>
    </div>
    <div class="kpi">
      <p class="label">Metode unggulan</p>
      <p class="nilai"><?= e(($byPayment[0]['payment_method'] ?? 'QRIS')) ?></p>
      <p class="sub">Pembayaran paling banyak dipakai</p>
    </div>
  </div>
</section>

<section class="section">
  <h2>Film paling laku</h2>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Film</th>
          <th class="num">Tiket</th>
          <th class="num">Pendapatan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topFilm as $film): ?>
          <tr>
            <td><?= e($film['title']) ?></td>
            <td class="num"><?= angka((int)$film['tickets']) ?></td>
            <td class="num"><?= rupiah((float)$film['revenue']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="section">
  <h2>Metode pembayaran</h2>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Metode</th>
          <th class="num">Transaksi</th>
          <th class="num">Pendapatan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($byPayment as $row): ?>
          <tr>
            <td><?= e($row['payment_method']) ?></td>
            <td class="num"><?= angka((int)$row['transaksi']) ?></td>
            <td class="num"><?= rupiah((float)$row['pendapatan']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="section">
  <h2>Transaksi terbaru</h2>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Pelanggan</th>
          <th>Film</th>
          <th>Lokasi</th>
          <th class="num">Tiket</th>
          <th class="num">Total</th>
          <th>Metode</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lastBookings as $row): ?>
          <tr>
            <td><?= e($row['booking_number']) ?></td>
            <td><?= e($row['customer_name']) ?></td>
            <td><?= e($row['title']) ?></td>
            <td><?= e($row['cinema_name']) ?> · <?= e($row['hall']) ?></td>
            <td class="num"><?= angka((int)$row['quantity']) ?></td>
            <td class="num"><?= rupiah((float)$row['total_amount']) ?></td>
            <td><?= e($row['payment_method']) ?></td>
            <td><?= e($row['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
