<?php
session_start();
require __DIR__ . '/includes/koneksi.php';
require __DIR__ . '/includes/functions.php';

$pageTitle  = 'ENHYPEN Live in Concert';
$activePage = 'home';

$totalJadwal  = $pdo->query("SELECT COUNT(*) FROM jadwal")->fetchColumn();
$totalPesanan = $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();

$tiket = json_decode(file_get_contents(__DIR__ . '/data/tiket.json'), true) ?: [];

include __DIR__ . '/includes/header.php';
?>

<main>
  <section class="hero" id="beranda">
    <h1>ENHYPEN Live in Concert</h1>
    <p>Jakarta International Stadium &mdash; Jumat, 23 Januari 2026 &mdash; Gate 15.00 WIB</p>
    <a href="pesanan/tambah.php" class="btn">Pesan Tiket</a>
  </section>

  <section class="section container" id="statistik">
    <h2>Statistik</h2>
    <div class="stat-grid">
      <div class="stat-card">
        <span class="stat-angka"><?= (int) $totalJadwal ?></span>
        <span class="stat-label">Jadwal Tur</span>
      </div>

      <div class="stat-card">
        <span class="stat-angka"><?= (int) $totalPesanan ?></span>
        <span class="stat-label">Pesanan Masuk</span>
      </div>
    </div>
  </section>

  <section class="section container" id="tiket">
    <h2>Kategori Tiket</h2>

    <div class="ticket-grid">
      <?php if (empty($tiket)): ?>
        <p class="pesan-gagal">Data kategori tiket belum tersedia.</p>
      <?php else: ?>

        <?php foreach ($tiket as $t): ?>
          <div class="ticket-card">
            <span
              class="swatch"
              style="background:<?= h($t['warna']) ?>;">
            </span>

            <h3><?= h($t['nama']) ?></h3>
            <p class="type"><?= h($t['tipe']) ?></p>
            <p class="price">
              <?= formatRupiah((int) $t['harga']) ?>
            </p>
          </div>
        <?php endforeach; ?>

      <?php endif; ?>
    </div>
  </section>

  <section class="section container" id="benefit">
    <h2>Benefit VIP A &amp; B</h2>

    <ul class="benefit-list">
      <li>Satu (1) tiket VIP A/B (Standing)</li>
      <li>Akses eksklusif ke sesi soundcheck</li>
      <li>Merchandise VIP spesial dari ENHYPEN</li>
      <li>Laminate &amp; lanyard eksklusif VIP</li>
      <li>Jalur masuk khusus (dedicated entrance)</li>
      <li>Jalur merchandise khusus (dedicated merchandise lane)</li>
    </ul>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
