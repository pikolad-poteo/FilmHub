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

// base для корректных ссылок в подпапке (/filmhub)
$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$base = rtrim($base, '/');
$baseHref = $base === '' ? '' : $base;

// абсолютный корень проекта для action/formaction
$root = ($baseHref ? $baseHref . '/' : '/');

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
      <a class="btn-pill btn-pill--accent" href="<?= $root ?>all"><i class="bi bi-grid"></i> Открыть каталог</a>
    <?php else: ?>
      <div class="movie-grid" style="margin-top:14px;">
        <?php foreach ($favorites as $m): ?>
          <?php
            $mid   = (int)($m['id'] ?? 0);
            $title = (string)($m['title'] ?? '');
            $year  = !empty($m['year']) ? (int)$m['year'] : null;
            $genre = (string)($m['genre_name'] ?? '');
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

              <div class="movie-actions">
                <form method="post" action="<?= $root ?>favoriteToggle">
                  <input type="hidden" name="movie_id" value="<?= $mid ?>">
                  <input type="hidden" name="mode" value="remove">
                  <input type="hidden" name="return" value="profile">
                  <button type="submit" class="btn-pill">
                    <i class="bi bi-heartbreak"></i> Убрать
                  </button>
                </form>
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
        <img
          id="avatarPreview"
          src="<?= htmlspecialchars($avatarUrl) ?>"
          alt="Avatar"
          <?= $avatarUrl ? '' : 'style="display:none;"' ?>
          onerror="this.style.display='none'; document.getElementById('avatarPlaceholder').style.display='flex';"
        >

        <div
          id="avatarPlaceholder"
          class="profile-avatar__ph"
          style="<?= $avatarUrl ? 'display:none;' : 'display:flex;' ?>"
        >
          <i class="bi bi-person"></i>
        </div>
      </div>


      <form id="avatarForm" method="post" action="<?= $root ?>profileAvatar" enctype="multipart/form-data" style="margin-top:12px;">

        <div class="fh-file">
          <label class="fh-file-btn" for="avatarInput">
            <i class="bi bi-image"></i> Добавить фото
          </label>

          <input id="avatarInput" type="file" name="avatar" accept="image/*">
          <span id="avatarFileName" class="fh-file-name">Файл не выбран</span>
        </div>

        <div class="fh-actions">
          <button type="submit" class="btn-pill btn-pill--accent">
            <i class="bi bi-upload"></i> Загрузить
          </button>

          <?php if (!empty($user['avatar'])): ?>
            <button
              type="submit"
              class="btn-danger"
              formaction="<?= $root ?>profileAvatarDelete"
              formmethod="post"
              onclick="return confirm('Удалить текущий аватар и поставить дефолтный?');"
              name="delete_avatar"
              value="1"
            >
              <i class="bi bi-trash"></i> Удалить
            </button>
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

      <form method="post" action="<?= $root ?>profileUpdate">
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

      <form method="post" action="<?= $root ?>profileDelete"
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
    <script src="<?= $root ?>public/js/profile.js"></script>

    <script>
      (function () {
        const input = document.getElementById('avatarInput');
        const name  = document.getElementById('avatarFileName');
        if (!input || !name) return;

        input.addEventListener('change', () => {
          const f = input.files && input.files[0];
          name.textContent = f ? f.name : 'Файл не выбран';
        });
      })();
    </script>

  </aside>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
