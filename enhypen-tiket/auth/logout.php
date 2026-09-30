<?php
session_start();

// Kosongkan semua data session
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session lama
session_destroy();

// Mulai session baru khusus untuk menyimpan pesan flash logout
session_start();
$_SESSION['flash'] = [
    'type' => 'sukses',
    'pesan' => 'Anda telah berhasil keluar dari sistem.'
];

// Redirect kembali ke halaman login
header('Location: login.php');
exit;