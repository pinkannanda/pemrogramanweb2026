<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$pageTitle  = 'Daftar Jadwal Tur 2026';
$activePage = 'jadwal';

// Parameter Pencarian & Pagination
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

try {
    if ($q !== '') {
        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM jadwal WHERE kota ILIKE :q OR venue ILIKE :q');
        $countStmt->execute(['q' => "%$q%"]);
        $totalRows = (int) $countStmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT * FROM jadwal WHERE kota ILIKE :q OR venue ILIKE :q ORDER BY tanggal DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':q', "%$q%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $totalRows = (int) $pdo->query('SELECT COUNT(*) FROM jadwal')->fetchColumn();

        $stmt = $pdo->prepare('SELECT * FROM jadwal ORDER BY tanggal DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jadwalList = [];
    $totalRows = 0;
}

$totalPages = ceil($totalRows / $limit);

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="section-head" style="text-align: left; margin-top: 30px;">
    <h2>Daftar Jadwal Tur 2026</h2>
    <p>Data dibaca dari database PostgreSQL, dengan pencarian & pagination di server.</p>
  </div>

  <!-- Form Pencarian & Tombol Tambah -->
  <form method="get" action="list.php" class="search-box">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Cari kota atau venue...">
    <button type="submit" class="btn">Cari</button>
    <a href="tambah.php" class="btn btn-secondary">+ Tambah Jadwal</a>
  </form>

  <!-- Tabel Jadwal -->
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Kota</th>
          <th>Venue</th>
          <th>Kapasitas</th>
          <th>Harga Mulai</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($jadwalList)): ?>
          <tr>
            <td colspan="7" style="text-align: center; color: #7a525d; padding: 20px;">
              Data jadwal tidak ditemukan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($jadwalList as $row): ?>
            <tr>
              <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              <td><strong><?= h($row['kota']) ?></strong></td>
              <td><?= h($row['venue']) ?></td>
              <td><?= number_format((int)$row['kapasitas'], 0, ',', '.') ?></td>
              <td><?= formatRupiah((int)$row['harga_mulai']) ?></td>
              <td>
                <span class="user-greeting" style="background-color: #e2f0d9; color: #2e7d32;">
                  <?= h($row['status'] ?? 'Tersedia') ?>
                </span>
              </td>
              <td class="aksi-cell">
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-aksi btn-edit">Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn-aksi btn-hapus" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="list.php?q=<?= urlencode($q) ?>&page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>