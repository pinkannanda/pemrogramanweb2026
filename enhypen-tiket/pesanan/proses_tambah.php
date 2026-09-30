<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

// 1. Tangkap input POST dari form tambah.php
$nama     = trim($_POST['nama_pemesan'] ?? $_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telepon  = trim($_POST['telepon'] ?? $_POST['no_wa'] ?? '');
$kota     = trim($_POST['kota'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$jumlah   = (int) ($_POST['jumlah'] ?? $_POST['jumlah_tiket'] ?? 1);

// 2. Validasi input
if ($nama === '' || $email === '' || $telepon === '' || $kota === '' || $kategori === '') {
    simpanOldInput($_POST);
    setFlash('danger', 'Harap isi semua kolom formulir.');
    header('Location: tambah.php');
    exit;
}

try {
    // 3. Simpan data menggunakan kolom nama_pemesan
    $stmt = $pdo->prepare('
        INSERT INTO pesanan (nama_pemesan, email, telepon, kota, kategori, jumlah)
        VALUES (:nama, :email, :telepon, :kota, :kategori, :jumlah)
    ');

    $stmt->execute([
        'nama'     => $nama,
        'email'    => $email,
        'telepon'  => $telepon,
        'kota'     => $kota,
        'kategori' => $kategori,
        'jumlah'   => $jumlah
    ]);

    setFlash('success', 'Pesanan tiket berhasil ditambahkan.');
    header('Location: list.php');
    exit;

} catch (PDOException $ex) {
    simpanOldInput($_POST);
    setFlash('danger', 'Gagal menyimpan pesanan: ' . $ex->getMessage());
    header('Location: tambah.php');
    exit;
}