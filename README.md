# Data Warehouse PassGo: Penjualan Tiket

Proyek web PHP + MySQL untuk tugas mata kuliah Data Warehouse. Data disusun dalam skema bintang (star schema): satu tabel fakta `fact_penjualan` dan empat tabel dimensi (`dim_waktu`, `dim_film`, `dim_bioskop`, `dim_pelanggan`).

## Cara menjalankan (XAMPP)

1. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
2. Salin folder `dw-tiket` ke `C:\xampp\htdocs\` sehingga menjadi `C:\xampp\htdocs\dw-tiket\`.
3. Buka `http://localhost/phpmyadmin`, pilih tab **SQL**, lalu tempel isi `database.sql` dan klik **Kirim**.
4. Buka `http://localhost/dw-tiket/seed.php` untuk mengisi tabel fakta dengan 1.200 transaksi contoh. Halaman ini menampilkan pesan berhasil.
5. Buka `http://localhost/dw-tiket/` untuk halaman utama.

Jika tampil pesan "Koneksi database gagal", pastikan MySQL aktif. Jika password root MySQL di komputermu tidak kosong, ubah nilai `$dbPass` di `koneksi.php`.

## Struktur file

| File | Fungsi |
| --- | --- |
| `index.php` | Beranda: latar belakang, ringkasan KPI, grafik pendapatan per bulan, dan film terlaris |
| `laporan.php` | Laporan analisis per bioskop, genre, segmen, bulan, dan metode bayar, dengan filter tahun |
| `tentang.php` | Identitas brand, latar belakang, visi misi, palet warna, tipografi, dan arsitektur data warehouse |
| `seed.php` | Mengisi dimensi waktu dan tabel fakta dengan data contoh yang selalu sama setiap dijalankan |
| `database.sql` | Membuat database, tabel, dan isi dimensi |
| `koneksi.php` | Koneksi PDO dan fungsi bantu (`e`, `rupiah`, `angka`, `ambil`) |
| `includes/` | Header dan footer yang dipakai bersama |
| `assets/` | Stylesheet dan logo SVG |

## Mengganti nama brand atau bioskop

- Nama brand: ubah konstanta `APP_NAME` di `koneksi.php`.
- Nama bioskop: ubah isi `INSERT INTO dim_bioskop` di `database.sql`, lalu jalankan ulang `database.sql` dan `seed.php`.

Semua data di situs ini adalah data contoh hasil simulasi, bukan transaksi nyata.
