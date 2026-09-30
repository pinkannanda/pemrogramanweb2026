<?php
session_start();
require __DIR__ . '/../includes/functions.php';

$flash = ambilFlash();
$old   = ambilOldInput();

$pageTitle = 'Registrasi Akun';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Daftar Akun Baru</h2>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_register.php" method="post">
      <div class="form-row">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= h(old($old, 'nama')) ?>" required>
      </div>

      <div class="form-row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= h(old($old, 'username')) ?>" required>
      </div>

      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-block">Daftar Sekarang</button>
      <p style="margin-top: 15px; text-align: center;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>