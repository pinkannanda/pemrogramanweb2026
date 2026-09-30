<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM pesanan WHERE id = :id');
$stmt->execute(['id' => $id]);
$pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pesanan) {
    header('Location: list.php');
    exit;
}

$flash = ambilFlash();

$pageTitle  = 'Edit Pesanan';
$activePage = 'pesanan';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Edit Pesanan Tiket</h2>
      <p>Mengubah data pesanan dengan id <?= (int) $pesanan['id'] ?>.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_edit.php" method="post" data-validasi>
      <input type="hidden" name="id" value="<?= (int) $pesanan['id'] ?>">

      <div class="form-row">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" data-label="Nama lengkap" value="<?= h($pesanan['nama']) ?>" required>
      </div>
      <div class="form-grid-2">
        <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" data-label="Email" value="<?= h($pesanan['email']) ?>" required>
        </div>
        <div class="form-row">
          <label for="telepon">No. WhatsApp</label>
          <input type="tel" id="telepon" name="telepon" data-label="No. WhatsApp" value="<?= h($pesanan['telepon']) ?>" required>
        </div>
      </div>
      <div class="form-grid-2">
        <div class="form-row">
          <label for="kota">Kota Konser</label>
          <select id="kota" name="kota">
            <?php foreach (['Jakarta, Indonesia', 'Bangkok, Thailand', 'Manila, Filipina', 'Singapura', 'Kuala Lumpur, Malaysia', 'Ho Chi Minh City, Vietnam', 'Taipei, Taiwan'] as $opsi): ?>
              <option <?= $opsi === $pesanan['kota'] ? 'selected' : '' ?>><?= h($opsi) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-row">
          <label for="kategori">Kategori Tiket</label>
          <?php $kategoriOpsi = ['VIP A & B — Rp 3.650.000', 'CAT 1 — Rp 3.450.000', 'CAT 2 A, B & C — Rp 3.250.000', 'CAT 3 — Rp 2.950.000', 'CAT 4 — Rp 2.750.000', 'CAT 5 — Rp 2.250.000', 'CAT 6 A & B — Rp 1.850.000', 'CAT 7 A & B — Rp 1.450.000']; ?>
          <select id="kategori" name="kategori">
            <?php foreach ($kategoriOpsi as $opsi): ?>
              <option <?= $opsi === $pesanan['kategori'] ? 'selected' : '' ?>><?= h($opsi) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <label for="jumlah">Jumlah Tiket (1–4)</label>
        <input type="number" id="jumlah" name="jumlah" data-label="Jumlah tiket" min="1" max="4" value="<?= (int) $pesanan['jumlah'] ?>" required>
      </div>
      <button type="submit" class="btn btn-block">Simpan Perubahan</button>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>