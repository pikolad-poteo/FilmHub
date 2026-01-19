<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle = $pageTitle ?? 'Admin';
$content   = $content ?? '';

/**
 * Определяем базовые пути так, чтобы работало и в корне домена, и в подпапке.
 * SCRIPT_NAME обычно вида: /filmhub/admin/index.php или /admin/index.php
 */
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$siteBase  = preg_replace('~/admin$~', '', $scriptDir); // /filmhub или ''
if ($siteBase === '') $siteBase = ''; // чтобы "/img/.." не превратилось в "//img/.."

$adminBase = $scriptDir; // /filmhub/admin

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function site_url(string $path = ''): string {
  global $siteBase;
  $path = ltrim($path, '/');
  return $siteBase . ($path ? '/' . $path : '/');
}
function admin_url(string $path = ''): string {
  global $adminBase;
  $path = ltrim($path, '/');
  // В твоём роутинге админки ссылки выглядят как "moviesAdmin", "dashboard" и т.д.
  // Оставляем относительные ссылки, но на случай необходимости даём helper.
  return $path ?: '';
}

/** Определяем текущий маршрут для подсветки меню */
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$uriPath = trim($uriPath, '/');
$current = $uriPath ? basename($uriPath) : '';

$isAdmin = !empty($_SESSION['is_admin']);

$nav = [
  ['key' => 'dashboard',      'title' => 'Dashboard',  'icon' => 'speedometer2', 'href' => 'dashboard'],
  ['key' => 'moviesAdmin',    'title' => 'Movies',     'icon' => 'film',         'href' => 'moviesAdmin'],
  ['key' => 'genresAdmin',    'title' => 'Genres',     'icon' => 'tags',         'href' => 'genresAdmin'],
  ['key' => 'usersAdmin',     'title' => 'Users',      'icon' => 'people',       'href' => 'usersAdmin'],
  ['key' => 'commentsAdmin',  'title' => 'Comments',   'icon' => 'chat-left-text','href'=> 'commentsAdmin'],
  ['key' => 'favoritesAdmin', 'title' => 'Favorites',  'icon' => 'heart',        'href' => 'favoritesAdmin'],
  ['key' => 'ratingsAdmin',   'title' => 'Ratings',    'icon' => 'star',         'href' => 'ratingsAdmin'],
];

$flashError   = $_SESSION['errorString']   ?? '';
$flashSuccess = $_SESSION['successString'] ?? ''; // если ты где-то захочешь добавить successString
unset($_SESSION['errorString'], $_SESSION['successString']);
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?> — FilmHub Admin</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Admin CSS (создай файл ниже) -->
  <link rel="stylesheet" href="<?= h(site_url('admin/public/css/admin.css')) ?>">

</head>
<body class="fh-admin-body">

<?php if ($isAdmin): ?>
  <div class="fh-admin">
    <!-- Sidebar -->
    <aside class="fh-sidebar">
      <div class="fh-sidebar__brand">
        <div class="fh-logo">
          <i class="bi bi-film"></i>
        </div>
        <div>
          <div class="fh-brand-title">FilmHub</div>
          <div class="fh-brand-sub">Admin panel</div>
        </div>
      </div>

      <nav class="fh-nav">
        <?php foreach ($nav as $item): 
          $active = (strpos($_SERVER['REQUEST_URI'] ?? '', $item['href']) !== false) ? 'active' : '';
        ?>
          <a class="fh-nav__link <?= $active ?>" href="<?= h($item['href']) ?>">
            <i class="bi bi-<?= h($item['icon']) ?>"></i>
            <span><?= h($item['title']) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="fh-sidebar__footer">
        <a class="btn btn-sm btn-outline-light w-100 mb-2" href="<?= h(site_url('index.php')) ?>">
          <i class="bi bi-box-arrow-up-right me-1"></i> На сайт
        </a>
        <a class="btn btn-sm btn-danger w-100" href="logout">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </aside>

    <!-- Main -->
    <main class="fh-main">
      <!-- Topbar -->
      <header class="fh-topbar">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="fhSidebarToggle">
          <i class="bi bi-list"></i>
        </button>

        <div class="fh-topbar__title">
          <div class="fw-semibold"><?= h($pageTitle) ?></div>
          <div class="text-muted small">Управление контентом FilmHub</div>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
          <span class="badge text-bg-dark-subtle border">
            <i class="bi bi-shield-lock me-1"></i> Admin
          </span>
        </div>
      </header>

      <section class="fh-content container-fluid">
        <?php if (!empty($flashError)): ?>
          <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div><?= $flashError ?></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($flashSuccess)): ?>
          <div class="alert alert-success d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-check-circle-fill mt-1"></i>
            <div><?= $flashSuccess ?></div>
          </div>
        <?php endif; ?>

        <?= $content ?>
      </section>

      <footer class="fh-footer">
        <span class="text-muted small">FilmHub Admin • UI v1</span>
      </footer>
    </main>
  </div>

<?php else: ?>
  <!-- Если не админ: аккуратная “обложка” (например login) -->
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">
        <?php if (!empty($flashError)): ?>
          <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div><?= $flashError ?></div>
          </div>
        <?php endif; ?>

        <?= $content ?>

        <div class="text-center text-muted small mt-3">
          FilmHub Admin
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Toast container (если позже захочешь делать уведомления) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="fhToastContainer"></div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Admin JS (создай файл ниже) -->
<script src="<?= h(site_url('admin/public/js/admin.js')) ?>"></script>

</body>
</html>
