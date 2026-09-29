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
$kota     = trim($_POST['kota'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
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

// Catatan: kolom email diberi UNIQUE di sql/01_jadwal_pesanan.sql.
// Kalau dicoba pakai email yang sudah pernah dipakai, baris di bawah
// akan melempar PDOException mentah — INI SENGAJA belum ditangani
// rapi, sesuai bab 7.3 poin terakhir. Penanganannya (try/catch) ada
// di latihan bab 7.4 poin 1, menyusul kalau kamu sudah siap.
$stmt = $pdo->prepare(
    "INSERT INTO pesanan (nama, email, telepon, kota, kategori, jumlah)
     VALUES (:nama, :email, :telepon, :kota, :kategori, :jumlah)
     RETURNING id"
);
$stmt->execute([
    'nama'     => $nama,
    'email'    => $email,
    'telepon'  => $telepon,
    'kota'     => $kota,
    'kategori' => $kategori,
    'jumlah'   => (int) $jumlah,
]);

$_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Pesanan tiket berhasil ditambahkan.'];
header('Location: list.php');
exit;