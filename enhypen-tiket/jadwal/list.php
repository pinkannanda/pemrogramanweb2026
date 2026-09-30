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
  <div style="width: 100%; overflow-x: auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(139,38,62,0.08); border: 1px solid #f8d7da; margin-bottom: 25px;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 650px;">
      <thead>
        <tr style="background-color: #f8d7da; color: #8b263e;">
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Tanggal</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Kota</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Venue</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Kapasitas</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Harga Mulai</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Status</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Aksi</th>
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
            <tr style="border-bottom: 1px solid #f2d6dc;">
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <?= !empty($row['tanggal']) ? date('d M Y', strtotime($row['tanggal'])) : '-' ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <strong><?= h($row['kota'] ?? '-') ?></strong>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <?= h($row['venue'] ?? '-') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <?= number_format((int)($row['kapasitas'] ?? 0), 0, ',', '.') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <?= formatRupiah((int)($row['harga_mulai'] ?? 0)) ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <span style="background-color: #fce4ec; color: #8b263e; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                  <?= h($row['status'] ?? 'Tersedia') ?>
                </span>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <div style="display: flex; gap: 8px; align-items: center;">
                  <a href="edit.php?id=<?= $row['id'] ?>" style="color: #c94a6e; text-decoration: none; font-weight: 600; border: 1px solid #c94a6e; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">Edit</a>
                  <a href="hapus.php?id=<?= $row['id'] ?>" style="color: #d9534f; text-decoration: none; font-weight: 600; border: 1px solid #d9534f; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                </div>
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