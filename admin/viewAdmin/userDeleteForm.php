<?php
$pageTitle = 'Delete User';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="fh-card p-4">
  <div class="d-flex align-items-center gap-2 mb-2">
    <div class="fh-logo"><i class="bi bi-person-x"></i></div>
    <div>
      <div class="fw-bold">Удаление пользователя</div>
      <div class="text-muted small">Будут удалены связанные данные (каскады)</div>
    </div>
  </div>

  <hr class="my-3">

  <div class="mb-2">Точно удалить пользователя:</div>
  <div class="fw-semibold fs-5"><?= h($u['login'] ?? '') ?></div>
  <div class="text-muted">ID <?= (int)$u['id'] ?> • <?= h($u['email'] ?? '') ?></div>

  <div class="alert alert-warning mt-3 mb-0">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    Внимание: комментарии/избранное/оценки пользователя будут удалены из-за каскадов.
  </div>

  <div class="d-flex gap-2 mt-4">
    <a class="btn btn-danger"
       href="userDeleteResult?id=<?= (int)$u['id'] ?>"
       data-confirm="delete"
       data-title="Удалить пользователя?"
       data-text="Пользователь и связанные данные будут удалены. Продолжить?">
      <i class="bi bi-trash3 me-1"></i> Удалить
    </a>
    <a class="btn btn-outline-secondary ms-auto" href="usersAdmin">
      Отмена
    </a>
  </div>
</div>
<?php
$content = ob_get_clean();
$layout = __DIR__ . '/layout.php';
if (!is_file($layout)) $layout = __DIR__ . '/templates/layout.php';
require $layout;
