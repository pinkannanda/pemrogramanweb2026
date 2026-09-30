<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id        = (int) ($_POST['id'] ?? 0);
$tanggal   = trim($_POST['tanggal'] ?? '');
$kota      = trim($_POST['kota'] ?? '');
$venue     = trim($_POST['venue'] ?? '');
$kapasitas = trim($_POST['kapasitas'] ?? '');
$harga     = trim($_POST['harga'] ?? '');
$status    = trim($_POST['status'] ?? 'Tiket Tersedia');

$errors = [];

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

if ($tanggal === '') {
    $errors[] = 'Tanggal wajib diisi.';
}
if ($kota === '') {
    $errors[] = 'Kota wajib diisi.';
}
if ($venue === '') {
    $errors[] = 'Venue wajib diisi.';
}
if ($kapasitas === '' || !is_numeric($kapasitas) || (int) $kapasitas < 1) {
    $errors[] = 'Kapasitas harus angka minimal 1.';
}
if ($harga === '' || !is_numeric($harga) || (int) $harga < 0) {
    $errors[] = 'Harga tidak boleh negatif.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?id=' . $id);
    exit;
}

// PENTING: WHERE id = :id wajib ada. Tanpa ini, UPDATE akan
// mengubah SEMUA baris di tabel jadwal sekaligus.
$stmt = $pdo->prepare(
    'UPDATE jadwal
     SET tanggal = :tanggal, kota = :kota, venue = :venue,
         kapasitas = :kapasitas, harga_mulai = :harga_mulai, status = :status
     WHERE id = :id'
);
$stmt->execute([
    'tanggal'     => $tanggal,
    'kota'        => $kota,
    'venue'       => $venue,
    'kapasitas'   => (int) $kapasitas,
    'harga_mulai' => (int) $harga,
    'status'      => kodeStatus($status),
    'id'          => $id,
]);

$_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Jadwal tur berhasil diubah.'];
header('Location: list.php');
exit;