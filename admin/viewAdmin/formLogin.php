<?php
$pageTitle = 'Admin Login';
ob_start();
?>
<div class="fh-card p-4">
  <div class="d-flex align-items-center gap-2 mb-3">
    <div class="fh-logo"><i class="bi bi-shield-lock"></i></div>
    <div>
      <div class="fw-bold">Вход в админку</div>
      <div class="text-muted small">FilmHub Admin Panel</div>
    </div>
  </div>

  <form method="post" action="login" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Login или Email</label>
      <input type="text" name="email" class="form-control" placeholder="admin или admin@filmhub.local" autocomplete="username">
    </div>
    <div class="mb-3">
      <label class="form-label">Пароль</label>
      <input type="password" name="password" class="form-control" placeholder="••••••••" autocomplete="current-password">
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right me-1"></i> Войти
      </button>
      <a class="btn btn-outline-secondary ms-auto" href="../index.php">
        <i class="bi bi-arrow-left me-1"></i> На сайт
      </a>
    </div>
  </form>

  <div class="text-muted small mt-3">
    Тестовые данные: admin / Admin!2345
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
