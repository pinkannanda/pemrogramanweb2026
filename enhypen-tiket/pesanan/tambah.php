<?php
session_start();
require __DIR__ . '/../includes/functions.php';

$flash = ambilFlash();
$old   = ambilOldInput();

$pageTitle  = 'Tambah Jadwal';
$activePage = 'jadwal';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Tambah Jadwal Tur</h2>
      <p>Data dikirim ke <code>proses_tambah.php</code> dan divalidasi di server, bukan hanya di browser.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_tambah.php" method="post" data-validasi>
      <div class="form-row">
        <label for="tanggal">Tanggal Konser</label>
        <input type="date" id="tanggal" name="tanggal" data-label="Tanggal" min="2026-01-01" max="2026-12-31" value="<?= h(old($old, 'tanggal')) ?>" required>
      </div>
      <div class="form-row">
        <label for="kota">Kota</label>
        <input type="text" id="kota" name="kota" data-label="Kota" placeholder="Contoh: Jakarta, Indonesia" value="<?= h(old($old, 'kota')) ?>" required>
      </div>
      <div class="form-row">
        <label for="venue">Venue</label>
        <input type="text" id="venue" name="venue" data-label="Venue" placeholder="Nama stadion / arena" value="<?= h(old($old, 'venue')) ?>" required>
      </div>
      <div class="form-grid-2">
        <div class="form-row">
          <label for="kapasitas">Kapasitas</label>
          <input type="number" id="kapasitas" name="kapasitas" data-label="Kapasitas" min="1" value="<?= h(old($old, 'kapasitas')) ?>" required>
        </div>
        <div class="form-row">
          <label for="harga">Harga Mulai (Rp)</label>
          <input type="number" id="harga" name="harga" data-label="Harga" min="0" value="<?= h(old($old, 'harga')) ?>" required>
        </div>
      </div>
      <div class="form-row">
        <label for="status">Status</label>
        <?php $statusLama = old($old, 'status', 'Tiket Tersedia'); ?>
        <select id="status" name="status">
          <?php foreach (['Tiket Tersedia', 'Segera Dibuka', 'Habis Terjual'] as $opsi): ?>
            <option <?= $opsi === $statusLama ? 'selected' : '' ?>><?= h($opsi) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-block">Simpan Jadwal</button>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>