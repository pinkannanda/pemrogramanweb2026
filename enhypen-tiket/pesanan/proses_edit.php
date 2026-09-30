<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id       = (int) ($_POST['id'] ?? 0);
$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telepon  = trim($_POST['telepon'] ?? '');
$kota     = trim($_POST['kota'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$jumlah   = trim($_POST['jumlah'] ?? '');

$errors = [];

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

if ($nama === '') $errors[] = 'Nama wajib diisi.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
if ($telepon === '' || !preg_match('/^\+?[0-9\s-]{9,15}$/', $telepon)) $errors[] = 'No. WhatsApp harus 9-15 digit angka.';
if ($jumlah === '' || !is_numeric($jumlah) || (int) $jumlah < 1 || (int) $jumlah > 4) $errors[] = 'Jumlah tiket harus 1-4.';

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?id=' . $id);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'UPDATE pesanan
         SET nama = :nama, email = :email, telepon = :telepon,
             kota = :kota, kategori = :kategori, jumlah = :jumlah
         WHERE id = :id'
    );
    $stmt->execute([
        'nama'     => $nama,
        'email'    => $email,
        'telepon'  => $telepon,
        'kota'     => $kota,
        'kategori' => $kategori,
        'jumlah'   => (int) $jumlah,
        'id'       => $id,
    ]);
} catch (PDOException $e) {
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Email ini sudah dipakai pesanan lain.'];
        header('Location: edit.php?id=' . $id);
        exit;
    }
    throw $e;
}

$_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Pesanan berhasil diubah.'];
header('Location: list.php');
exit;