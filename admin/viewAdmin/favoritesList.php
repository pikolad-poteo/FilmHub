<?php
$pageTitle = 'Favorites';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="mb-3">
  <h1 class="h4 mb-1">Избранное</h1>
  <div class="text-muted small">Связи пользователь → фильм</div>
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
            <th style="width:160px;">User</th>
            <th>Movie</th>
            <th style="width:220px;">Created</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arr as $f): ?>
            <tr>
              <td class="text-muted"><?= (int)$f['id'] ?></td>
              <td class="fw-semibold"><?= h($f['user_login'] ?? '') ?></td>
              <td><?= h($f['movie_title'] ?? '') ?></td>
              <td class="text-muted"><?= h($f['created_at'] ?? '') ?></td>
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
