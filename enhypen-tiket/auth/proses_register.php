<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($nama) || empty($username) || empty($password)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Semua kolom wajib diisi.'
    ];
    header('Location: register.php');
    exit;
}

// Cek Username Duplikat
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Username sudah digunakan, silakan pilih username lain.'
    ];
    header('Location: register.php');
    exit;
}

// Hash Password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke Database
$stmt = $pdo->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
$stmt->execute([$nama, $username, $passwordHash]);

$_SESSION['flash'] = [
    'type' => 'sukses',
    'pesan' => 'Registrasi berhasil! Silakan login.'
];
header('Location: login.php');
exit;