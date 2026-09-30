<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle  = 'Pesan Tiket Konser';
$activePage = 'pesan_tiket';

// Ambil data jadwal tur dari database
try {
    $stmtJadwal = $pdo->query("SELECT * FROM jadwal ORDER BY tanggal ASC");
    $jadwalList = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jadwalList = [];
}

// Ambil data kategori tiket dari file JSON
$tiketFile = __DIR__ . '/../data/tiket.json';
$tiketList = file_exists($tiketFile) ? json_decode(file_get_contents($tiketFile), true) : [];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama_pemesan'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $hp       = trim($_POST['no_hp'] ?? '');
    $jadwalId = (int)($_POST['jadwal_id'] ?? 0);
    $kategori = trim($_POST['kategori_tiket'] ?? '');
    $jumlah   = (int)($_POST['jumlah_tiket'] ?? 1);

    if ($nama === '' || $email === '' || $hp === '' || $jadwalId <= 0 || $kategori === '') {
        $error = 'Semua field wajib diisi.';
    } elseif ($jumlah < 1 || $jumlah > 4) {
        $error = 'Maksimal pembelian adalah 4 tiket per transaksi.';
    } else {
        $hargaSatuan = 0;
        foreach ($tiketList as $t) {
            if (($t['nama'] ?? '') === $kategori) {
                $hargaSatuan = (int)($t['harga'] ?? 0);
                break;
            }
        }
        $totalHarga = $hargaSatuan * $jumlah;

        try {
            $sql = "INSERT INTO pesanan (nama_pemesan, email, no_hp, jadwal_id, kategori_tiket, jumlah_tiket, total_harga) 
                    VALUES (:nama, :email, :hp, :jadwal_id, :kategori, :jumlah, :total)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nama'       => $nama,
                'email'      => $email,
                'hp'         => $hp,
                'jadwal_id'  => $jadwalId,
                'kategori'   => $kategori,
                'jumlah'     => $jumlah,
                'total'      => $totalHarga
            ]);

            setFlash('success', 'Tiket berhasil dipesan!');
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            // Menampilkan pesan error MySQL agar mudah didebug
            $error = 'Gagal menyimpan pesanan: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="card-form" style="margin: 20px auto 40px; padding: 25px 30px; max-width: 500px;">
    <h2 style="color: #8b263e; font-size: 1.4rem; margin-bottom: 15px; text-align: center; font-weight: 700;">Pesan Tiket Konser</h2>

    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="flash flash-<?= $_SESSION['flash_type'] ?? 'info' ?>" style="margin-bottom: 15px; padding: 10px;">
        <?= h($_SESSION['flash_message']) ?>
      </div>
      <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <div class="flash flash-danger" style="margin-bottom: 15px; padding: 10px; color: #a94442; background-color: #f2dede; border-radius: 6px; font-size: 0.9rem;">
        <?= h($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="tambah.php">
      <div class="form-group" style="margin-bottom: 12px;">
        <label for="nama_pemesan" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">Nama Lengkap</label>
        <input type="text" id="nama_pemesan" name="nama_pemesan" class="form-control" style="width:100%; padding: 8px 12px;" placeholder="Masukkan nama lengkap" value="<?= h($_POST['nama_pemesan'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="email" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">Email</label>
        <input type="email" id="email" name="email" class="form-control" style="width:100%; padding: 8px 12px;" placeholder="contoh@email.com" value="<?= h($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="no_hp" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">No. WhatsApp</label>
        <input type="tel" id="no_hp" name="no_hp" class="form-control" style="width:100%; padding: 8px 12px;" placeholder="081234567890" value="<?= h($_POST['no_hp'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="jadwal_id" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">Kota Konser</label>
        <select id="jadwal_id" name="jadwal_id" class="form-control" style="width:100%; padding: 8px 12px;" required>
          <option value="">-- Pilih Kota --</option>
          <?php foreach ($jadwalList as $j): ?>
            <?php 
              $labelKota  = !empty($j['kota']) ? $j['kota'] : 'Kota Tidak Diketahui';
              $labelVenue = !empty($j['venue']) ? ' (' . $j['venue'] . ')' : '';
            ?>
            <option value="<?= $j['id'] ?>" <?= (isset($_POST['jadwal_id']) && $_POST['jadwal_id'] == $j['id']) ? 'selected' : '' ?>>
              <?= h($labelKota . $labelVenue) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="kategori_tiket" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">Kategori Tiket</label>
        <select id="kategori_tiket" name="kategori_tiket" class="form-control" style="width:100%; padding: 8px 12px;" required>
          <option value="">-- Pilih Kategori Tiket --</option>
          <?php foreach ($tiketList as $t): ?>
            <option value="<?= h($t['nama'] ?? '') ?>" <?= (isset($_POST['kategori_tiket']) && $_POST['kategori_tiket'] === ($t['nama'] ?? '')) ? 'selected' : '' ?>>
              <?= h($t['nama'] ?? 'Tiket') ?> - <?= formatRupiah((int)($t['harga'] ?? 0)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 20px;">
        <label for="jumlah_tiket" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem;">Jumlah Tiket (Maksimal 4)</label>
        <input type="number" id="jumlah_tiket" name="jumlah_tiket" class="form-control" style="width:100%; padding: 8px 12px;" min="1" max="4" value="<?= h($_POST['jumlah_tiket'] ?? '1') ?>" required>
      </div>

      <button type="submit" style="width: 100%; padding: 12px; font-size: 1rem; font-weight: bold; background-color: #c94a6e; color: #ffffff; border: none; border-radius: 8px; cursor: pointer; display: block;">
        Pesan Tiket Sekarang
      </button>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>