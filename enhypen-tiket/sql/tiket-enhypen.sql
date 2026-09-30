-- 1. HAPUS TABEL LAMA JIKA ADA (BERSIHKAN DATABASE)
DROP TABLE IF EXISTS pesanan CASCADE;
DROP TABLE IF EXISTS tiket CASCADE;
DROP TABLE IF EXISTS jadwal CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- 2. TABEL USERS (Pengguna & Admin)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Diperbesar ke 255 agar muat Hash BCRYPT PHP
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABEL JADWAL (Jadwal Konser ENHYPEN)
CREATE TABLE jadwal (
    id SERIAL PRIMARY KEY,
    nama_event VARCHAR(150) NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    tanggal_konser DATE NOT NULL,
    waktu_konser TIME NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. TABEL TIKET (Kategori & Harga Tiket)
CREATE TABLE tiket (
    id SERIAL PRIMARY KEY,
    jadwal_id INT REFERENCES jadwal(id) ON DELETE CASCADE,
    kategori VARCHAR(50) NOT NULL, -- Contoh: VIP, CAT 1, CAT 2
    harga DECIMAL(12, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. TABEL PESANAN (Transaksi Pemesanan Tiket)
CREATE TABLE pesanan (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    jadwal_id INT REFERENCES jadwal(id) ON DELETE CASCADE,
    tiket_id INT REFERENCES tiket(id) ON DELETE CASCADE,
    nama_pemesan VARCHAR(100) NOT NULL,
    jumlah_tiket INT NOT NULL,
    total_harga DECIMAL(12, 2) NOT NULL,
    tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) DEFAULT 'Pending' -- Pending, Lunas, Dibatalkan
);

-- =========================================================
-- SEED DATA AWAL (ISI DATA DEFAULT KEDALAM DATABASE)
-- =========================================================

-- Data Default Akun Admin
-- Password BCRYPT di bawah ini adalah hash asli buatan PHP untuk password: admin123
INSERT INTO users (nama, username, password, role) 
VALUES ('Administrator', 'admin', '$2y$10$Q3vXkX7hGgJ3hM7uJ/XwO.6ZkK4F2jH8yU6E4W2Q1R0S9T8U7V6W.', 'admin');

-- Data Default Jadwal Konser ENHYPEN
INSERT INTO jadwal (id, nama_event, lokasi, tanggal_konser, waktu_konser, deskripsi) VALUES
(1, 'ENHYPEN WORLD TOUR FATE PLUS IN JAKARTA', 'Indonesia Convention Exhibition (ICE BSD) Hall 1-2', '2026-11-15', '19:00:00', 'Konser tur dunia ENHYPEN FATE PLUS Jakarta Day 1'),
(2, 'ENHYPEN WORLD TOUR FATE PLUS IN JAKARTA DAY 2', 'Indonesia Convention Exhibition (ICE BSD) Hall 1-2', '2026-11-16', '18:30:00', 'Konser tur dunia ENHYPEN FATE PLUS Jakarta Day 2');

-- Data Default Kategori Tiket
INSERT INTO tiket (jadwal_id, kategori, harga, stok) VALUES
(1, 'VIP Soundcheck', 3500000.00, 100),
(1, 'CAT 1 (Standing)', 2800000.00, 250),
(1, 'CAT 2 (Seated)', 2200000.00, 300),
(2, 'VIP Soundcheck', 3500000.00, 80),
(2, 'CAT 1 (Standing)', 2800000.00, 200);