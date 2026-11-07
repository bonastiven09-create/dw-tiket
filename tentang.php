<?php
require_once __DIR__ . '/koneksi.php';

if (empty($_SESSION['user_id'])) {
    $redirect = rawurlencode($_SERVER['REQUEST_URI'] ?? 'tentang.php');
    header('Location: register.php?redirect=' . $redirect);
    exit;
}

$judulHalaman = 'Tentang';
$halaman      = 'tentang';
require __DIR__ . '/includes/header.php';
?>

<section class="hero identitas">
  <img src="assets/logo.svg" width="88" height="88" alt="Logo <?= e(APP_NAME) ?>">
  <div>
    <p class="eyebrow">Identitas brand</p>
    <h1><?= e(APP_NAME) ?></h1>
    <p class="lead">PassGo adalah platform pemesanan tiket bioskop yang dirancang untuk membuat proses booking lebih cepat, rapi, dan menyenangkan di setiap langkahnya.</p>
  </div>
</section>

<section class="section">
  <h2>Latar belakang</h2>
  <div class="kolom dua">
    <article class="kartu">
      <p>PassGo lahir dari kebutuhan untuk menyederhanakan pengalaman menonton film. Banyak pengguna sering dibuat ribet karena harus membandingkan bioskop, jadwal, dan kursi secara terpisah. Platform ini dibuat agar semua proses bisa dilakukan dalam satu tempat dengan cara yang lebih terstruktur.</p>
    </article>
    <article class="kartu">
      <p>Dalam satu pengalaman, pengguna bisa memilih film, menentukan lokasi bioskop, melihat jadwal tayang, memilih jumlah tiket, menentukan kursi, dan menyelesaikan pembayaran. Tujuannya bukan hanya memudahkan transaksi, tetapi juga membuat pengalaman menonton menjadi lebih nyaman dan efisien.</p>
    </article>
  </div>
</section>

<section class="section">
  <h2>Bioskop yang menggunakan layanan ini</h2>
  <div class="kolom">
    <article class="kartu">
      <h3>Medan</h3>
      <p><strong>CGV Plaza Medan</strong> · Jl. Gatot Subroto No. 88</p>
      <p><strong>XXI Centre Point</strong> · Jl. H. Adam Malik No. 54</p>
    </article>
    <article class="kartu">
      <h3>Binjai</h3>
      <p><strong>Cinema 21 Binjai</strong> · Jl. Sisingamangaraja No. 102</p>
    </article>
    <article class="kartu">
      <h3>Pematangsiantar</h3>
      <p><strong>Cinema 21 Pematangsiantar</strong> · Jl. Merdeka No. 77</p>
    </article>
  </div>
  <p class="catatan">PassGo didesain untuk mewakili pengalaman pemesanan tiket bioskop di beberapa kota utama, terutama di wilayah Sumatera Utara, dengan standar layanan yang konsisten dan mudah dikelola.</p>
</section>

<section class="section">
  <h2>Fitur yang tersedia</h2>
  <div class="kolom tiga">
    <article class="kartu">
      <h3>Login & registrasi</h3>
      <p>Pengguna harus memiliki akun sebelum masuk ke halaman booking. Proses login dan logout sudah tersedia agar data pengguna tetap aman dan terorganisir.</p>
    </article>
    <article class="kartu">
      <h3>Pilih bioskop & jadwal</h3>
      <p>Setiap pengguna dapat memilih lokasi bioskop dan jam tayang yang tersedia sebelum melanjutkan ke proses pemesanan.</p>
    </article>
    <article class="kartu">
      <h3>Jumlah tiket fleksibel</h3>
      <p>Transaksi tidak dibatasi satu tiket saja. Pengguna bisa memilih berapa banyak kursi yang dibutuhkan sesuai kebutuhan individu maupun rombongan.</p>
    </article>
    <article class="kartu">
      <h3>Pilihan kursi interaktif</h3>
      <p>Sistem menampilkan tata letak seat secara visual dan memungkinkan pengguna memilih kursi sesuai yang tersedia dengan lebih mudah.</p>
    </article>
    <article class="kartu">
      <h3>Checkout dan pembayaran</h3>
      <p>Proses pembayaran, data pembeli, dan ringkasan transaksi dibuat dalam satu flow yang jelas untuk meminimalkan kebingungan.</p>
    </article>
    <article class="kartu">
      <h3>Laporan penjualan</h3>
      <p>Halaman laporan membantu menampilkan data transaksi secara ringkas, termasuk jumlah tiket, pendapatan, dan ringkasan aktivitas.</p>
    </article>
    <article class="kartu">
      <h3>Dark mode</h3>
      <p>Pengguna bisa berpindah antar tema terang dan gelap dengan satu tombol, yang tetap tersimpan di browser untuk pengalaman yang lebih nyaman.</p>
    </article>
    <article class="kartu">
      <h3>Animasi & UX</h3>
      <p>Transisi antar halaman, hover state, dan interaksi kecil dibuat agar tampilan terasa lebih hidup, halus, dan modern.</p>
    </article>
    <article class="kartu">
      <h3>Mascot interaktif</h3>
      <p>Maskot PassGo hadir sebagai karakter pendamping dengan ekspresi dan sambutan awal untuk membuat pengalaman pengguna terasa lebih personal.</p>
    </article>
  </div>
</section>

<section class="section">
  <h2>Visi dan misi</h2>
  <div class="kolom dua">
    <article class="kartu">
      <h3>Visi</h3>
      <p>Menjadi solusi booking tiket bioskop yang modern, mudah dipakai, dan relevan bagi kebutuhan masyarakat digital saat ini.</p>
    </article>
    <article class="kartu">
      <h3>Misi</h3>
      <p>Menyediakan pengalaman pemesanan yang cepat, konsisten, dan menyenangkan dari saat memilih film hingga menyelesaikan pembayaran.</p>
    </article>
  </div>
</section>

<section class="section">
  <h2>Kenapa PassGo?</h2>
  <div class="kolom dua">
    <article class="kartu">
      <h3>Praktis</h3>
      <p>Semua kebutuhan booking tiket dapat dilakukan dalam satu platform tanpa perlu berpindah-pindah sistem.</p>
    </article>
    <article class="kartu">
      <h3>Nyaman</h3>
      <p>Desain yang bersih, responsif, dan mudah dibaca membuat pengguna lebih nyaman saat menjalankan proses pemesanan.</p>
    </article>
  </div>
</section>

<section class="section">
  <h2>Durasi pembuatan</h2>
  <div class="kolom dua">
    <article class="kartu">
      <h3>Timeline proyek</h3>
      <p>Proyek ini mulai dibangun dalam beberapa tahap: perancangan struktur fitur, pengembangan sistem login dan booking, hingga penyempurnaan UI/UX serta animasi dan branding.</p>
    </article>
    <article class="kartu">
      <h3>Estimasi waktu</h3>
      <p>Secara keseluruhan, proses pembuatan web ini berlangsung dalam waktu sekitar <strong>2 sampai 3 minggu</strong>, tergantung pada iterasi desain, revisi UI, dan pengujian fitur yang dilakukan selama pengembangan.</p>
    </article>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
