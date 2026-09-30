<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket — ENHYPEN Tickets</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header style="background: #ffffff; border-bottom: 2px solid #f3d7e4; padding: 12px 30px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-family: sans-serif;">
        <!-- Logo ENHYPEN -->
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="background: #b85b84; color: #ffffff; font-weight: bold; padding: 6px 10px; border-radius: 6px; font-size: 14px; display: inline-block;">EN</span>
            <div style="display: flex; flex-direction: column; line-height: 1.2;">
                <strong style="color: #333333; font-size: 16px; letter-spacing: 0.5px;">ENHYPEN</strong>
                <small style="color: #888888; font-size: 10px; font-weight: 600; letter-spacing: 1px;">TIKET KONSER</small>
            </div>
        </div>

        <!-- Menu Navigasi & User Info -->
        <nav style="display: flex; align-items: center; gap: 20px;">
            <a href="/" style="text-decoration: none; color: #555555; font-weight: 600; font-size: 14px; transition: color 0.2s;">Beranda</a>
            <a href="/jadwal/list.php" style="text-decoration: none; color: #555555; font-weight: 600; font-size: 14px; transition: color 0.2s;">Jadwal Tur</a>
            <a href="/pesanan/list.php" style="text-decoration: none; color: #b85b84; font-weight: 700; font-size: 14px; border-bottom: 2px solid #b85b84; padding-bottom: 2px;">Pesanan</a>
            <a href="/pesanan/tambah.php" style="text-decoration: none; color: #555555; font-weight: 600; font-size: 14px; transition: color 0.2s;">Pesan Tiket</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div style="display: flex; align-items: center; gap: 12px; margin-left: 15px; padding-left: 15px; border-left: 1px solid #e0e0e0;">
                    <span style="background: #fdf0f5; color: #b85b84; font-weight: 700; font-size: 13px; padding: 6px 12px; border-radius: 20px; border: 1px solid #f3d7e4;">
                        Hi, <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?>
                    </span>
                    <a href="/auth/logout.php" style="text-decoration: none; color: #dc3545; font-weight: 700; font-size: 13px; background: #fff0f0; padding: 6px 12px; border-radius: 6px; border: 1px solid #f8d7da; transition: all 0.2s;">Logout</a>
                </div>
            <?php else: ?>
                <a href="/auth/login.php" style="text-decoration: none; color: #ffffff; background: #b85b84; font-weight: 600; font-size: 13px; padding: 6px 16px; border-radius: 6px;">Login</a>
            <?php endif; ?>
        </nav>
    </header>