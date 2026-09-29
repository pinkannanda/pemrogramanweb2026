<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

// Jobsheet 8: sumber data sekarang SELECT dari database, bukan lagi
// $_SESSION['jadwal']. ORDER BY id DESC = data terbaru muncul di atas.
$daftarJadwal = $pdo->query("SELECT * FROM jadwal ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$flash = ambilFlash();

$pageTitle  = 'Daftar Jadwal';
$activePage = 'jadwal';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head">
      <h2>Daftar Jadwal Tur 2026</h2>
      <p>Data dibaca langsung dari database PostgreSQL (tabel <code>jadwal</code>), tersimpan permanen.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <div class="search-box">
      <input type="search" data-filter-tabel="#tabel-jadwal" placeholder="Cari kota, venue, atau status..." aria-label="Cari jadwal">
      <a href="tambah.php" class="btn btn-kecil">+ Tambah Jadwal</a>
    </div>
    <div class="table-responsive">
      <table id="tabel-jadwal">
        <thead>
          <tr><th>Tanggal</th><th>Kota</th><th>Venue</th><th>Kapasitas</th><th>Harga Mulai</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarJadwal)): ?>
            <tr><td colspan="7" class="pesan-gagal">Belum ada jadwal.</td></tr>
          <?php else: ?>
            <?php foreach ($daftarJadwal as $j): ?>
              <tr>
                <td><?= h(formatTanggalIndo($j['tanggal'])) ?></td>
                <td><?= h($j['kota']) ?></td>
                <td><?= h($j['venue']) ?></td>
                <td><?= formatAngka((int) $j['kapasitas']) ?></td>
                <td><?= formatRupiah((int) $j['harga_mulai']) ?></td>
                <td><span class="status-pill status-<?= h($j['status']) ?>"><?= h(labelStatus($j['status'])) ?></span></td>
                <td><button type="button" class="btn-hapus" data-nama="jadwal <?= h($j['kota']) ?>">Hapus</button></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>