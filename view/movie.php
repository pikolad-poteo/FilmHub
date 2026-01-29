<?php
$pageTitle = 'Фильм';

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
<?php if (empty($m)): ?>
  <h1>Фильм не найден</h1>
<?php else: ?>
  <?php
    $id = (int)($m['id'] ?? 0);
    $title = (string)($m['title'] ?? '');
    $posterUrl = movie_poster_url($m['poster'] ?? '', $baseHref);
    $yt = (string)($m['youtube_trailer_id'] ?? '');
    $avg = $ratingStats['rating_avg'] ?? null;
    $cnt = $ratingStats['rating_count'] ?? 0;
  ?>

  <div class="movie-page"><div>

  <!-- info -->
  <div>
    <h1 style="margin:0 0 12px;"><?= htmlspecialchars($title) ?></h1>

    <div class="movie-hero">
      <?php if ($posterUrl): ?>
        <img class="poster-lg" src="<?= htmlspecialchars($posterUrl) ?>" alt="<?= htmlspecialchars($title) ?>">
      <?php endif; ?>

      <div style="min-width:0;">
        <div class="movie-meta" style="margin-bottom:10px;">
          <span><i class="bi bi-star-fill"></i> <?= ($avg === null) ? '-' : htmlspecialchars((string)$avg) ?></span>
          <span><i class="bi bi-people"></i> <?= (int)$cnt ?></span>
        </div>

        <p style="margin:0;">
          <b>Оригинальное название:</b> <?= htmlspecialchars($m['original_title'] ?? '-') ?><br>
          <b>Год:</b> <?= !empty($m['year']) ? (int)$m['year'] : '-' ?><br>
          <b>Длительность:</b> <?= !empty($m['duration_minutes']) ? (int)$m['duration_minutes'] . ' мин.' : '-' ?><br>
          <b>Страна:</b> <?= htmlspecialchars($m['country'] ?? '-') ?><br>
          <b>Режиссёр:</b> <?= htmlspecialchars($m['director'] ?? '-') ?><br>
          <b>Жанр:</b> <?= htmlspecialchars($m['genre_name'] ?? '-') ?><br>
        </p>

        <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <a class="btn-pill btn-pill--accent" href="favorite?id=<?= $id ?>">
              <i class="bi bi-heart"></i>
              <?= !empty($isFav) ? 'Убрать из избранного' : 'В избранное' ?>
            </a>
          <?php else: ?>
            <a class="btn-pill" href="loginForm"><i class="bi bi-box-arrow-in-right"></i> Войти для избранного</a>
          <?php endif; ?>

          <a class="btn-pill" href="all"><i class="bi bi-arrow-left"></i> Назад к каталогу</a>
        </div>
      </div>
    </div>

    <?php if (!empty($m['description'])): ?>
      <div class="block">
        <h2>Описание</h2>
        <p><?= nl2br(htmlspecialchars($m['description'])) ?></p>
      </div>
    <?php endif; ?>

    <!-- trailer -->
    <div class="block">
      <h2>Трейлер</h2>
      <?php if ($yt !== ''): ?>
        <div class="movie-trailer" style="position:static; top:auto;">
          <iframe
            src="https://www.youtube.com/embed/<?= htmlspecialchars($yt) ?>"
            title="YouTube trailer"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
          ></iframe>
        </div>
      <?php else: ?>
        <p>Трейлер не указан.</p>
      <?php endif; ?>
    </div>

    <div class="block" id="rating">
      <h2>Оценка</h2>

      <p style="margin:0 0 12px;">
        <b>Средний:</b> <?= ($avg === null) ? '-' : htmlspecialchars((string)$avg) ?>
        &nbsp;&nbsp; <b>Оценок:</b> <?= (int)$cnt ?>
      </p>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <p style="margin:0 0 10px;"><b>Ваша оценка:</b> <?= ($myRating === null) ? 'нет' : (int)$myRating ?></p>

        <div style="display:flex; flex-wrap:wrap; gap:8px;">
          <?php for ($i=1; $i<=10; $i++): ?>
            <?php $cls = 'btn-pill' . (($myRating !== null && (int)$myRating === $i) ? ' is-selected' : ''); ?>
            <a class="<?= $cls ?>" href="rate?id=<?= $id ?>&rating=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      <?php else: ?>
        <p><a class="btn-pill" href="loginForm"><i class="bi bi-box-arrow-in-right"></i> Войдите, чтобы поставить оценку</a></p>
      <?php endif; ?>
    </div>

    <div class="block" id="ctable">
      <h2>Комментарии</h2>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <form method="get" action="insertcomment">
          <input type="hidden" name="id" value="<?= $id ?>">
          <textarea name="comment" rows="3" placeholder="Ваш комментарий..."></textarea>
          <div style="margin-top:10px;">
            <button type="submit"><i class="bi bi-send"></i> Отправить</button>
          </div>
        </form>
      <?php else: ?>
        <p><a class="btn-pill" href="loginForm"><i class="bi bi-box-arrow-in-right"></i> Войдите, чтобы писать комментарии</a></p>
      <?php endif; ?>

      <?php if (empty($comments)): ?>
        <p style="margin-top:12px;">Комментариев пока нет.</p>
      <?php else: ?>
        <ul class="comment-list">
          <?php foreach ($comments as $c): ?>
            <li class="comment-item">
              <b><?= htmlspecialchars($c['user_login'] ?? 'user') ?>:</b>
              <?= nl2br(htmlspecialchars($c['text'] ?? '')) ?>
              <div><small><?= htmlspecialchars($c['created_at'] ?? '') ?></small></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
