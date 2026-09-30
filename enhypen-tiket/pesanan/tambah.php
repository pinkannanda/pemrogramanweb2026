<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle  = 'Pesan Tiket Konser';
$activePage = 'pesan_tiket';

try {
    $stmtJadwal = $pdo->query("SELECT * FROM jadwal ORDER BY tanggal ASC");
    $jadwalList = $stmtJadwal->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jadwalList = [];
}

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
            if ($t['nama'] === $kategori) {
                $hargaSatuan = (int)$t['harga'];
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
            $error = 'Gagal menyimpan pesanan. Silakan coba lagi.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="card-form" style="margin: 20px auto 30px; padding: 25px 30px; max-width: 480px;">
    <h2 style="color: #8b263e; font-size: 1.4rem; margin-bottom: 15px; text-align: center; font-weight: 700;">Pesan Tiket Konser</h2>

    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="flash flash-<?= $_SESSION['flash_type'] ?? 'info' ?>" style="margin-bottom: 12px; padding: 8px 12px; font-size: 0.85rem;">
        <?= h($_SESSION['flash_message']) ?>
      </div>
      <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <div class="flash flash-danger" style="margin-bottom: 12px; padding: 8px 12px; font-size: 0.85rem;">
        <?= h($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="tambah.php">
      <div class="form-group" style="margin-bottom: 12px;">
        <label for="nama_pemesan" style="margin-bottom: 4px; font-size: 0.85rem;">Nama Lengkap</label>
        <input type="text" id="nama_pemesan" name="nama_pemesan" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" placeholder="Masukkan nama lengkap" value="<?= h($_POST['nama_pemesan'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="email" style="margin-bottom: 4px; font-size: 0.85rem;">Email</label>
        <input type="email" id="email" name="email" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" placeholder="contoh@email.com" value="<?= h($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="no_hp" style="margin-bottom: 4px; font-size: 0.85rem;">No. WhatsApp</label>
        <input type="tel" id="no_hp" name="no_hp" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" placeholder="081234567890" value="<?= h($_POST['no_hp'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="jadwal_id" style="margin-bottom: 4px; font-size: 0.85rem;">Kota Konser</label>
        <select id="jadwal_id" name="jadwal_id" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" required>
          <option value="">-- Pilih Kota --</option>
          <?php foreach ($jadwalList as $j): ?>
            <option value="<?= $j['id'] ?>" <?= (isset($_POST['jadwal_id']) && $_POST['jadwal_id'] == $j['id']) ? 'selected' : '' ?>>
              <?= h($j['kota']) ?> (<?= h($j['venue']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="kategori_tiket" style="margin-bottom: 4px; font-size: 0.85rem;">Kategori Tiket</label>
        <select id="kategori_tiket" name="kategori_tiket" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" required>
          <option value="">-- Pilih Kategori Tiket --</option>
          <?php foreach ($tiketList as $t): ?>
            <option value="<?= h($t['nama']) ?>" <?= (isset($_POST['kategori_tiket']) && $_POST['kategori_tiket'] === $t['nama']) ? 'selected' : '' ?>>
              <?= h($t['nama']) ?> - <?= formatRupiah((int)$t['harga']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 18px;">
        <label for="jumlah_tiket" style="margin-bottom: 4px; font-size: 0.85rem;">Jumlah Tiket (Maksimal 4)</label>
        <input type="number" id="jumlah_tiket" name="jumlah_tiket" class="form-control" style="padding: 8px 12px; font-size: 0.9rem;" min="1" max="4" value="<?= h($_POST['jumlah_tiket'] ?? '1') ?>" required>
      </div>

      <button type="submit" style="width: 100%; padding: 10px; font-size: 0.95rem; font-weight: bold; background-color: #c94a6e; color: #ffffff; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
        Pesan Tiket Sekarang
      </button>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>