<?php
// Aktifkan error log
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
}
if ($kota === '') {
    $errors[] = 'Kota wajib diisi.';
}
if ($venue === '') {
    $errors[] = 'Venue wajib diisi.';
}
if ($kapasitas === '' || !is_numeric($kapasitas)) {
    $errors[] = 'Kapasitas harus berupa angka.';
}
if ($harga === '' || !is_numeric($harga)) {
    $errors[] = 'Harga harus berupa angka.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    $_SESSION['old']   = $_POST;
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO jadwal (tanggal, kota, venue, kapasitas, harga_mulai, status)
         VALUES (:tanggal, :kota, :venue, :kapasitas, :harga_mulai, :status)"
    );
    $stmt->execute([
        'tanggal'     => $tanggal,
        'kota'        => $kota,
        'venue'       => $venue,
        'kapasitas'   => (int) $kapasitas,
        'harga_mulai' => (int) $harga,
        'status'      => $status,
    ]);

    $_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Jadwal tur berhasil ditambahkan.'];
    header('Location: list.php');
    exit;

} catch (Exception $e) {
    // Tampilkan detail error tepat di layar
    echo "<h3 style='color:red;'>Terjadi Error Saat Menyimpan:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}