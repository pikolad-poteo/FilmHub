<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Dashboard';
ob_start();

/**
 * Если контроллер не передал статистику — посчитаем прямо здесь.
 * Dashboard остаётся “живым”.
 */
$stats    = $stats ?? null;
$activity = $activity ?? null;

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

if ($stats === null && $hasDb) {
  try {
    $db = new Database();

    $stats = [
      'movies'     => (int)($db->getOne("SELECT COUNT(*) AS c FROM movies")['c'] ?? 0),
      'genres'     => (int)($db->getOne("SELECT COUNT(*) AS c FROM genres")['c'] ?? 0),
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
    $stats = [];
    $activity = [];
  }
}

$stats = $stats ?? [];
$moviesCount    = $stats['movies']    ?? '—';
$genresCount    = $stats['genres']    ?? '—';
$usersCount     = $stats['users']     ?? '—';
$commentsCount  = $stats['comments']  ?? '—';
$ratingsCount   = $stats['ratings']   ?? '—';
$favoritesCount = $stats['favorites'] ?? '—';

// Профиль админа (из session)
$adminId    = (int)($_SESSION['user_id'] ?? 0);
$adminLogin = (string)($_SESSION['login'] ?? 'admin');
$adminRole  = (string)($_SESSION['role'] ?? 'admin');

// Аватар админа ТОЛЬКО из БД (если NULL/'' -> покажем иконку)
$projectBase = fh_project_base();

$avatarUrl = '';
$hasAvatar = false;

if ($hasDb && $adminId > 0) {
  try {
    $db2 = $db ?? new Database();
    $row = $db2->getOne("SELECT avatar FROM users WHERE id = :id LIMIT 1", [':id' => $adminId]);
    $adminAvatar = trim((string)($row['avatar'] ?? ''));
    $avatarUrl   = user_avatar_url(['avatar' => $adminAvatar], $projectBase);
    $hasAvatar   = ($avatarUrl !== '');
  } catch (Throwable $e) {
    $avatarUrl = '';
    $hasAvatar = false;
  }
}

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

<!-- TOP STATS: 6 cards in one row on XL -->
<div class="row g-3 mb-3">
  <div class="col-12 col-md-6 col-xl-2">
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

  <div class="col-12 col-md-6 col-xl-2">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Genres</div>
          <div class="fs-3 fw-bold"><?= h($genresCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-tags"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="genresAdmin">Open Genres</a>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-2">
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

  <div class="col-12 col-md-6 col-xl-2">
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

  <div class="col-12 col-md-6 col-xl-2">
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

  <div class="col-12 col-md-6 col-xl-2">
    <div class="fh-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-muted small">Favorites</div>
          <div class="fs-3 fw-bold"><?= h($favoritesCount) ?></div>
        </div>
        <div class="fh-logo" style="width:44px;height:44px;border-radius:14px;">
          <i class="bi bi-heart"></i>
        </div>
      </div>
      <a class="btn btn-sm btn-outline-primary mt-3 w-100" href="favoritesAdmin">Open Favorites</a>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-12">
    <div class="fh-card p-4">
      <div class="fw-semibold mb-2"><i class="bi bi-lightning-charge me-1"></i> Быстрые действия</div>

      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-success" href="movieAdd"><i class="bi bi-plus-circle me-1"></i> Add movie</a>
        <a class="btn btn-outline-primary" href="genreAdd"><i class="bi bi-tag me-1"></i> Add genre</a>
        <a class="btn btn-outline-secondary" href="<?= h(($projectBase ?: '')) ?>/">
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
                <?php if ($hasAvatar): ?>
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
              <a class="btn btn-sm btn-outline-primary" href="usersAdmin" title="Edit users">
                <i class="bi bi-pencil-square"></i>
              </a>

              <?php if ($hasAvatar): ?>
                <a
                  href="adminAvatarDelete"
                  class="btn btn-sm btn-outline-danger"
                  data-confirm="delete"
                  data-title="Удалить аватар?"
                  data-text="В БД будет сброшено поле avatar. Если файл есть на диске — удаляйте отдельно."
                  title="Delete avatar"
                >
                  <i class="bi bi-trash3"></i>
                </a>
              <?php else: ?>
                <button class="btn btn-sm btn-outline-secondary" disabled title="No avatar">
                  <i class="bi bi-image"></i>
                </button>
              <?php endif; ?>
            </div>

            <div class="text-muted small mt-2">
              Аватар берётся <b>только из БД</b> (поле <code>users.avatar</code>). Если там <code>NULL</code> — показывается иконка.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
