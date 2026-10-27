<?php
$judulHalaman = 'Beranda';
$halaman      = 'beranda';
require __DIR__ . '/includes/header.php';

$movies = ambil($koneksi, 'SELECT * FROM movies ORDER BY id');
$screenings = ambil($koneksi, "SELECT s.*, m.title, m.genre, m.duration_minutes, c.name AS cinema_name, c.city
    FROM screenings s
    JOIN movies m ON m.id = s.movie_id
    JOIN cinemas c ON c.id = s.cinema_id
    WHERE s.showtime >= NOW()
    ORDER BY s.showtime ASC
    LIMIT 8");
?>

<section class="hero">
  <p class="eyebrow">PassGo Cinema</p>
  <h1>Pilih film favorit Anda dan selesai dalam hitungan menit.</h1>
  <p class="lead">Pesan tiket bioskop online dengan jadwal yang jelas, kursi yang tersedia, dan proses pembayaran yang aman.</p>
</section>

<section class="section">
  <h2>Film yang sedang tayang</h2>
  <div class="movie-grid">
    <?php foreach ($movies as $movie): ?>
      <article class="movie-card">
        <div class="poster-box">
          <span><?= e($movie['rating']) ?></span>
        </div>
        <div class="movie-info">
          <div class="meta-row">
            <span><?= e($movie['genre']) ?></span>
            <span><?= (int)$movie['duration_minutes'] ?> menit</span>
          </div>
          <h3><?= e($movie['title']) ?></h3>
          <p><?= e($movie['description']) ?></p>
          <div class="movie-footer">
            <strong><?= rupiah($movie['price']) ?></strong>
            <a href="checkout.php?movie_id=<?= (int)$movie['id'] ?>">Beli tiket</a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <h2>Jadwal tayang</h2>
  <div class="schedule-list">
    <?php foreach ($screenings as $screening): ?>
      <div class="schedule-item">
        <div>
          <strong><?= e($screening['title']) ?></strong>
          <p><?= e($screening['cinema_name']) ?> · <?= e($screening['city']) ?> · <?= e($screening['hall']) ?></p>
        </div>
        <div class="schedule-meta">
          <span><?= date('d M Y', strtotime($screening['showtime'])) ?></span>
          <span><?= date('H:i', strtotime($screening['showtime'])) ?></span>
          <span><?= angka($screening['available_seats']) ?> kursi</span>
          <a href="checkout.php?movie_id=<?= (int)$screening['movie_id'] ?>&screening_id=<?= (int)$screening['id'] ?>">Pesan</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <h2>Pesan tiket cepat</h2>
  <form class="booking-form" method="post" action="checkout.php">
    <div class="form-grid">
      <div>
        <label for="movie_id">Film</label>
        <select id="movie_id" name="movie_id" required>
          <?php foreach ($movies as $movie): ?>
            <option value="<?= (int)$movie['id'] ?>"><?= e($movie['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="quantity">Jumlah tiket</label>
        <input id="quantity" name="quantity" type="number" min="1" max="10" value="2" required>
      </div>
      <div>
        <label for="payment_method">Metode pembayaran</label>
        <select id="payment_method" name="payment_method">
          <option value="QRIS">QRIS</option>
          <option value="E-Wallet">E-Wallet</option>
          <option value="Bank Transfer">Bank Transfer</option>
          <option value="Kartu Kredit">Kartu Kredit</option>
        </select>
      </div>
      <div>
        <label for="customer_name">Nama</label>
        <input id="customer_name" name="customer_name" type="text" placeholder="Nama pembeli" required>
      </div>
      <div class="wide">
        <label for="customer_email">Email</label>
        <input id="customer_email" name="customer_email" type="email" placeholder="nama@email.com" required>
      </div>
    </div>
    <button type="submit" class="submit-btn">Lanjut ke pembayaran</button>
  </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
