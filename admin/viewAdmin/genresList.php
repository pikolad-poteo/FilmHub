<?php
$pageTitle = 'Genres';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Жанры</h1>
    <div class="text-muted small">Справочник жанров</div>
  </div>
  <a class="btn btn-success" href="genreAdd">
    <i class="bi bi-plus-lg me-1"></i> Добавить жанр
  </a>
</div>

<div class="fh-card p-3">
  <?php if (empty($arr)): ?>
    <div class="text-muted">Пусто.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table fh-table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:80px;">ID</th>
            <th>Name</th>
            <th style="width:220px;">Slug</th>
            <th style="width:110px;" class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arr as $g): ?>
            <tr>
              <td class="text-muted"><?= (int)$g['id'] ?></td>
              <td class="fw-semibold"><?= h($g['name'] ?? '') ?></td>
              <td><code><?= h($g['slug'] ?? '') ?></code></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-danger"
                   href="genreDelete?id=<?= (int)$g['id'] ?>"
                   data-confirm="delete"
                   data-title="Удалить жанр?"
                   data-text="Жанр будет удалён. Если он привязан к фильмам — поле genre_id станет NULL.">
                  <i class="bi bi-trash3"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
