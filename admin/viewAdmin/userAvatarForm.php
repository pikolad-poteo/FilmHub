<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'User avatar';
ob_start();

$u = $u ?? [];
$id = (int)($u['id'] ?? 0);
$login = (string)($u['login'] ?? '');
$email = (string)($u['email'] ?? '');
$avatarUrl = (string)($avatarUrl ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Avatar</h1>
    <div class="text-muted small">
      User #<?= $id ?> — <strong><?= h($login) ?></strong> (<?= h($email) ?>)
    </div>
  </div>
  <a class="btn btn-sm btn-outline-secondary" href="usersAdmin">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="fh-card p-4">
      <div class="fw-semibold mb-2">Current avatar</div>

      <div class="d-flex align-items-center gap-3">
        <div class="fh-avatar">
          <?php if ($avatarUrl !== ''): ?>
            <img src="<?= h($avatarUrl) ?>" alt="<?= h($login) ?>">
          <?php else: ?>
            <div class="fh-avatar__ph"><i class="bi bi-person"></i></div>
          <?php endif; ?>
        </div>

        <div class="flex-grow-1">
          <div class="text-muted small">
            Хранится в <code>/img/users/</code> как <code><?= $id ?>.jpg/png/webp</code>
          </div>

          <div class="d-flex gap-2 mt-2">
            <a
              class="btn btn-sm btn-outline-danger"
              href="userAvatarDelete?id=<?= $id ?>"
              data-confirm="delete"
              data-title="Удалить аватар?"
              data-text="Аватар пользователя будет удалён. Действие нельзя отменить."
            >
              <i class="bi bi-trash3"></i>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="fh-card p-4">
      <div class="fw-semibold mb-2">Upload new avatar</div>

      <form action="userAvatarUpdate?id=<?= $id ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Choose image (JPG/PNG/WebP, max 2MB)</label>
          <input class="form-control" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
        </div>

        <button class="btn btn-success" type="submit">
          <i class="bi bi-upload"></i> Save
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
