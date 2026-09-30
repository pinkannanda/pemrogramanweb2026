<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle  = 'Edit Pesanan Tiket';
$activePage = 'pesanan';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID pesanan tidak valid.');
    header('Location: list.php');
    exit;
}

// Ambil data pesanan berdasarkan ID
try {
    $stmt = $pdo->prepare('SELECT * FROM pesanan WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pesanan) {
        setFlash('danger', 'Data pesanan tidak ditemukan.');
        header('Location: list.php');
        exit;
    }
} catch (PDOException $e) {
    setFlash('danger', 'Gagal mengambil data pesanan.');
    header('Location: list.php');
    exit;
}

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
            $sql = "UPDATE pesanan SET 
                        nama_pemesan = :nama, 
                        email = :email, 
                        no_hp = :hp, 
                        jadwal_id = :jadwal_id, 
                        kategori_tiket = :kategori, 
                        jumlah_tiket = :jumlah, 
                        total_harga = :total 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nama'       => $nama,
                'email'      => $email,
                'hp'         => $hp,
                'jadwal_id'  => $jadwalId,
                'kategori'   => $kategori,
                'jumlah'     => $jumlah,
                'total'      => $totalHarga,
                'id'         => $id
            ]);

            setFlash('success', 'Data pesanan berhasil diperbarui!');
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal memperbarui pesanan: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="card-form" style="margin: 30px auto 40px; padding: 25px 30px; max-width: 500px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(139,38,62,0.08); border: 1px solid #f8d7da;">
    <h2 style="color: #8b263e; font-size: 1.4rem; margin-bottom: 5px; text-align: center; font-weight: 700;">Edit Pesanan Tiket</h2>
    <p style="text-align: center; color: #7a525d; font-size: 0.85rem; margin-bottom: 20px;">Mengubah data pesanan #<?= $id ?></p>

    <?php if ($error !== ''): ?>
      <div class="flash flash-danger" style="margin-bottom: 15px; padding: 10px; color: #a94442; background-color: #f2dede; border-radius: 6px; font-size: 0.9rem;">
        <?= h($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="edit.php?id=<?= $id ?>">
      <div class="form-group" style="margin-bottom: 12px;">
        <label for="nama_pemesan" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Nama Lengkap</label>
        <input type="text" id="nama_pemesan" name="nama_pemesan" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" placeholder="Masukkan nama lengkap" value="<?= h($_POST['nama_pemesan'] ?? $pesanan['nama_pemesan'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="email" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Email</label>
        <input type="email" id="email" name="email" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" placeholder="contoh@email.com" value="<?= h($_POST['email'] ?? $pesanan['email'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="no_hp" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">No. WhatsApp</label>
        <input type="tel" id="no_hp" name="no_hp" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" placeholder="081234567890" value="<?= h($_POST['no_hp'] ?? $pesanan['no_hp'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="jadwal_id" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Kota Konser</label>
        <select id="jadwal_id" name="jadwal_id" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" required>
          <option value="">-- Pilih Kota --</option>
          <?php 
            $selectedJadwal = $_POST['jadwal_id'] ?? $pesanan['jadwal_id'] ?? '';
          ?>
          <?php foreach ($jadwalList as $j): ?>
            <?php 
              $labelKota  = !empty($j['kota']) ? $j['kota'] : 'Kota Tidak Diketahui';
              $labelVenue = !empty($j['venue']) ? ' (' . $j['venue'] . ')' : '';
            ?>
            <option value="<?= $j['id'] ?>" <?= (string)$selectedJadwal === (string)$j['id'] ? 'selected' : '' ?>>
              <?= h($labelKota . $labelVenue) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="kategori_tiket" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Kategori Tiket</label>
        <select id="kategori_tiket" name="kategori_tiket" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" required>
          <option value="">-- Pilih Kategori Tiket --</option>
          <?php 
            $selectedKategori = $_POST['kategori_tiket'] ?? $pesanan['kategori_tiket'] ?? '';
          ?>
          <?php foreach ($tiketList as $t): ?>
            <option value="<?= h($t['nama'] ?? '') ?>" <?= (string)$selectedKategori === (string)($t['nama'] ?? '') ? 'selected' : '' ?>>
              <?= h($t['nama'] ?? 'Tiket') ?> - <?= formatRupiah((int)($t['harga'] ?? 0)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 20px;">
        <label for="jumlah_tiket" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Jumlah Tiket (Maksimal 4)</label>
        <input type="number" id="jumlah_tiket" name="jumlah_tiket" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" min="1" max="4" value="<?= h($_POST['jumlah_tiket'] ?? $pesanan['jumlah_tiket'] ?? '1') ?>" required>
      </div>

      <div style="display: flex; gap: 10px;">
        <button type="submit" style="flex: 1; padding: 10px; font-size: 0.95rem; font-weight: bold; background-color: #c94a6e; color: #ffffff; border: none; border-radius: 8px; cursor: pointer;">
          Simpan Perubahan
        </button>
        <a href="list.php" style="flex: 1; text-align: center; padding: 10px; font-size: 0.95rem; font-weight: bold; background-color: #6c757d; color: #ffffff; border: none; border-radius: 8px; text-decoration: none; display: inline-block;">
          Batal
        </a>
      </div>
    </form>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>