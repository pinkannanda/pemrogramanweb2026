<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM jadwal WHERE id = :id');
$stmt->execute(['id' => $id]);
$jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jadwal) {
    header('Location: list.php');
    exit;
}

$flash = ambilFlash();

$pageTitle  = 'Edit Jadwal';
$activePage = 'jadwal';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Edit Jadwal Tur</h2>
      <p>Mengubah data jadwal dengan id <?= (int) $jadwal['id'] ?>.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_edit.php" method="post" data-validasi>
      <input type="hidden" name="id" value="<?= (int) $jadwal['id'] ?>">

      <div class="form-row">
        <label for="tanggal">Tanggal Konser</label>
        <input type="date" id="tanggal" name="tanggal" data-label="Tanggal" min="2026-01-01" max="2026-12-31" value="<?= h($jadwal['tanggal']) ?>" required>
      </div>
      <div class="form-row">
        <label for="kota">Kota</label>
        <input type="text" id="kota" name="kota" data-label="Kota" value="<?= h($jadwal['kota']) ?>" required>
      </div>
      <div class="form-row">
        <label for="venue">Venue</label>
        <input type="text" id="venue" name="venue" data-label="Venue" value="<?= h($jadwal['venue']) ?>" required>
      </div>
      <div class="form-grid-2">
        <div class="form-row">
          <label for="kapasitas">Kapasitas</label>
          <input type="number" id="kapasitas" name="kapasitas" data-label="Kapasitas" min="1" value="<?= (int) $jadwal['kapasitas'] ?>" required>
        </div>
        <div class="form-row">
          <label for="harga">Harga Mulai (Rp)</label>
          <input type="number" id="harga" name="harga" data-label="Harga" min="0" value="<?= (int) $jadwal['harga_mulai'] ?>" required>
        </div>
      </div>
      <div class="form-row">
        <label for="status">Status</label>
        <?php $statusLabelSekarang = labelStatus($jadwal['status']); ?>
        <select id="status" name="status">
          <?php foreach (['Tiket Tersedia', 'Segera Dibuka', 'Habis Terjual'] as $opsi): ?>
            <option <?= $opsi === $statusLabelSekarang ? 'selected' : '' ?>><?= h($opsi) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-block">Simpan Perubahan</button>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>