<?php
/**
 * Header bersama untuk semua halaman.
 * Variabel yang BOLEH diisi oleh halaman pemanggil sebelum include ini:
 *   $pageTitle   (string) judul tab browser, opsional
 *   $activePage  (string) salah satu: 'home', 'jadwal', 'pesanan', 'pesan'
 *
 * $base dihitung OTOMATIS (tidak perlu diisi manual per halaman),
 * berdasarkan seberapa dalam file yang meng-include ini berada dari
 * folder proyek (induk dari folder includes/). Jadi header.php ini
 * bisa dipakai baik oleh index.php di root maupun oleh
 * jadwal/list.php atau pesanan/tambah.php tanpa diubah sama sekali.
 */

$rootDir    = dirname(__DIR__); // folder proyek = induk dari includes/
$currentDir = dirname($_SERVER['SCRIPT_FILENAME']);

$relatif = str_replace('\\', '/', trim(str_replace(realpath($rootDir), '', realpath($currentDir)), '/\\'));
$depth   = $relatif === '' ? 0 : substr_count($relatif, '/') + 1;
$base    = str_repeat('../', $depth);

$pageTitle  = $pageTitle ?? 'ENHYPEN Tickets';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?> — ENHYPEN Tickets</title>
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="nav-bar">
    <a href="<?= $base ?>index.php" class="brand">
      <span class="brand-badge">EN</span>
      <span class="brand-text">ENHYPEN<small>tiket konser</small></span>
    </a>
    <button type="button" class="nav-toggle" aria-label="Buka menu" aria-expanded="false"><span></span></button>
    <nav class="main-nav">
      <ul>
        <li><a href="<?= $base ?>index.php" class="<?= $activePage === 'home' ? 'active' : '' ?>">Beranda</a></li>
        <li><a href="<?= $base ?>jadwal/list.php" class="<?= $activePage === 'jadwal' ? 'active' : '' ?>">Jadwal Tur</a></li>
        <li><a href="<?= $base ?>pesanan/list.php" class="<?= $activePage === 'pesanan' ? 'active' : '' ?>">Pesanan</a></li>
        <li><a href="<?= $base ?>pesanan/tambah.php" class="<?= $activePage === 'pesan' ? 'active' : '' ?>">Pesan Tiket</a></li>
      </ul>
    </nav>
  </div>
</header>