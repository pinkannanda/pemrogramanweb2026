<?php
require __DIR__ . '/../includes/koneksi.php';
require __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle  = 'Edit Jadwal Tur';
$activePage = 'jadwal';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('danger', 'ID jadwal tidak valid.');
    header('Location: list.php');
    exit;
}

// Ambil data jadwal berdasarkan ID
try {
    $stmt = $pdo->prepare('SELECT * FROM jadwal WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jadwal) {
        setFlash('danger', 'Data jadwal tidak ditemukan.');
        header('Location: list.php');
        exit;
    }
} catch (PDOException $e) {
    setFlash('danger', 'Gagal mengambil data jadwal.');
    header('Location: list.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = trim($_POST['tanggal'] ?? '');
    $kota    = trim($_POST['kota'] ?? '');
    $venue   = trim($_POST['venue'] ?? '');

    if ($tanggal === '' || $kota === '' || $venue === '') {
        $error = 'Semua field wajib diisi.';
    } else {
        try {
            $sql = "UPDATE jadwal SET tanggal = :tanggal, kota = :kota, venue = :venue WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'tanggal' => $tanggal,
                'kota'    => $kota,
                'venue'   => $venue,
                'id'      => $id
            ]);

            setFlash('success', 'Jadwal tur berhasil diperbarui!');
            header('Location: list.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal memperbarui jadwal: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <div class="card-form" style="margin: 30px auto 40px; padding: 25px 30px; max-width: 500px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(139,38,62,0.08); border: 1px solid #f8d7da;">
    <h2 style="color: #8b263e; font-size: 1.4rem; margin-bottom: 5px; text-align: center; font-weight: 700;">Edit Jadwal Tur</h2>
    <p style="text-align: center; color: #7a525d; font-size: 0.85rem; margin-bottom: 20px;">Mengubah data jadwal #<?= $id ?></p>

    <?php if ($error !== ''): ?>
      <div class="flash flash-danger" style="margin-bottom: 15px; padding: 10px; color: #a94442; background-color: #f2dede; border-radius: 6px; font-size: 0.9rem;">
        <?= h($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="edit.php?id=<?= $id ?>">
      <div class="form-group" style="margin-bottom: 12px;">
        <label for="tanggal" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Tanggal Konser</label>
        <input type="date" id="tanggal" name="tanggal" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" value="<?= h($_POST['tanggal'] ?? $jadwal['tanggal'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 12px;">
        <label for="kota" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Kota</label>
        <input type="text" id="kota" name="kota" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" placeholder="Contoh: Jakarta" value="<?= h($_POST['kota'] ?? $jadwal['kota'] ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 20px;">
        <label for="venue" style="display:block; margin-bottom: 4px; font-weight:600; font-size: 0.85rem; color: #333;">Stadion / Venue</label>
        <input type="text" id="venue" name="venue" class="form-control" style="width:100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px;" placeholder="Contoh: Gelora Bung Karno" value="<?= h($_POST['venue'] ?? $jadwal['venue'] ?? '') ?>" required>
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