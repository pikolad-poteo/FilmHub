<?php
$pageTitle = 'Профиль';

$user      = $user ?? null;
$favorites = $favorites ?? [];
$avatarUrl = $avatarUrl ?? '';

if (!$user) {
  $content = "<p>Пользователь не найден</p>";
  require __DIR__ . '/templates/layout.php';
  exit;
}

// base для корректных ссылок на ассеты/скрипты
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

<h1 style="margin:0 0 14px;">Профиль</h1>

<div class="profile-layout">

  <!-- LEFT: favorites -->
  <section class="block">
    <div class="profile-head">
      <h2 style="margin:0;">Избранные фильмы</h2>
      <span class="profile-badge">
        <i class="bi bi-heart"></i>
        <?= (int)count($favorites) ?>
      </span>
    </div>

    <?php if (empty($favorites)): ?>
      <p style="margin-top:12px;">Пока пусто. Перейди в каталог и добавь фильмы в избранное.</p>
      <a class="btn-pill btn-pill--accent" href="all"><i class="bi bi-grid"></i> Открыть каталог</a>
    <?php else: ?>
      <div class="movie-grid" style="margin-top:14px;">
        <?php foreach ($favorites as $m): ?>
          <?php
            $mid   = (int)($m['id'] ?? 0);
            $title = (string)($m['title'] ?? '');
            $year  = !empty($m['year']) ? (int)$m['year'] : null;
            $genre = (string)($m['genre_name'] ?? '');
            $posterPath = (string)($m['poster'] ?? '');
            $posterUrl = movie_poster_url($m['poster'] ?? '', $baseHref);
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

              <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:6px;">
                <a class="btn-link" href="movie?id=<?= $mid ?>"><i class="bi bi-play-circle"></i> Открыть</a>
                <a class="btn-pill" href="favorite?id=<?= $mid ?>"><i class="bi bi-heartbreak"></i> Убрать</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- RIGHT: account -->
  <aside class="profile-side">

    <!-- avatar -->
    <section class="block">
      <div class="profile-head">
        <h2 style="margin:0;">Аватар</h2>
        <span class="profile-badge">
          <i class="bi bi-person-circle"></i>
          <?= htmlspecialchars((string)($user['login'] ?? '')) ?>
        </span>
      </div>

      <div class="profile-avatar">
        <img id="avatarPreview" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar">
      </div>

      <form id="avatarForm" method="post" action="profileAvatar" enctype="multipart/form-data" style="margin-top:12px;">
        <label for="avatarInput">Выберите изображение</label>
        <input id="avatarInput" type="file" name="avatar" accept="image/*">

        <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
          <button type="submit"><i class="bi bi-upload"></i> Загрузить</button>

          <?php if (!empty($user['avatar'])): ?>
            <form method="post" action="profileAvatarDelete"
                  onsubmit="return confirm('Удалить текущий аватар и поставить дефолтный?');">
              <button type="submit" class="btn-danger"><i class="bi bi-trash"></i> Удалить</button>
            </form>
          <?php endif; ?>
        </div>

        <div class="profile-help">
          Изображение будет обрезано в браузере до 256×256 и отправлено на сервер.
        </div>
      </form>
    </section>

    <!-- profile data -->
    <section class="block">
      <div class="profile-head">
        <h2 style="margin:0;">Данные профиля</h2>
      </div>

      <form method="post" action="profileUpdate">
        <div class="profile-field">
          <label>Логин</label>
          <input type="text" name="login" value="<?= htmlspecialchars((string)($user['login'] ?? '')) ?>">
        </div>

        <div class="profile-field">
          <label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars((string)($user['email'] ?? '')) ?>">
        </div>

        <div style="margin-top:10px;">
          <button type="submit"><i class="bi bi-check2-circle"></i> Сохранить</button>
        </div>
      </form>
    </section>

    <!-- danger zone -->
    <section class="block profile-danger">
      <div class="profile-head">
        <h2 style="margin:0;">Опасная зона</h2>
      </div>

      <p class="profile-danger-text">
        Удаление аккаунта необратимо. Все ваши данные будут удалены.
      </p>

      <form method="post" action="profileDelete"
            onsubmit="return confirm('Точно удалить аккаунт? Это действие необратимо.');">
        <div class="profile-field">
          <label>Подтверждение</label>
          <input type="text" name="confirm" placeholder="Введите DELETE">
        </div>

        <div style="margin-top:10px;">
          <button type="submit" class="btn-danger"><i class="bi bi-trash3"></i> Удалить аккаунт</button>
        </div>
      </form>
    </section>

    <!-- profile.js (avatar upload) -->
    <script src="<?= ($baseHref ? $baseHref . '/' : '/') ?>public/js/profile.js"></script>
  </aside>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
