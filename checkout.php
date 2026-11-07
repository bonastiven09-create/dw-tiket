<?php
require_once __DIR__ . '/koneksi.php';

if (empty($_SESSION['user_id'])) {
    $redirect = rawurlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
    header('Location: register.php?redirect=' . $redirect);
    exit;
}

$judulHalaman = 'Checkout';
$halaman = 'checkout';
require __DIR__ . '/includes/header.php';

$movieId = (int)($_GET['movie_id'] ?? $_POST['movie_id'] ?? 0);
$cinemaId = (int)($_GET['cinema_id'] ?? $_POST['cinema_id'] ?? 0);
$screeningId = (int)($_GET['screening_id'] ?? $_POST['screening_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1));
$customerName = trim((string)($_POST['customer_name'] ?? ''));
$customerEmail = trim((string)($_POST['customer_email'] ?? ''));
$paymentMethod = trim((string)($_POST['payment_method'] ?? 'QRIS'));
$selectedSeat = trim((string)($_POST['seat_number'] ?? $_GET['seat_number'] ?? ''));
$error = '';

try {
    $koneksi->query("SELECT seat_number FROM bookings LIMIT 1");
} catch (Throwable $e) {
    $koneksi->exec("ALTER TABLE bookings ADD COLUMN seat_number VARCHAR(40) NOT NULL DEFAULT 'A1'");
}

$movie = $movieId ? satu($koneksi, 'SELECT * FROM movies WHERE id = ?', [$movieId]) : null;
$cinemas = ambil($koneksi, 'SELECT * FROM cinemas ORDER BY city, name');
$screeningsForMovie = [];
$screening = null;

if ($movie) {
    $screeningsForMovie = ambil($koneksi, "SELECT s.*, m.title, m.price, c.name AS cinema_name, c.city, c.address, c.id AS cinema_id
        FROM screenings s
        JOIN movies m ON m.id = s.movie_id
        JOIN cinemas c ON c.id = s.cinema_id
        WHERE s.movie_id = ? AND s.showtime >= NOW()
        ORDER BY s.showtime ASC", [$movieId]);
}

if ($screeningId) {
    $screening = satu($koneksi, "SELECT s.*, m.title, m.price, c.name AS cinema_name, c.city, c.address, c.id AS cinema_id
        FROM screenings s
        JOIN movies m ON m.id = s.movie_id
        JOIN cinemas c ON c.id = s.cinema_id
        WHERE s.id = ?", [$screeningId]);
} elseif ($movie && $screeningsForMovie) {
    if ($cinemaId) {
        foreach ($screeningsForMovie as $row) {
            if ((int)$row['cinema_id'] === $cinemaId) {
                $screening = $row;
                break;
            }
        }
    }

    if (!$screening) {
        $screening = $screeningsForMovie[0];
    }
}

if ($cinemaId === 0 && $screening) {
    $cinemaId = (int)$screening['cinema_id'];
}

if ($movie && $screening) {
    $availableScreeningsForCinema = [];
    foreach ($screeningsForMovie as $row) {
        if ((int)$row['cinema_id'] === (int)$screening['cinema_id']) {
            $availableScreeningsForCinema[] = $row;
        }
    }
    if (!$screeningId && !empty($availableScreeningsForCinema)) {
        $screening = $availableScreeningsForCinema[0];
        $screeningId = (int)$screening['id'];
    }
}

$selectedSeatList = [];
if ($selectedSeat !== '') {
    $selectedSeatList = array_values(array_filter(array_map(function ($part) {
        return trim($part);
    }, preg_split('/\s*,\s*|\s*;\s*/', $selectedSeat)), function ($seat) {
        return $seat !== '';
    }));
}

$seatRows = ['A', 'B', 'C', 'D', 'E', 'F'];
$seatCols = range(1, 8);
$seatOptions = [];
foreach ($seatRows as $row) {
    foreach ($seatCols as $col) {
        $seatOptions[] = $row . $col;
    }
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
    } elseif ($selectedSeat === '') {
        $error = 'Silakan pilih kursi untuk pembelian tiket Anda.';
    } else {
        $seatList = array_values(array_filter(array_map(function ($part) {
            return trim($part);
        }, preg_split('/\s*,\s*|\s*;\s*/', $selectedSeat)), function ($seat) {
            return $seat !== '';
        }));

        if (count($seatList) !== $quantity) {
            $error = 'Jumlah kursi yang dipilih harus sama dengan jumlah tiket yang dibeli.';
        } else {
            try {
                $bookingNumber = 'PG-' . date('YmdHis');
                $koneksi->beginTransaction();

                $stmtBooking = $koneksi->prepare(
                    'INSERT INTO bookings (booking_number, customer_name, customer_email, movie_id, cinema_id, screening_id, seat_number, quantity, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmtBooking->execute([
                    $bookingNumber,
                    $customerName,
                    $customerEmail,
                    (int)$movie['id'],
                    (int)$screening['cinema_id'],
                    (int)$screening['id'],
                    implode(', ', $seatList),
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
      <input type="hidden" name="finalize_payment" value="1">
      <input type="hidden" name="seat_number" id="seat_number" value="<?= e(implode(', ', $selectedSeatList)) ?>">

      <div class="form-grid">
        <div>
          <label for="cinema_id">Pilih bioskop</label>
          <select id="cinema_id" name="cinema_id" required>
            <option value="">-- Pilih bioskop --</option>
            <?php foreach ($cinemas as $cinema): ?>
              <?php $hasScreening = false; foreach ($screeningsForMovie as $row) { if ((int)$row['cinema_id'] === (int)$cinema['id']) { $hasScreening = true; break; } } ?>
              <?php if ($hasScreening): ?>
                <option value="<?= (int)$cinema['id'] ?>" <?= $cinemaId === (int)$cinema['id'] ? 'selected' : '' ?>><?= e($cinema['name']) ?> · <?= e($cinema['city']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="screening_id">Pilih jadwal</label>
          <select id="screening_id" name="screening_id" required>
            <?php foreach ($screeningsForMovie as $row): ?>
              <?php if ((int)$row['cinema_id'] === $cinemaId || $cinemaId === 0): ?>
                <option value="<?= (int)$row['id'] ?>" <?= (int)$screening['id'] === (int)$row['id'] ? 'selected' : '' ?>><?= e($row['cinema_name']) ?> · <?= date('d M Y, H:i', strtotime($row['showtime'])) ?> · Studio <?= e($row['hall']) ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
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
          <label for="quantity">Jumlah tiket</label>
          <input id="quantity" name="quantity" type="number" min="1" max="10" value="<?= (int)$quantity ?>" required>
        </div>
      </div>

      <div class="seat-picker-wrap">
        <h3>Pilih kursi</h3>
        <p>Silakan pilih <?= angka($quantity) ?> kursi untuk <?= e($movie['title']) ?>.</p>

        <div class="cinema-stage">LAYAR</div>
        <div class="seat-picker" data-max-seats="<?= (int)$quantity ?>">
          <?php foreach ($seatRows as $row): ?>
            <div class="seat-row" aria-label="Baris <?= e($row) ?>">
              <span class="row-label"><?= e($row) ?></span>
              <?php foreach ($seatCols as $col): ?>
                <?php $seat = $row . $col; $isSelected = in_array($seat, $selectedSeatList, true); ?>
                <button
                  type="button"
                  class="seat-btn <?= $isSelected ? 'selected' : '' ?>"
                  data-seat="<?= e($seat) ?>"
                  aria-pressed="<?= $isSelected ? 'true' : 'false' ?>">
                  <?= e($col) ?>
                </button>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
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

<script>
  (function () {
    const form = document.querySelector('.booking-form');
    if (!form) return;

    const quantityInput = form.querySelector('input[name="quantity"]');
    const seatInput = document.getElementById('seat_number');
    const seatPicker = form.querySelector('.seat-picker');
    const seatButtons = Array.from(document.querySelectorAll('.seat-btn'));
    const selected = new Set((seatInput?.value || '').split(',').map((seat) => seat.trim()).filter(Boolean));

    const getMaxSeats = () => {
      const value = Number(quantityInput?.value || 1);
      return Math.max(1, Math.min(10, Number.isFinite(value) ? value : 1));
    };

    const updateSeatInput = () => {
      const maxSeats = getMaxSeats();
      const values = Array.from(selected);

      while (values.length > maxSeats) {
        selected.delete(values[values.length - 1]);
        values.pop();
      }

      if (seatInput) seatInput.value = Array.from(selected).join(', ');

      seatButtons.forEach((button) => {
        const seat = button.dataset.seat;
        const active = selected.has(seat);
        button.classList.toggle('selected', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.disabled = !active && selected.size >= maxSeats;
      });
    };

    seatButtons.forEach((button) => {
      button.addEventListener('click', function () {
        const seat = button.dataset.seat;
        const maxSeats = getMaxSeats();

        if (selected.has(seat)) {
          selected.delete(seat);
        } else if (selected.size < maxSeats) {
          selected.add(seat);
        }

        updateSeatInput();
      });
    });

    if (quantityInput) {
      quantityInput.addEventListener('input', function () {
        const value = Number(quantityInput.value || 1);
        quantityInput.value = String(Math.max(1, Math.min(10, Number.isFinite(value) ? value : 1)));
        updateSeatInput();
      });
    }

    if (seatInput && seatInput.value) {
      updateSeatInput();
    }
  })();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
