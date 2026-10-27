-- =====================================================
-- PassGo Cinema Ticketing System
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_dw_tiket
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_dw_tiket;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS screenings;
DROP TABLE IF EXISTS cinemas;
DROP TABLE IF EXISTS movies;

CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  genre VARCHAR(50) NOT NULL,
  duration_minutes INT NOT NULL,
  rating VARCHAR(10) NOT NULL,
  price INT NOT NULL,
  description TEXT NOT NULL,
  poster VARCHAR(255) DEFAULT 'poster-default.jpg'
);

CREATE TABLE cinemas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  city VARCHAR(80) NOT NULL,
  address VARCHAR(255) NOT NULL
);

CREATE TABLE screenings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  cinema_id INT NOT NULL,
  hall VARCHAR(40) NOT NULL,
  showtime DATETIME NOT NULL,
  total_seats INT NOT NULL DEFAULT 48,
  available_seats INT NOT NULL,
  price INT NOT NULL,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE CASCADE
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_number VARCHAR(30) NOT NULL UNIQUE,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(160) NOT NULL,
  movie_id INT NOT NULL,
  cinema_id INT NOT NULL,
  screening_id INT NOT NULL,
  quantity INT NOT NULL,
  total_amount INT NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  status ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (movie_id) REFERENCES movies(id),
  FOREIGN KEY (cinema_id) REFERENCES cinemas(id),
  FOREIGN KEY (screening_id) REFERENCES screenings(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  payment_reference VARCHAR(40) NOT NULL UNIQUE,
  amount INT NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  status ENUM('paid','failed','pending') NOT NULL DEFAULT 'paid',
  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

INSERT INTO movies (title, genre, duration_minutes, rating, price, description, poster) VALUES
('The Last Guardian', 'Action', 132, '13+', 50000, 'Seorang penjaga kota terakhir harus menyelamatkan dunia dari ancaman bencana besar.', 'poster-guardian.jpg'),
('Moonlight Echo', 'Drama', 118, '13+', 45000, 'Seorang musisi muda mencoba menemukan suaranya kembali setelah kehilangan masa depannya.', 'poster-moonlight.jpg'),
('Skyline Runner', 'Adventure', 126, 'PA', 55000, 'Lomba lari antariksa dengan misi melintasi ruang hampa yang mematikan.', 'poster-skyline.jpg'),
('Horror Motel 9', 'Horror', 98, '17+', 42000, 'Kelompok teman terjebak di motel angker yang menyimpan rahasia kelam.', 'poster-motel.jpg'),
('The Little Comet', 'Animation', 96, 'SU', 40000, 'Petualangan lucu seorang komet kecil yang ingin menemukan rumahnya.', 'poster-comet.jpg');

INSERT INTO cinemas (name, city, address) VALUES
('CGV Plaza Medan', 'Medan', 'Jl. Gatot Subroto No. 88'),
('XXI Centre Point', 'Medan', 'Jl. H. Adam Malik No. 54'),
('Cinema 21 Binjai', 'Binjai', 'Jl. Sisingamangaraja No. 102'),
('Cinema 21 Pematangsiantar', 'Pematangsiantar', 'Jl. Merdeka No. 77');

INSERT INTO screenings (movie_id, cinema_id, hall, showtime, total_seats, available_seats, price) VALUES
(1, 1, 'Studio 1', '2026-10-27 13:00:00', 48, 26, 50000),
(1, 2, 'Studio 2', '2026-10-27 15:30:00', 48, 18, 50000),
(2, 1, 'Studio 3', '2026-10-27 17:00:00', 48, 22, 45000),
(3, 2, 'Studio 4', '2026-10-27 19:00:00', 48, 15, 55000),
(4, 3, 'Studio 1', '2026-10-28 13:30:00', 48, 31, 42000),
(5, 4, 'Studio 2', '2026-10-28 16:00:00', 48, 27, 40000),
(2, 4, 'Studio 1', '2026-10-28 18:30:00', 48, 20, 45000),
(3, 1, 'Studio 5', '2026-10-29 20:00:00', 48, 24, 55000);

INSERT INTO bookings (booking_number, customer_name, customer_email, movie_id, cinema_id, screening_id, quantity, total_amount, payment_method, status) VALUES
('PG-20261027-001', 'Ayu Pratama', 'ayu@mail.com', 1, 1, 1, 2, 100000, 'QRIS', 'paid'),
('PG-20261027-002', 'Budi Santoso', 'budi@mail.com', 2, 1, 3, 3, 135000, 'E-Wallet', 'paid'),
('PG-20261027-003', 'Citra Lestari', 'citra@mail.com', 4, 3, 5, 1, 42000, 'Bank Transfer', 'pending');

INSERT INTO payments (booking_id, payment_reference, amount, payment_method, status, paid_at) VALUES
(1, 'PAY-20261027-001', 100000, 'QRIS', 'paid', '2026-10-27 12:45:00'),
(2, 'PAY-20261027-002', 135000, 'E-Wallet', 'paid', '2026-10-27 16:50:00');
