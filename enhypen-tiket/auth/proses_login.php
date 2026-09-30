<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Username dan password wajib diisi.'
    ];
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Username atau password salah.'
        ];
        header('Location: login.php');
        exit;
    }

    $nama_user = !empty($user['nama']) ? $user['nama'] : $user['username'];

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama']     = $nama_user;

    $_SESSION['flash'] = [
        'type' => 'sukses',
        'pesan' => 'Selamat datang kembali, ' . $nama_user . '!'
    ];

    // Redirect aman untuk Railway & Localhost
    header('Location: ../index.php');
    exit;

} catch (PDOException $e) {
    die("Error Login: " . $e->getMessage());
}