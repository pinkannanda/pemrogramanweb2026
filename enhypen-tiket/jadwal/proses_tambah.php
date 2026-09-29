<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$tanggal   = trim($_POST['tanggal'] ?? '');
$kota      = trim($_POST['kota'] ?? '');
$venue     = trim($_POST['venue'] ?? '');
$kapasitas = trim($_POST['kapasitas'] ?? '');
$harga     = trim($_POST['harga'] ?? '');
$status    = trim($_POST['status'] ?? 'Tiket Tersedia');

$errors = [];

if ($tanggal === '') {
    $errors[] = 'Tanggal wajib diisi.';
} else {
    $tahun = (int) date('Y', strtotime($tanggal) ?: 0);
    if ($tahun < 2026 || $tahun > 2026) {
        $errors[] = 'Tanggal harus di tahun 2026.';
    }
}

if ($kota === '') {
    $errors[] = 'Kota wajib diisi.';
}
if ($venue === '') {
    $errors[] = 'Venue wajib diisi.';
}

if ($kapasitas === '' || !is_numeric($kapasitas)) {
    $errors[] = 'Kapasitas harus berupa angka.';
} elseif ((int) $kapasitas < 1) {
    $errors[] = 'Kapasitas minimal 1.';
}

if ($harga === '' || !is_numeric($harga)) {
    $errors[] = 'Harga harus berupa angka.';
} elseif ((int) $harga < 0) {
    $errors[] = 'Harga tidak boleh negatif.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    $_SESSION['old']   = $_POST;
    header('Location: tambah.php');
    exit;
}

// Jobsheet 8: INSERT prepared statement, menggantikan
// $_SESSION['jadwal'][] = ... dari Jobsheet 7.
$stmt = $pdo->prepare(
    "INSERT INTO jadwal (tanggal, kota, venue, kapasitas, harga_mulai, status)
     VALUES (:tanggal, :kota, :venue, :kapasitas, :harga_mulai, :status)
     RETURNING id"
);
$stmt->execute([
    'tanggal'     => $tanggal,
    'kota'        => $kota,
    'venue'       => $venue,
    'kapasitas'   => (int) $kapasitas,
    'harga_mulai' => (int) $harga,
    'status'      => kodeStatus($status),
]);

$_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Jadwal tur berhasil ditambahkan.'];
header('Location: list.php');
exit;