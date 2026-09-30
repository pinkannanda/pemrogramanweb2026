<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

$flash = ambilFlash();
$old   = ambilOldInput();

$pageTitle  = 'Pesan Tiket';
$activePage = 'pesan_tiket';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Pesan Tiket Konser</h2>
      <p>Silakan isi formulir di bawah ini untuk memesan tiket.</p>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_tambah.php" method="post" data-validasi>
      <div class="form-row">
        <label for="nama_pemesan">Nama Lengkap</label>
        <input type="text" id="nama_pemesan" name="nama_pemesan" data-label="Nama lengkap" placeholder="Masukkan nama lengkap" value="<?= h(old($old, 'nama_pemesan') ?: old($old, 'nama')) ?>" required>
      </div>

      <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" data-label="Email" placeholder="contoh@email.com" value="<?= h(old($old, 'email')) ?>" required>
      </div>

      <div class="form-row">
        <label for="telepon">No. WhatsApp</label>
        <input type="tel" id="telepon" name="telepon" data-label="No. WhatsApp" placeholder="081234567890" value="<?= h(old($old, 'telepon')) ?>" required>
      </div>

      <div class="form-row">
        <label for="kota">Kota Konser</label>
        <?php $kotaLama = old($old, 'kota'); ?>
        <select id="kota" name="kota" required>
          <option value="">-- Pilih Kota --</option>
          <?php foreach (['Jakarta, Indonesia', 'Seoul, South Korea', 'Tokyo, Japan', 'Bangkok, Thailand'] as $opsiKota): ?>
            <option value="<?= h($opsiKota) ?>" <?= $opsiKota === $kotaLama ? 'selected' : '' ?>><?= h($opsiKota) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row">
        <label for="kategori">Kategori Tiket</label>
        <?php $kategoriLama = old($old, 'kategori'); ?>
        <select id="kategori" name="kategori" required>
          <option value="">-- Pilih Kategori Tiket --</option>
          <?php foreach (['VIP', 'CAT 1', 'CAT 2', 'CAT 3'] as $opsiKategori): ?>
            <option value="<?= h($opsiKategori) ?>" <?= $opsiKategori === $kategoriLama ? 'selected' : '' ?>><?= h($opsiKategori) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

     
<div class="mb-3">
    <label for="jumlah" class="form-label">Jumlah Tiket (Maksimal 4)</label>
    <input type="number" 
           id="jumlah" 
           name="jumlah" 
           class="form-control" 
           min="1" 
           max="4" 
           value="<?= h(old($old, 'jumlah', '1')) ?>" 
           required>
</div>

      <button type="submit" class="btn btn-block">Pesan Tiket Sekarang</button>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>