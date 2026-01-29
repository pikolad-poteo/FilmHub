<?php
// admin/viewAdmin/userAvatarForm.php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'User avatar';
ob_start();

/**
 * Совместимость: контроллер мог передать $u или $user или $arr
 */
$user = $u ?? ($user ?? ($arr ?? []));
if ($user instanceof Traversable) {
  $tmp = [];
  foreach ($user as $x) $tmp[] = $x;
  $user = $tmp;
}
if (!is_array($user)) $user = [];

$id    = (int)($user['id'] ?? ($_GET['id'] ?? 0));
$login = (string)($user['login'] ?? '');
$role  = (string)($user['role'] ?? 'user');

/**
 * projectBase (без хардкода /filmhub)
 */
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /.../admin
$projectBase = preg_replace('~/admin$~', '', $adminBase) ?: ''; // /...

/**
 * avatarUrl:
 * - если контроллер уже передал $avatarUrl — используем его
 * - иначе строим сами через user_avatar_url()
 */
if (!isset($avatarUrl) || !is_string($avatarUrl)) {
  $avatarUrl = user_avatar_url($user + ['id' => $id], $projectBase);
}
$avatarUrl = trim((string)$avatarUrl);
$hasAvatar = ($avatarUrl !== '');
?>

<div class="mb-3">
  <h1 class="h4 mb-1">User avatar</h1>
  <div class="text-muted">Manage avatar for user: <span class="fw-semibold"><?= h($login) ?></span></div>
</div>

<div class="row g-3 align-items-stretch">

  <!-- LEFT: current avatar -->
  <div class="col-12 col-lg-5">
    <div class="fh-card p-4 fh-avatar-card h-100">
      <div class="fw-semibold mb-3">Current avatar</div>

      <div class="d-flex align-items-center gap-3">
        <div class="fh-avatar fh-avatar-lg">
          <?php if ($hasAvatar): ?>
            <img src="<?= h($avatarUrl) ?>" alt="User avatar">
          <?php else: ?>
            <div class="fh-avatar__ph"><i class="bi bi-person"></i></div>
          <?php endif; ?>
        </div>

        <div class="flex-grow-1">

          <?php
            // Нормализуем логин: убираем пробелы и "псевдо-плейсхолдеры"
            $loginNorm = trim((string)($login ?? ''));

            // Иногда логин “по умолчанию” кладут как '—' или '-' — считаем это пустым
            if (in_array($loginNorm, ['—', '-', '–', '— —', ''], true)) {
              $loginNorm = '';
            }
          ?>

          <div class="fw-bold">
            <?= $loginNorm !== '' ? h($loginNorm) : 'ID:' . (int)$id ?>
          </div>

          <div class="text-muted small mt-2">
            Хранится в <code>/img/users/</code> как <code>u<?= h((string)$id) ?>_TIMESTAMP.jpg/png/webp</code>
          </div>

          <div class="d-flex gap-2 mt-2">
            <a
              class="btn btn-sm btn-outline-danger"
              href="userAvatarDelete?id=<?= (int)$id ?>"
              data-confirm="delete"
              data-title="Удалить аватар?"
              data-text="Аватар пользователя будет удалён. Действие нельзя отменить."
              title="Delete avatar"
              aria-label="Delete avatar"
            >
              <i class="bi bi-trash3"></i>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT: upload -->
  <div class="col-12 col-lg-7">
    <div class="fh-card p-4 fh-avatar-upload h-100">
      <div class="fw-semibold mb-2">Upload new avatar</div>

      <form action="userAvatarUpdate?id=<?= (int)$id ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Choose image (JPG/PNG/WebP, max 2MB)</label>
          <input class="form-control" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
        </div>

        <button class="btn btn-success" type="submit">
          <i class="bi bi-upload me-1"></i> Save
        </button>
      </form>

      <div class="text-muted small mt-3">
        После сохранения файл будет лежать в <code>/img/users/</code> и автоматически заменит старый.
      </div>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
