<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Delete HANYA boleh lewat POST, bukan GET. Kalau seseorang membuka
// halaman ini langsung lewat address bar (itu request GET), tidak
// ada apa pun yang terhapus — cuma diarahkan balik ke list.php.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    // WHERE id = :id wajib ada — tanpa ini, DELETE menghapus SEMUA
    // baris di tabel jadwal sekaligus.
    $stmt = $pdo->prepare('DELETE FROM jadwal WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Jadwal tur berhasil dihapus.'];
}

header('Location: list.php');
exit;