<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Dashboard';
ob_start();

/**
 * Если контроллер не передал статистику — посчитаем прямо здесь.
 * Так dashboard будет всегда “живой”.
 */
$stats    = $stats ?? null;
$activity = $activity ?? null;
$admin    = $admin ?? null;

$hasDb = class_exists('Database');

function fh_col_exists(Database $db, string $table, string $col): bool {
  try {
    $row = $db->getOne(
      "SELECT 1 AS ok
       FROM INFORMATION_SCHEMA.COLUMNS
       WHERE TABLE_SCHEMA = DATABASE()
         AND TABLE_NAME = :t
         AND COLUMN_NAME = :c
       LIMIT 1",
      [':t' => $table, ':c' => $col]
    );
    return (bool)$row;
  } catch (Throwable $e) {
    return false;
  }
}

function fh_project_base(): string {
  $adminBase = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
  return preg_replace('~/admin$~', '', $adminBase) ?: '';
}

function fh_find_avatar_url(int $userId): array {
  $projectBase = fh_project_base();

  $projectRoot = realpath(__DIR__ . '/../../'); // ✅ корень проекта (FILMHUB)
  $imgDir      = $projectRoot ? realpath($projectRoot . '/img/users') : false; // ✅ правильная папка

  if (!$imgDir) return [null, null];

  foreach (["{$userId}.jpg","{$userId}.jpeg","{$userId}.png","{$userId}.webp"] as $f) {
    $abs = $imgDir . DIRECTORY_SEPARATOR . $f;
    if (is_file($abs)) return [$projectBase . '/img/users/' . $f, $abs];
  }
  return [null, null];
}


if ($stats === null && $hasDb) {
  try {
    $db = new Database();

    $stats = [
      'movies'     => (int)($db->getOne("SELECT COUNT(*) AS c FROM movies")['c'] ?? 0),
      'users'      => (int)($db->getOne("SELECT COUNT(*) AS c FROM users")['c'] ?? 0),
      'comments'   => (int)($db->getOne("SELECT COUNT(*) AS c FROM comments")['c'] ?? 0),
      'ratings'    => (int)($db->getOne("SELECT COUNT(*) AS c FROM ratings")['c'] ?? 0),
      'favorites'  => (int)($db->getOne("SELECT COUNT(*) AS c FROM favorites")['c'] ?? 0),
    ];

    // Активность — за последние 24 часа (если есть created_at)
    $activity = [
      'new_users_24h'    => null,
      'comments_24h'     => null,
      'ratings_24h'      => null,
      'favorites_24h'    => null,
      'last_comment_at'  => null,
      'last_user_at'     => null,
    ];

    if (fh_col_exists($db, 'users', 'created_at')) {
      $activity['new_users_24h'] = (int)($db->getOne(
        "SELECT COUNT(*) AS c FROM users WHERE created_at >= (NOW() - INTERVAL 1 DAY)"
      )['c'] ?? 0);
      $activity['last_user_at'] = (string)($db->getOne(
        "SELECT MAX(created_at) AS d FROM users"
      )['d'] ?? '');
    }

    if (fh_col_exists($db, 'comments', 'created_at')) {
      $activity['comments_24h'] = (int)($db->getOne(
        "SELECT COUNT(*) AS c FROM comments WHERE created_at >= (NOW() - INTERVAL 1 DAY)"
      )['c'] ?? 0);
      $activity['last_comment_at'] = (string)($db->getOne(
        "SELECT MAX(created_at) AS d FROM comments"
      )['d'] ?? '');
    }

    if (fh_col_exists($db, 'ratings', 'created_at')) {
      $activity['ratings_24h'] = (int)($db->getOne(
        "SELECT COUNT(*) AS c FROM ratings WHERE created_at >= (NOW() - INTERVAL 1 DAY)"
      )['c'] ?? 0);
    }

    if (fh_col_exists($db, 'favorites', 'created_at')) {
      $activity['favorites_24h'] = (int)($db->getOne(
        "SELECT COUNT(*) AS c FROM favorites WHERE created_at >= (NOW() - INTERVAL 1 DAY)"
      )['c'] ?? 0);
    }

  } catch (Throwable $e) {
    // Если БД временно недоступна — оставим всё безопасно пустым
    $stats = [];
    $activity = [];
  }
}

$stats = $stats ?? [];
$moviesCount    = $stats['movies']    ?? '—';
$usersCount     = $stats['users']     ?? '—';
$commentsCount  = $stats['comments']  ?? '—';
$ratingsCount   = $stats['ratings']   ?? '—';
$favoritesCount = $stats['favorites'] ?? '—';

// Профиль админа (из session)
$adminId    = (int)($_SESSION['user_id'] ?? 0);
$adminLogin = (string)($_SESSION['login'] ?? 'admin');
$adminRole  = (string)($_SESSION['role'] ?? 'admin');

/* ВОТ ЗДЕСЬ */
[$avatarUrl, $avatarAbs] = fh_find_avatar_url($adminId);

$hasAvatar = (bool)$avatarUrl;

// статус (пока простой)
$statusLabel = 'Online';
$statusBadge = 'text-bg-success';

// Активность: красиво и безопасно
$activity = $activity ?? [];
?>

<div class="mb-3">
  <h1 class="h4 mb-1">Dashboard</h1>
  <div class="text-muted">Быстрый обзор и действия</div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-md-6 col-xl-3">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Movies</div>
          <div class="fs-3 fw-bold"><?= h($moviesCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-camera-reels"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="moviesAdmin">Open Movies</a>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Users</div>
          <div class="fs-3 fw-bold"><?= h($usersCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-people"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="usersAdmin">Open Users</a>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Comments</div>
          <div class="fs-3 fw-bold"><?= h($commentsCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-chat-left-text"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="commentsAdmin">Open Comments</a>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Ratings</div>
          <div class="fs-3 fw-bold"><?= h($ratingsCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-star"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="ratingsAdmin">Open Ratings</a>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-7">
    <div class="fh-card p-4">
      <div class="fw-semibold mb-2"><i class="bi bi-lightning-charge me-1"></i> Быстрые действия</div>

      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-success" href="movieAdd"><i class="bi bi-plus-circle me-1"></i> Add movie</a>
        <a class="btn btn-outline-primary" href="genreAdd"><i class="bi bi-tag me-1"></i> Add genre</a>
        <a class="btn btn-outline-secondary" href="/filmhub/">
          <i class="bi bi-box-arrow-up-right me-1"></i> Open site
        </a>
      </div>

      <hr class="my-3">

      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <div class="fh-card p-3" style="box-shadow:none;">
            <div class="fw-semibold mb-2"><i class="bi bi-activity me-1"></i> Активность (24h)</div>

            <div class="d-flex justify-content-between">
              <div class="text-muted">New users</div>
              <div class="fw-semibold"><?= h($activity['new_users_24h'] ?? '—') ?></div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="text-muted">Comments</div>
              <div class="fw-semibold"><?= h($activity['comments_24h'] ?? '—') ?></div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="text-muted">Ratings</div>
              <div class="fw-semibold"><?= h($activity['ratings_24h'] ?? '—') ?></div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="text-muted">Favorites</div>
              <div class="fw-semibold"><?= h($activity['favorites_24h'] ?? '—') ?></div>
            </div>

            <hr class="my-2">

            <div class="text-muted small">
              Last user: <?= h($activity['last_user_at'] ?? '—') ?><br>
              Last comment: <?= h($activity['last_comment_at'] ?? '—') ?>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="fh-card p-3" style="box-shadow:none;">
            <div class="fw-semibold mb-2"><i class="bi bi-person-badge me-1"></i> Admin profile</div>

            <div class="d-flex align-items-center gap-3">
              <div class="fh-avatar">
                <?php if ($avatarUrl): ?>
                  <img src="<?= h($avatarUrl) ?>" alt="Admin avatar">
                <?php else: ?>
                  <div class="fh-avatar__ph"><i class="bi bi-person"></i></div>
                <?php endif; ?>
              </div>

              <div class="flex-grow-1">
                <div class="fw-bold"><?= h($adminLogin) ?></div>
                <div class="text-muted small">Role: <?= h($adminRole) ?></div>
                <div class="mt-1">
                  <span class="badge <?= h($statusBadge) ?>"><?= h($statusLabel) ?></span>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-3">
              <?php if ($hasAvatar): ?>
                <a
                  href="adminAvatarDelete"
                  class="btn btn-sm btn-outline-danger"
                  data-confirm="delete"
                  data-title="Удалить аватар?"
                  data-text="Аватар администратора будет удалён (файл)."
                >
                  <i class="bi bi-trash3 me-1"></i>
                </a>
              <?php else: ?>
                <button class="btn btn-sm btn-outline-secondary" disabled>
                  <i class="bi bi-image me-1"></i> No avatar
                </button>
              <?php endif; ?>
            </div>

            <div class="text-muted small mt-2">
              Аватар берётся из <code>/img/users/</code> (например <code><?= h($adminId) ?>.png</code> или <code>admin.png</code>).
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="fh-card p-4">
      <div class="fw-semibold mb-2"><i class="bi bi-heart me-1"></i> Favorites (всего)</div>
      <div class="fs-3 fw-bold"><?= h($favoritesCount) ?></div>
      <div class="text-muted small">Показывает активность пользователей</div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="favoritesAdmin">Open Favorites</a>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
