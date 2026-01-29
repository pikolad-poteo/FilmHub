<?php
$pageTitle = 'Жанры';

require_once __DIR__ . '/../model/CommentModel.php';

$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$base = rtrim($base, '/');
$baseHref = $base === '' ? '' : $base;

function abs_asset(string $baseHref, string $path): string {
  $path = trim((string)$path);
  if ($path === '') return '';
  if (preg_match('~^https?://~i', $path)) return $path;
  if ($path[0] === '/') return $path;
  return ($baseHref ? $baseHref . '/' : '/') . $path;
}

ob_start();
?>
<h1>Жанры</h1>

<?php if (!empty($genre) && is_array($genre)): ?>
  <h2 style="margin-top:10px;">Фильмы жанра: <?= htmlspecialchars($genre['name'] ?? '') ?></h2>
<?php endif; ?>

<?php if (empty($arr)): ?>
  <p>Нет данных.</p>
<?php else: ?>
  <?php
    // Если в $arr есть поле "slug" — это жанры, иначе — фильмы
    $isGenresList = isset($arr[0]['slug']);
  ?>

  <?php if ($isGenresList): ?>
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:14px;">
      <?php foreach ($arr as $g): ?>
        <a class="btn-pill" href="genre?id=<?= (int)$g['id'] ?>">
          <i class="bi bi-tag"></i> <?= htmlspecialchars($g['name'] ?? '') ?>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="movie-grid" style="margin-top:14px;">
      <?php foreach ($arr as $m): ?>
        <?php
          $id = (int)($m['id'] ?? 0);
          $title = (string)($m['title'] ?? '');
          $year = !empty($m['year']) ? (int)$m['year'] : null;
          $posterUrl = movie_poster_url($m['poster'] ?? '', $baseHref);
          $avg = $m['rating_avg'] ?? null;
          $commentsCount = $id ? CommentModel::getCommentCountByMovieID($id) : 0;
        ?>
        <div class="movie-card">
          <div class="movie-poster">
            <?php if ($posterUrl): ?>
              <img src="<?= htmlspecialchars($posterUrl) ?>" alt="<?= htmlspecialchars($title) ?>">
            <?php endif; ?>
          </div>

          <div class="movie-body">
            <h3 class="movie-title"><?= htmlspecialchars($title ?: 'Без названия') ?></h3>
            <div class="movie-sub"><?= $year ? $year : '-' ?></div>

            <div class="movie-meta">
              <span><i class="bi bi-star-fill"></i> <?= ($avg === null) ? '-' : htmlspecialchars((string)$avg) ?></span>
              <span><i class="bi bi-chat-dots"></i> <?= (int)$commentsCount ?></span>
            </div>

            <a class="btn-link" href="movie?id=<?= $id ?>"><i class="bi bi-play-circle"></i> Смотреть</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="margin-top:20px;">
      <a class="btn-pill" href="genres"><i class="bi bi-arrow-left"></i> Ко всем жанрам</a>
    </div>
  <?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
