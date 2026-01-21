<?php
$pageTitle = 'Add Genre';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Добавить жанр</h1>
    <div class="text-muted small">Slug можно оставить пустым — он сгенерируется</div>
  </div>
  <a class="btn btn-outline-secondary" href="genresAdmin">
    <i class="bi bi-arrow-left me-1"></i> Назад
  </a>
</div>

<div class="fh-card p-4">
  <form method="post" action="genreAddResult" class="row g-3">
    <div class="col-12 col-lg-6">
      <label class="form-label">Name*</label>
      <input class="form-control" name="name" required>
    </div>
    <div class="col-12 col-lg-6">
      <label class="form-label">Slug (optional)</label>
      <input class="form-control" name="slug" placeholder="если пусто — сгенерируется">
    </div>

    <div class="col-12 d-flex gap-2 mt-2">
      <button class="btn btn-success">
        <i class="bi bi-check2-circle me-1"></i> Сохранить
      </button>
      <a class="btn btn-outline-secondary ms-auto" href="genresAdmin">Отмена</a>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
