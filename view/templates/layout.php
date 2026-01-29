<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'FilmHub';
$content   = $content ?? '';

$isAuth = !empty($_SESSION['user_id']);
$login  = $_SESSION['login'] ?? '';
$role   = $_SESSION['role'] ?? '';

// base: /filmhub или ''
$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$base = rtrim($base, '/');
$baseHref = $base === '' ? '' : $base;

function href_base(string $baseHref, string $path): string {
    $path = ltrim($path, '/');
    return ($baseHref ? $baseHref . '/' : '/') . $path;
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Bootstrap Icons (stars, chat, etc.) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Public CSS -->
    <link rel="stylesheet" href="<?= href_base($baseHref, 'public/css/app.css') ?>">
</head>

<body class="fh-public-body">

<nav class="fh-topnav">
  <div class="fh-container fh-topnav__inner">
    <a class="fh-brand" href="<?= href_base($baseHref, '') ?>">FILMHUB</a>

    <div class="fh-navlinks">
      <a class="btn-pill" href="<?= href_base($baseHref, 'all') ?>"><i class="bi bi-grid"></i> Все фильмы</a>
      <a class="btn-pill" href="<?= href_base($baseHref, 'genres') ?>"><i class="bi bi-tags"></i> Жанры</a>

      <?php if ($isAuth): ?>
        <a class="btn-pill" href="<?= href_base($baseHref, 'myfavorites') ?>"><i class="bi bi-heart"></i> Избранное</a>
        <a class="btn-pill" href="<?= href_base($baseHref, 'profile') ?>"><i class="bi bi-person"></i> Профиль</a>
        <span class="fh-userpill">
          <i class="bi bi-dot"></i>
          <?= htmlspecialchars($login) ?> (<?= htmlspecialchars($role) ?>)
        </span>
        <a class="btn-pill btn-pill--danger" href="<?= href_base($baseHref, 'logout') ?>"><i class="bi bi-box-arrow-right"></i> Выйти</a>

        <?php if ($role === 'admin'): ?>
          <a class="btn-pill btn-pill--accent" href="<?= href_base($baseHref, 'admin/') ?>"><i class="bi bi-shield-lock"></i> Админка</a>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn-pill" href="<?= href_base($baseHref, 'loginForm') ?>"><i class="bi bi-box-arrow-in-right"></i> Вход</a>
        <a class="btn-pill btn-pill--accent" href="<?= href_base($baseHref, 'registerForm') ?>"><i class="bi bi-person-plus"></i> Регистрация</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="fh-container fh-main">
  <?php if (!empty($_SESSION['errorString'])): ?>
      <div class="fh-alert">
          <?= $_SESSION['errorString']; unset($_SESSION['errorString']); ?>
      </div>
  <?php endif; ?>

  <?= $content ?>
</main>

<footer class="fh-footer">
  <div class="fh-container">
    FilmHub • public
  </div>
</footer>

<script src="<?= href_base($baseHref, 'public/js/app.js') ?>"></script>
</body>
</html>
