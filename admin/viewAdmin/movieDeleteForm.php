<?php
$pageTitle = 'Delete Movie';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="fh-card p-4">
  <div class="d-flex align-items-center gap-2 mb-2">
    <div class="fh-logo"><i class="bi bi-trash3"></i></div>
    <div>
      <div class="fw-bold">Удаление фильма</div>
      <div class="text-muted small">Действие необратимо</div>
    </div>
  </div>

  <hr class="my-3">

  <p class="mb-2">Точно удалить фильм:</p>
  <div class="fw-semibold fs-5"><?= h($m['title'] ?? '') ?></div>
  <div class="text-muted">ID <?= (int)$m['id'] ?></div>

  <div class="d-flex gap-2 mt-4">
    <a class="btn btn-danger"
       href="movieDeleteResult?id=<?= (int)$m['id'] ?>"
       data-confirm="delete"
       data-title="Удалить фильм?"
       data-text="Фильм будет удалён из базы данных. Продолжить?">
      <i class="bi bi-trash3 me-1"></i> Удалить
    </a>
    <a class="btn btn-outline-secondary ms-auto" href="moviesAdmin">
      Отмена
    </a>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
