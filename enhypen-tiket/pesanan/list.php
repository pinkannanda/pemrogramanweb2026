<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');

$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

if ($q !== '') {
    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM pesanan WHERE nama ILIKE :kw OR email ILIKE :kw');
    $totalStmt->execute(['kw' => '%' . $q . '%']);
} else {
    $totalStmt = $pdo->query('SELECT COUNT(*) FROM pesanan');
}
$totalBaris = (int) $totalStmt->fetchColumn();
$totalHalaman = max(1, (int) ceil($totalBaris / $perPage));

if ($q !== '') {
    $stmt = $pdo->prepare(
        'SELECT * FROM pesanan WHERE nama ILIKE :kw OR email ILIKE :kw
         ORDER BY id DESC LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue('kw', '%' . $q . '%');
} else {
    $stmt = $pdo->prepare('SELECT * FROM pesanan ORDER BY id DESC LIMIT :limit OFFSET :offset');
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarPesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = ambilFlash();

$pageTitle  = 'Daftar Pemesan';
$activePage = 'pesanan';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head">
      <h2>Daftar Pemesan Tiket</h2>
      <p>Data dibaca dari database PostgreSQL, dengan pencarian &amp; pagination di server.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="search-box" method="get" action="list.php">
      <input type="text" id="search-input" name="q" data-filter-tabel="#tabel-pesanan"
             placeholder="Cari nama atau email..." value="<?= h($q) ?>">
      <button type="submit" class="btn btn-kecil">Cari</button>
      <a href="tambah.php" class="btn btn-kecil">+ Pesan Tiket</a>
    </form>

    <div class="table-responsive">
      <table id="tabel-pesanan">
        <thead>
          <tr><th>Nama</th><th>Email</th><th>No. WhatsApp</th><th>Kota</th><th>Kategori</th><th>Jumlah</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarPesanan)): ?>
            <tr><td colspan="7" class="pesan-gagal">Tidak ada pesanan yang cocok.</td></tr>
          <?php else: ?>
            <?php foreach ($daftarPesanan as $p): ?>
              <tr>
                <td><?= h($p['nama']) ?></td>
                <td><?= h($p['email']) ?></td>
                <td><?= h($p['telepon']) ?></td>
                <td><?= h($p['kota']) ?></td>
                <td><?= h($p['kategori']) ?></td>
                <td><?= (int) $p['jumlah'] ?></td>
                <td class="aksi-cell">
                  <a href="edit.php?id=<?= (int) $p['id'] ?>" class="btn-aksi btn-edit">Edit</a>
                  <form class="form-hapus" method="post" action="hapus.php">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="btn-aksi btn-hapus" data-nama="pemesan <?= h($p['nama']) ?>">Hapus</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalHalaman > 1): ?>
      <nav class="pagination">
        <?php for ($p = 1; $p <= $totalHalaman; $p++): ?>
          <a href="list.php?page=<?= $p ?><?= $q !== '' ? '&q=' . urlencode($q) : '' ?>"
             class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>