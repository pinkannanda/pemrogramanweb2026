<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telepon  = trim($_POST['telepon'] ?? '');
$kota     = trim($_POST['kota'] ?? '');      // Dianggap sebagai nama_event / kota
$kategori = trim($_POST['kategori'] ?? '');  // Nama kategori tiket
$jumlah   = trim($_POST['jumlah'] ?? '');

$errors = [];

if ($nama === '') {
    $errors[] = 'Nama lengkap wajib diisi.';
}

if ($email === '') {
    $errors[] = 'Email wajib diisi.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}

if ($telepon === '') {
    $errors[] = 'No. WhatsApp wajib diisi.';
} elseif (!preg_match('/^\+?[0-9\s-]{9,15}$/', $telepon)) {
    $errors[] = 'No. WhatsApp harus 9-15 digit angka.';
}

if ($kota === '') {
    $errors[] = 'Kota konser wajib dipilih.';
}

if ($kategori === '') {
    $errors[] = 'Kategori tiket wajib dipilih.';
}

if ($jumlah === '' || !is_numeric($jumlah)) {
    $errors[] = 'Jumlah tiket harus berupa angka.';
} elseif ((int) $jumlah < 1) {
    $errors[] = 'Jumlah tiket minimal 1.';
} elseif ((int) $jumlah > 4) {
    $errors[] = 'Jumlah tiket maksimal 4.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    $_SESSION['old']   = $_POST;
    header('Location: tambah.php');
    exit;
}

try {
    // 1. Ambil ID Tiket & Harga berdasarkan Kategori
    $stmtTiket = $pdo->prepare("SELECT id, harga, jadwal_id FROM tiket WHERE LOWER(kategori) LIKE LOWER(?) LIMIT 1");
    $stmtTiket->execute(['%' . $kategori . '%']);
    $dataTiket = $stmtTiket->fetch(PDO::FETCH_ASSOC);

    // Fallback jika kategori tidak ditemukan di tabel tiket
    $tiket_id  = $dataTiket['id'] ?? 1;
    $jadwal_id = $dataTiket['jadwal_id'] ?? 1;
    $harga     = $dataTiket['harga'] ?? 2200000;
    $total_harga = $harga * (int)$jumlah;
    $user_id   = $_SESSION['user_id'] ?? null;

    // 2. Insert ke tabel pesanan dengan struktur kolom PostgreSQL yang valid
    $stmt = $pdo->prepare(
        "INSERT INTO pesanan (user_id, jadwal_id, tiket_id, nama_pemesan, jumlah_tiket, total_harga, status)
         VALUES (:user_id, :jadwal_id, :tiket_id, :nama_pemesan, :jumlah_tiket, :total_harga, :status)"
    );
    $stmt->execute([
        'user_id'      => $user_id,
        'jadwal_id'    => $jadwal_id,
        'tiket_id'     => $tiket_id,
        'nama_pemesan' => $nama,
        'jumlah_tiket' => (int) $jumlah,
        'total_harga'  => $total_harga,
        'status'       => 'Pending'
    ]);

    $_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Pesanan tiket berhasil ditambahkan.'];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    die("Error Simpan Pesanan: " . $e->getMessage());
}