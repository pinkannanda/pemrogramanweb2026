<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

// ---------- Pencarian server-side ----------
$q = trim($_GET['q'] ?? '');

// ---------- Pagination ----------
$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

try {
    if ($q !== '') {
        $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM jadwal WHERE kota ILIKE :kw OR venue ILIKE :kw');
        $totalStmt->execute(['kw' => '%' . $q . '%']);
    } else {
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM jadwal');
    }
    $totalBaris = (int) $totalStmt->fetchColumn();
    $totalHalaman = max(1, (int) ceil($totalBaris / $perPage));

    if ($q !== '') {
        $stmt = $pdo->prepare(
            'SELECT * FROM jadwal WHERE kota ILIKE :kw OR venue ILIKE :kw
             ORDER BY id DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('kw', '%' . $q . '%');
    } else {
        $stmt = $pdo->prepare('SELECT * FROM jadwal ORDER BY id DESC LIMIT :limit OFFSET :offset');
    }
    $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $daftarJadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error Query Jadwal: " . $e->getMessage());
}

$flash = ambilFlash();

$pageTitle  = 'Daftar Jadwal';
$activePage = 'jadwal';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head">
      <h2>Daftar Jadwal Tur 2026</h2>
      <p>Data dibaca dari database PostgreSQL, dengan pencarian &amp; pagination di server.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="search-box" method="get" action="list.php">
      <input type="text" id="search-input" name="q" data-filter-tabel="#tabel-jadwal"
             placeholder="Cari kota atau venue..." value="<?= h($q) ?>">
      <button type="submit" class="btn btn-kecil">Cari</button>
      <a href="tambah.php" class="btn btn-kecil">+ Tambah Jadwal</a>
    </form>

    <div class="table-responsive">
      <table id="tabel-jadwal">
        <thead>
          <tr><th>Tanggal</th><th>Kota</th><th>Venue</th><th>Kapasitas</th><th>Harga Mulai</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php if (empty($daftarJadwal)): ?>
            <tr><td colspan="7" class="pesan-gagal">Tidak ada jadwal yang cocok.</td></tr>
          <?php else: ?>
            <?php foreach ($daftarJadwal as $j): ?>
              <?php
                // --- PENANGANAN NULL SAFETY ---
                $tglRaw = $j['tanggal'] ?? $j['tanggal_konser'] ?? null;
                $tglTampil = !empty($tglRaw) ? formatTanggalIndo((string) $tglRaw) : '-';
                
                $kotaTampil  = $j['kota'] ?? $j['nama_event'] ?? '-';
                $venueTampil = $j['venue'] ?? $j['lokasi'] ?? '-';
                $statusRaw   = $j['status'] ?? 'Tersedia';
                $labelStts   = function_exists('labelStatus') ? labelStatus($statusRaw) : $statusRaw;
              ?>
              <tr>
                <td><?= h($tglTampil) ?></td>
                <td><?= h($kotaTampil) ?></td>
                <td><?= h($venueTampil) ?></td>
                <td><?= formatAngka((int) ($j['kapasitas'] ?? 0)) ?></td>
                <td><?= formatRupiah((int) ($j['harga_mulai'] ?? 0)) ?></td>
                <td><span class="status-pill status-<?= h($statusRaw) ?>"><?= h($labelStts) ?></span></td>
                <td class="aksi-cell">
                  <a href="edit.php?id=<?= (int) $j['id'] ?>" class="btn-aksi btn-edit">Edit</a>
                  <form class="form-hapus" method="post" action="hapus.php">
                    <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
                    <button type="submit" class="btn-aksi btn-hapus" data-nama="jadwal <?= h($kotaTampil) ?>">Hapus</button>
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