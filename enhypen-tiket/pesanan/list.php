<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$daftarPesanan = $pdo->query("SELECT * FROM pesanan ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$flash = ambilFlash();

$pageTitle  = 'Daftar Pemesan';
$activePage = 'pesanan';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head">
      <h2>Daftar Pemesan Tiket</h2>
      <p>Data dibaca langsung dari database PostgreSQL (tabel <code>pesanan</code>), tersimpan permanen.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <div class="search-box">
      <input type="search" data-filter-tabel="#tabel-pesanan" placeholder="Cari nama, email, kota, atau kategori..." aria-label="Cari pesanan">
      <a href="tambah.php" class="btn btn-kecil">+ Pesan Tiket</a>
    </div>
    <div class="table-responsive">
      <table id="tabel-pesanan">
        <thead>
          <tr><th>Nama</th><th>Email</th><th>No. WhatsApp</th><th>Kota</th><th>Kategori</th><th>Jumlah</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarPesanan)): ?>
            <tr><td colspan="7" class="pesan-gagal">Belum ada pemesan.</td></tr>
          <?php else: ?>
            <?php foreach ($daftarPesanan as $p): ?>
              <tr>
                <td><?= h($p['nama']) ?></td>
                <td><?= h($p['email']) ?></td>
                <td><?= h($p['telepon']) ?></td>
                <td><?= h($p['kota']) ?></td>
                <td><?= h($p['kategori']) ?></td>
                <td><?= (int) $p['jumlah'] ?></td>
                <td><button type="button" class="btn-hapus" data-nama="pemesan <?= h($p['nama']) ?>">Hapus</button></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>