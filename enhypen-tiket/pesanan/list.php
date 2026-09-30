<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');

$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

try {
    if ($q !== '') {
        // Query disesuaikan untuk mengecek kolom nama_pemesan maupun nama
        $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM pesanan WHERE nama_pemesan ILIKE :kw OR email ILIKE :kw OR nama ILIKE :kw');
        $totalStmt->execute(['kw' => '%' . $q . '%']);
    } else {
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM pesanan');
    }
    $totalBaris = (int) $totalStmt->fetchColumn();
    $totalHalaman = max(1, (int) ceil($totalBaris / $perPage));

    if ($q !== '') {
        $stmt = $pdo->prepare(
            'SELECT * FROM pesanan WHERE nama_pemesan ILIKE :kw OR email ILIKE :kw OR nama ILIKE :kw
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
} catch (PDOException $e) {
    die("Error Query Pesanan: " . $e->getMessage());
}

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
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>No. WhatsApp</th>
            <th>Kota</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarPesanan)): ?>
            <tr><td colspan="7" class="pesan-gagal">Tidak ada pesanan yang cocok.</td></tr>
          <?php else: ?>
            <?php foreach ($daftarPesanan as $p): ?>
              <?php
                // Safe Fallback untuk menghindari Undefined Key & NULL argument error
                $namaTampil     = $p['nama_pemesan'] ?? $p['nama'] ?? '-';
                $emailTampil    = $p['email'] ?? '-';
                $teleponTampil  = $p['telepon'] ?? $p['no_wa'] ?? '-';
                $kotaTampil     = $p['kota'] ?? '-';
                $kategoriTampil = $p['kategori'] ?? '-';
                $jumlahTampil   = $p['jumlah'] ?? $p['jumlah_tiket'] ?? 0;
              ?>
              <tr>
                <td><?= h((string)$namaTampil) ?></td>
                <td><?= h((string)$emailTampil) ?></td>
                <td><?= h((string)$teleponTampil) ?></td>
                <td><?= h((string)$kotaTampil) ?></td>
                <td><?= h((string)$kategoriTampil) ?></td>
                <td><?= (int)$jumlahTampil ?></td>
                <td class="aksi-cell">
                  <a href="edit.php?id=<?= (int) $p['id'] ?>" class="btn-aksi btn-edit">Edit</a>
                  <form class="form-hapus" method="post" action="hapus.php">
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button type="submit" class="btn-aksi btn-hapus" data-nama="pemesan <?= h((string)$namaTampil) ?>">Hapus</button>
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