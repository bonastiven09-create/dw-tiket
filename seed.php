<?php
require_once __DIR__ . '/koneksi.php';

$sukses = false;
$pesan  = '';
$hasil  = 0;

try {
    $koneksi->exec('SET FOREIGN_KEY_CHECKS = 0');
    $koneksi->exec('TRUNCATE TABLE payments');
    $koneksi->exec('TRUNCATE TABLE bookings');
    $koneksi->exec('TRUNCATE TABLE screenings');
    $koneksi->exec('TRUNCATE TABLE cinemas');
    $koneksi->exec('TRUNCATE TABLE movies');
    $koneksi->exec('SET FOREIGN_KEY_CHECKS = 1');

    $movies = [
        ['The Last Guardian', 'Action', 132, '13+', 50000, 'Seorang penjaga kota terakhir harus menyelamatkan dunia dari ancaman bencana besar.', 'poster-guardian.jpg'],
        ['Moonlight Echo', 'Drama', 118, '13+', 45000, 'Seorang musisi muda mencoba menemukan suaranya kembali setelah kehilangan masa depannya.', 'poster-moonlight.jpg'],
        ['Skyline Runner', 'Adventure', 126, 'PA', 55000, 'Lomba lari antariksa dengan misi melintasi ruang hampa yang mematikan.', 'poster-skyline.jpg'],
        ['Horror Motel 9', 'Horror', 98, '17+', 42000, 'Kelompok teman terjebak di motel angker yang menyimpan rahasia kelam.', 'poster-motel.jpg'],
        ['The Little Comet', 'Animation', 96, 'SU', 40000, 'Petualangan lucu seorang komet kecil yang ingin menemukan rumahnya.', 'poster-comet.jpg'],
    ];

    $stmtMovie = $koneksi->prepare('INSERT INTO movies (title, genre, duration_minutes, rating, price, description, poster) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($movies as $movie) {
        $stmtMovie->execute($movie);
    }

    $cinemas = [
        ['CGV Plaza Medan', 'Medan', 'Jl. Gatot Subroto No. 88'],
        ['XXI Centre Point', 'Medan', 'Jl. H. Adam Malik No. 54'],
        ['Cinema 21 Binjai', 'Binjai', 'Jl. Sisingamangaraja No. 102'],
        ['Cinema 21 Pematangsiantar', 'Pematangsiantar', 'Jl. Merdeka No. 77'],
    ];

    $stmtCinema = $koneksi->prepare('INSERT INTO cinemas (name, city, address) VALUES (?, ?, ?)');
    foreach ($cinemas as $cinema) {
        $stmtCinema->execute($cinema);
    }

    $screenings = [
        [1, 1, 'Studio 1', '2026-10-27 13:00:00', 48, 26, 50000],
        [1, 2, 'Studio 2', '2026-10-27 15:30:00', 48, 18, 50000],
        [2, 1, 'Studio 3', '2026-10-27 17:00:00', 48, 22, 45000],
        [3, 2, 'Studio 4', '2026-10-27 19:00:00', 48, 15, 55000],
        [4, 3, 'Studio 1', '2026-10-28 13:30:00', 48, 31, 42000],
        [5, 4, 'Studio 2', '2026-10-28 16:00:00', 48, 27, 40000],
        [2, 4, 'Studio 1', '2026-10-28 18:30:00', 48, 20, 45000],
        [3, 1, 'Studio 5', '2026-10-29 20:00:00', 48, 24, 55000],
    ];

    $stmtScreening = $koneksi->prepare('INSERT INTO screenings (movie_id, cinema_id, hall, showtime, total_seats, available_seats, price) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($screenings as $screening) {
        $stmtScreening->execute($screening);
    }

    $bookings = [
        ['PG-20261027-001', 'Ayu Pratama', 'ayu@mail.com', 1, 1, 1, 2, 100000, 'QRIS', 'paid'],
        ['PG-20261027-002', 'Budi Santoso', 'budi@mail.com', 2, 1, 3, 3, 135000, 'E-Wallet', 'paid'],
        ['PG-20261027-003', 'Citra Lestari', 'citra@mail.com', 4, 3, 5, 1, 42000, 'Bank Transfer', 'pending'],
    ];

    $stmtBooking = $koneksi->prepare('INSERT INTO bookings (booking_number, customer_name, customer_email, movie_id, cinema_id, screening_id, quantity, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($bookings as $booking) {
        $stmtBooking->execute($booking);
    }

    $payments = [
        [1, 'PAY-20261027-001', 100000, 'QRIS', 'paid', '2026-10-27 12:45:00'],
        [2, 'PAY-20261027-002', 135000, 'E-Wallet', 'paid', '2026-10-27 16:50:00'],
    ];

    $stmtPayment = $koneksi->prepare('INSERT INTO payments (booking_id, payment_reference, amount, payment_method, status, paid_at) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($payments as $payment) {
        $stmtPayment->execute($payment);
    }

    $hasil = 2;
    $sukses = true;
} catch (Throwable $e) {
    $pesan = $e->getMessage();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seed Data · <?= e(APP_NAME) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="wrap seed">
  <p class="eyebrow">Pengisian data</p>
  <h1>Generate data tiket bioskop</h1>
  <?php if ($sukses): ?>
    <div class="kartu">
      <p><strong>Berhasil.</strong> Data film, jadwal, dan transaksi pembayaran sudah dibuat.</p>
      <p class="catatan">Total pembayaran berhasil dimasukkan: <?= angka($hasil) ?> transaksi.</p>
    </div>
    <p class="mt"><a class="tombol" href="index.php">Buka halaman utama</a></p>
  <?php else: ?>
    <div class="alert-gagal">
      <p><strong>Gagal memuat data.</strong></p>
      <p><?= e($pesan) ?></p>
    </div>
    <p class="mt"><a class="tombol" href="seed.php">Coba lagi</a></p>
  <?php endif; ?>
</main>
</body>
</html>
