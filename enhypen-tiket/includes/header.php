<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user']);
$username = $isLoggedIn ? $_SESSION['user']['username'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? h($pageTitle) . ' - ' : '' ?>ENHYPEN Konser</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .navbar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #ffffff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: relative;
    }
    
    .hamburger-btn {
      display: none;
      background: none;
      border: none;
      font-size: 1.8rem;
      color: #8b263e;
      cursor: pointer;
      padding: 0;
      line-height: 1;
    }

    .nav-menu {
      display: flex;
      gap: 15px;
      align-items: center;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .nav-menu a {
      text-decoration: none;
      color: #555;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 6px 12px;
      border-radius: 6px;
      transition: all 0.2s;
    }

    .nav-menu a.active, .nav-menu a:hover {
      color: #8b263e;
      border-bottom: 2px solid #8b263e;
    }

    /* Tampilan Mobile */
    @media (max-width: 768px) {
      .navbar-container {
        flex-wrap: wrap;
      }

      .hamburger-btn {
        display: block;
      }

      .nav-menu {
        display: none; /* Disembunyikan dulu sampai hamburger diklik */
        flex-direction: column;
        align-items: flex-start; /* Rata kiri */
        width: 100%;
        margin-top: 15px;
        padding-top: 10px;
        border-top: 1px solid #f8d7da;
        gap: 10px;
      }

      .nav-menu.active {
        display: flex; /* Muncul saat toggle aktif */
      }

      .nav-menu li {
        width: 100%;
      }

      .nav-menu a {
        display: block;
        width: 100%;
        padding: 8px 0;
        text-align: left; /* Rata kiri */
      }

      .nav-menu a.active, .nav-menu a:hover {
        border-bottom: none;
        background-color: #fce4ec;
        padding-left: 10px;
      }
    }
  </style>
</head>
<body style="background-color: #fff0f3; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<header style="background: #ffffff; border-bottom: 1px solid #f8d7da;">
  <div class="navbar-container" style="max-width: 1100px; margin: 0 auto;">
    <!-- Logo ENHYPEN -->
    <a href="/index.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
      <div style="background-color: #c94a6e; color: #fff; font-weight: bold; padding: 6px 12px; border-radius: 8px; font-size: 1.1rem;">EN</div>
      <div>
        <div style="color: #333; font-weight: 800; font-size: 1.1rem; line-height: 1;">ENHYPEN</div>
        <div style="color: #777; font-size: 0.75rem; letter-spacing: 1px;">TIKET KONSER</div>
      </div>
    </a>

    <!-- Tombol Hamburger Mobile -->
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Menu">
      &#9776;
    </button>

    <!-- Menu Navigasi -->
    <ul class="nav-menu" id="navMenu">
      <li><a href="/index.php" class="<?= ($activePage ?? '') === 'beranda' ? 'active' : '' ?>">Beranda</a></li>
      <li><a href="/jadwal/list.php" class="<?= ($activePage ?? '') === 'jadwal' ? 'active' : '' ?>">Jadwal Tur</a></li>
      <li><a href="/pesanan/list.php" class="<?= ($activePage ?? '') === 'pesanan' ? 'active' : '' ?>">Pesanan</a></li>
      <li><a href="/pesanan/tambah.php" class="<?= ($activePage ?? '') === 'pesan_tiket' ? 'active' : '' ?>">Pesan Tiket</a></li>
      
      <?php if ($isLoggedIn): ?>
        <li style="background-color: #fce4ec; color: #8b263e; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 5px;">
          Hi, <?= h($username) ?>
        </li>
        <li style="margin-top: 5px;"><a href="/auth/logout.php" style="background-color: #c94a6e; color: #fff; padding: 6px 14px; border-radius: 8px; border-bottom: none; display: inline-block;">Logout</a></li>
      <?php else: ?>
        <li style="margin-top: 5px;"><a href="/auth/login.php" style="background-color: #c94a6e; color: #fff; padding: 6px 14px; border-radius: 8px; border-bottom: none; display: inline-block;">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</header>

<script>
  // Toggle Hamburger Menu di Mobile
  document.getElementById('hamburgerBtn').addEventListener('click', function() {
    document.getElementById('navMenu').classList.toggle('active');
  });
</script>