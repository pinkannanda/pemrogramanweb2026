<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle  = 'Daftar Pemesan Tiket';
$activePage = 'pesanan';

// Parameter Pencarian & Pagination
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

try {
    if ($q !== '') {
        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM pesanan WHERE nama_pemesan ILIKE :q OR email ILIKE :q OR no_hp ILIKE :q');
        $countStmt->execute(['q' => "%$q%"]);
        $totalRows = (int) $countStmt->fetchColumn();

        $sql = 'SELECT p.*, j.kota, j.venue 
                FROM pesanan p 
                LEFT JOIN jadwal j ON p.jadwal_id = j.id 
                WHERE p.nama_pemesan ILIKE :q OR p.email ILIKE :q OR p.no_hp ILIKE :q 
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q', "%$q%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $totalRows = (int) $pdo->query('SELECT COUNT(*) FROM pesanan')->fetchColumn();

        $sql = 'SELECT p.*, j.kota, j.venue 
                FROM pesanan p 
                LEFT JOIN jadwal j ON p.jadwal_id = j.id 
                ORDER BY p.id DESC LIMIT :limit OFFSET :offset';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $pesananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pesananList = [];
    $totalRows = 0;
}

$totalPages = ceil($totalRows / $limit);

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="section-head" style="text-align: left; margin-top: 30px;">
    <h2>Daftar Pemesan Tiket</h2>
    <p>Data dibaca dari database PostgreSQL, dengan pencarian & pagination di server.</p>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash flash-<?= $_SESSION['flash_type'] ?? 'info' ?>" style="margin-bottom: 20px; padding: 12px; border-radius: 8px; background-color: #d4edda; color: #155724; font-weight: 500;">
      <?= h($_SESSION['flash_message']) ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <!-- Form Pencarian & Tombol Tambah -->
  <form method="get" action="list.php" class="search-box" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Cari nama atau email..." class="form-control" style="flex: 1; min-width: 200px; padding: 10px 15px;">
    <button type="submit" class="btn" style="background-color: #c94a6e; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Cari</button>
    <a href="tambah.php" class="btn btn-secondary" style="background-color: #8b263e; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; display: inline-block;">+ Pesan Tiket</a>
  </form>

  <!-- Tabel Pesanan -->
  <div style="width: 100%; overflow-x: auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(139,38,62,0.08); border: 1px solid #f8d7da; margin-bottom: 25px;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 750px;">
      <thead>
        <tr style="background-color: #f8d7da; color: #8b263e;">
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Nama</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Email</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">No. WhatsApp</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Kota</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Kategori</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Jumlah</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Total Harga</th>
          <th style="padding: 14px 16px; font-size: 0.85rem; text-transform: uppercase;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pesananList)): ?>
          <tr>
            <td colspan="8" style="text-align: center; color: #7a525d; padding: 25px;">
              Data pemesan tidak ditemukan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($pesananList as $row): ?>
            <tr style="border-bottom: 1px solid #f2d6dc;">
              <td style="padding: 14px 16px; font-size: 0.95rem; font-weight: 600; color: #333;">
                <?= h($row['nama_pemesan'] ?? '-') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem; color: #555;">
                <?= h($row['email'] ?? '-') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem; color: #555;">
                <?= h($row['no_hp'] ?? '-') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem; color: #555;">
                <?= h(!empty($row['kota']) ? $row['kota'] : '-') ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <span style="background-color: #fce4ec; color: #8b263e; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">
                  <?= h($row['kategori_tiket'] ?? '-') ?>
                </span>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem; font-weight: 600; text-align: center;">
                <?= (int)($row['jumlah_tiket'] ?? 1) ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem; font-weight: 600; color: #8b263e;">
                <?= formatRupiah((int)($row['total_harga'] ?? 0)) ?>
              </td>
              <td style="padding: 14px 16px; font-size: 0.95rem;">
                <div style="display: flex; gap: 8px; align-items: center;">
                  <a href="edit.php?id=<?= $row['id'] ?>" style="color: #c94a6e; text-decoration: none; font-weight: 600; border: 1px solid #c94a6e; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">Edit</a>
                  <a href="hapus.php?id=<?= $row['id'] ?>" style="color: #d9534f; text-decoration: none; font-weight: 600; border: 1px solid #d9534f; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">Hapus</a>
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
    <div class="pagination" style="display: flex; gap: 6px; justify-content: center; margin-bottom: 30px;">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="list.php?q=<?= urlencode($q) ?>&page=<?= $i ?>" style="padding: 8px 14px; text-decoration: none; border-radius: 6px; border: 1px solid #c94a6e; color: <?= $i === $page ? '#fff' : '#c94a6e' ?>; background-color: <?= $i === $page ? '#c94a6e' : '#fff' ?>; font-weight: 600;">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>