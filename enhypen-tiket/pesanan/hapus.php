<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM pesanan WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = ['type' => 'sukses', 'pesan' => 'Pesanan berhasil dihapus.'];
}

header('Location: list.php');
exit;