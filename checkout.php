<?php
$judulHalaman = 'Checkout';
$halaman = 'checkout';
require __DIR__ . '/includes/header.php';

$movieId = (int)($_GET['movie_id'] ?? $_POST['movie_id'] ?? 0);
$screeningId = (int)($_GET['screening_id'] ?? $_POST['screening_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1));
$customerName = trim((string)($_POST['customer_name'] ?? ''));
$customerEmail = trim((string)($_POST['customer_email'] ?? ''));
$paymentMethod = trim((string)($_POST['payment_method'] ?? 'QRIS'));
$error = '';

$movie = $movieId ? satu($koneksi, 'SELECT * FROM movies WHERE id = ?', [$movieId]) : null;
$screening = null;

if ($screeningId) {
    $screening = satu($koneksi, "SELECT s.*, m.title, m.price, c.name AS cinema_name, c.city, c.address, c.id AS cinema_id
        FROM screenings s
        JOIN movies m ON m.id = s.movie_id
        JOIN cinemas c ON c.id = s.cinema_id
        WHERE s.id = ?", [$screeningId]);
} elseif ($movieId) {
    $screening = satu($koneksi, "SELECT s.*, m.title, m.price, c.name AS cinema_name, c.city, c.address, c.id AS cinema_id
        FROM screenings s
        JOIN movies m ON m.id = s.movie_id
        JOIN cinemas c ON c.id = s.cinema_id
        WHERE s.movie_id = ? AND s.showtime >= NOW()
        ORDER BY s.showtime ASC
        LIMIT 1", [$movieId]);
}

if (!$movie) {
    $error = 'Film yang dipilih tidak ditemukan.';
} elseif (!$screening) {
    $error = 'Jadwal tayang untuk film ini belum tersedia.';
}

$total = 0;
if ($movie && $screening) {
    $unitPrice = (int)($screening['price'] ?: $movie['price']);
    $total = $unitPrice * $quantity;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize_payment'])) {
    if (!$movie || !$screening) {
        $error = 'Data pemesanan tidak lengkap.';
    } elseif ($customerName === '' || $customerEmail === '') {
        $error = 'Nama dan email pembeli wajib diisi.';
    } else {
        try {
            $bookingNumber = 'PG-' . date('YmdHis');
            $koneksi->beginTransaction();

            $stmtBooking = $koneksi->prepare(
                'INSERT INTO bookings (booking_number, customer_name, customer_email, movie_id, cinema_id, screening_id, quantity, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmtBooking->execute([
                $bookingNumber,
                $customerName,
                $customerEmail,
                (int)$movie['id'],
                (int)$screening['cinema_id'],
                (int)$screening['id'],
                $quantity,
                $total,
                $paymentMethod,
                'paid'
            ]);

            $bookingId = (int)$koneksi->lastInsertId();
            $paymentReference = 'PAY-' . date('YmdHis');

            $stmtPayment = $koneksi->prepare(
                'INSERT INTO payments (booking_id, payment_reference, amount, payment_method, status, paid_at) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmtPayment->execute([
                $bookingId,
                $paymentReference,
                $total,
                $paymentMethod,
                'paid',
                date('Y-m-d H:i:s')
            ]);

            $koneksi->commit();
            header('Location: success.php?booking=' . urlencode($bookingNumber));
            exit;
        } catch (Throwable $e) {
            if ($koneksi->inTransaction()) {
                $koneksi->rollBack();
            }
            $error = 'Pembayaran gagal diproses: ' . $e->getMessage();
        }
    }
}
?>

<?php if ($error): ?>
  <section class="section">
    <div class="alert-gagal">
      <strong>Oops.</strong> <?= e($error) ?>
    </div>
  </section>
<?php endif; ?>

<?php if ($movie && $screening): ?>
  <section class="hero">
    <p class="eyebrow">Checkout tiket</p>
    <h1><?= e($movie['title']) ?></h1>
    <p class="lead">Review pesanan Anda, lalu lanjutkan pembayaran untuk mendapatkan tiket.</p>
  </section>

  <section class="section">
    <div class="kolom dua">
      <article class="kartu">
        <h3>Detail film</h3>
        <p><strong>Judul:</strong> <?= e($movie['title']) ?></p>
        <p><strong>Genre:</strong> <?= e($movie['genre']) ?></p>
        <p><strong>Jam tayang:</strong> <?= date('d M Y, H:i', strtotime($screening['showtime'])) ?></p>
        <p><strong>Lokasi:</strong> <?= e($screening['cinema_name']) ?> · <?= e($screening['city']) ?></p>
        <p><strong>Studio:</strong> <?= e($screening['hall']) ?></p>
        <p><strong>Harga per tiket:</strong> <?= rupiah($screening['price'] ?: $movie['price']) ?></p>
      </article>

      <article class="kartu">
        <h3>Ringkasan pembayaran</h3>
        <p><strong>Jumlah tiket:</strong> <?= angka($quantity) ?></p>
        <p><strong>Subtotal:</strong> <?= rupiah($total) ?></p>
        <p><strong>Biaya layanan:</strong> Rp 0</p>
        <p><strong>Total akhir:</strong> <span style="font-size: 1.4rem; font-weight: 700; color: var(--brand-dark);"><?= rupiah($total) ?></span></p>
      </article>
    </div>
  </section>

  <section class="section">
    <form class="booking-form" method="post" action="checkout.php">
      <input type="hidden" name="movie_id" value="<?= (int)$movie['id'] ?>">
      <input type="hidden" name="screening_id" value="<?= (int)$screening['id'] ?>">
      <input type="hidden" name="quantity" value="<?= (int)$quantity ?>">
      <input type="hidden" name="finalize_payment" value="1">

      <div class="form-grid">
        <div>
          <label for="customer_name">Nama pembeli</label>
          <input id="customer_name" name="customer_name" type="text" value="<?= e($customerName) ?>" required>
        </div>
        <div>
          <label for="customer_email">Email</label>
          <input id="customer_email" name="customer_email" type="email" value="<?= e($customerEmail) ?>" required>
        </div>
        <div>
          <label for="payment_method">Metode pembayaran</label>
          <select id="payment_method" name="payment_method">
            <option value="QRIS" <?= $paymentMethod === 'QRIS' ? 'selected' : '' ?>>QRIS</option>
            <option value="E-Wallet" <?= $paymentMethod === 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
            <option value="Bank Transfer" <?= $paymentMethod === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
            <option value="Kartu Kredit" <?= $paymentMethod === 'Kartu Kredit' ? 'selected' : '' ?>>Kartu Kredit</option>
          </select>
        </div>
        <div>
          <label for="quantity_display">Jumlah tiket</label>
          <input id="quantity_display" type="text" value="<?= angka($quantity) ?> tiket" readonly>
        </div>
      </div>

      <button type="submit" class="submit-btn">Bayar sekarang</button>
    </form>
  </section>
<?php else: ?>
  <section class="section">
    <p><a class="tombol" href="index.php">Kembali ke beranda</a></p>
  </section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
