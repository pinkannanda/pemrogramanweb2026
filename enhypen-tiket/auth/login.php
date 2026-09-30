<?php
session_start();
require __DIR__ . '/../includes/functions.php';

$flash = ambilFlash();
$old   = ambilOldInput();

$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';
?>

<main class="container">
  <section class="section">
    <div class="section-head section-head-center">
      <h2>Login Pengguna</h2>
    </div>

    <?php if ($flash): ?>
      <p class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['pesan']) ?></p>
    <?php endif; ?>

    <form class="order-card" action="proses_login.php" method="post">
      <div class="form-row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= h(old($old, 'username')) ?>" required>
      </div>

      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-block">Masuk</button>
      <p style="margin-top: 15px; text-align: center;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>