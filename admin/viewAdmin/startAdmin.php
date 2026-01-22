<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Dashboard';
ob_start();

$stats = $stats ?? [];
$moviesCount    = $stats['movies']    ?? '—';
$usersCount     = $stats['users']     ?? '—';
$commentsCount  = $stats['comments']  ?? '—';
$ratingsCount   = $stats['ratings']   ?? '—';
$favoritesCount = $stats['favorites'] ?? '—';
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

      <div class="text-muted">
        На дашборде можно позже вывести последние добавленные фильмы и последние комментарии.
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
