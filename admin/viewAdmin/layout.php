<?php
// admin/viewAdmin/layout.php

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// helper: escape html (declare ONCE)
if (!function_exists('h')) {
  function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

$pageTitle = $pageTitle ?? 'Admin Panel';
$content   = $content ?? '<div class="alert alert-warning">No content</div>';

// active menu helper
$path = $_SERVER['REQUEST_URI'] ?? '';
function isActive(string $needle, string $path): string {
  return (strpos($path, $needle) !== false) ? 'active' : '';
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title><?= h($pageTitle) ?> — FilmHub Admin</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Your Admin CSS -->
  <link href="/filmhub/admin/public/css/admin.css" rel="stylesheet">
</head>

<body class="fh-admin-body">
  <div class="fh-admin">

    <div class="fh-sidebar-backdrop"></div>

    <aside class="fh-sidebar">
      <div class="fh-sidebar__brand">
        <div class="fh-logo"><i class="bi bi-film"></i></div>
        <div>
          <div class="fh-brand-title">FilmHub</div>
          <div class="fh-brand-sub">Admin Panel</div>
        </div>
      </div>

      <nav class="fh-nav">
        <a class="fh-nav__link <?= isActive('/admin', $path) && (strpos($path,'movies')===false && strpos($path,'users')===false && strpos($path,'genres')===false) ? 'active' : '' ?>" href="/filmhub/admin/">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a class="fh-nav__link <?= isActive('moviesAdmin', $path) ? 'active' : '' ?>" href="moviesAdmin">
          <i class="bi bi-camera-reels"></i> Movies
        </a>

        <a class="fh-nav__link <?= isActive('genresAdmin', $path) ? 'active' : '' ?>" href="genresAdmin">
          <i class="bi bi-tags"></i> Genres
        </a>

        <a class="fh-nav__link <?= isActive('usersAdmin', $path) ? 'active' : '' ?>" href="usersAdmin">
          <i class="bi bi-people"></i> Users
        </a>

        <a class="fh-nav__link <?= isActive('commentsAdmin', $path) ? 'active' : '' ?>" href="commentsAdmin">
          <i class="bi bi-chat-left-text"></i> Comments
        </a>

        <a class="fh-nav__link <?= isActive('favoritesAdmin', $path) ? 'active' : '' ?>" href="favoritesAdmin">
          <i class="bi bi-heart"></i> Favorites
        </a>

        <a class="fh-nav__link <?= isActive('ratingsAdmin', $path) ? 'active' : '' ?>" href="ratingsAdmin">
          <i class="bi bi-star"></i> Ratings
        </a>

        <hr>

        <a class="fh-nav__link" href="/filmhub/" title="На сайт">
          <i class="bi bi-box-arrow-up-right"></i> На сайт
        </a>

        <a class="fh-nav__link" href="/filmhub/logout" title="Выйти">
          <i class="bi bi-box-arrow-left"></i> Logout
        </a>
      </nav>
    </aside>

    <main class="fh-main">
      <div class="fh-topbar">
        <button class="btn btn-outline-light d-lg-none" id="fhSidebarToggle">
          <i class="bi bi-list"></i>
        </button>

        <div class="fw-semibold"><?= h($pageTitle) ?></div>

        <div class="ms-auto d-flex gap-2">
          <span class="badge text-bg-secondary">
            <?= h($_SESSION['user']['login'] ?? 'admin') ?>
          </span>
        </div>
      </div>

      <section class="fh-content">
        <?= $content ?>
      </section>

      <footer class="fh-footer text-muted small">
        © <?= date('Y') ?> FilmHub Admin
      </footer>
    </main>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Your Admin JS -->
  <script src="/filmhub/admin/public/js/admin.js"></script>
</body>
</html>
