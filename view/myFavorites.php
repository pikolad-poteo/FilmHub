<?php
$pageTitle = 'Моё избранное';

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
<h1>Моё избранное</h1>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
  <div class="movie-grid">
    <?php foreach ($arr as $m): ?>
      <?php
        $id = (int)($m['id'] ?? 0);
        $title = (string)($m['title'] ?? '');
        $year = !empty($m['year']) ? (int)$m['year'] : null;
        $genre = (string)($m['genre_name'] ?? '');
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
          <div class="movie-sub">
            <?= $year ? $year : '-' ?>
            <?= $genre ? ' • ' . htmlspecialchars($genre) : '' ?>
          </div>

          <div class="movie-meta">
            <span><i class="bi bi-star-fill"></i> <?= ($avg === null) ? '-' : htmlspecialchars((string)$avg) ?></span>
            <span><i class="bi bi-chat-dots"></i> <?= (int)$commentsCount ?></span>
          </div>

          <a class="btn-link" href="movie?id=<?= $id ?>"><i class="bi bi-play-circle"></i> Смотреть</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
