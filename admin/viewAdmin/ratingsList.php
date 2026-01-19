<?php
$pageTitle = 'Ratings';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="mb-3">
  <h1 class="h4 mb-1">Оценки</h1>
  <div class="text-muted small">История оценок пользователей</div>
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
            <th style="width:110px;">Rating</th>
            <th style="width:220px;">Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arr as $r): ?>
            <tr>
              <td class="text-muted"><?= (int)$r['id'] ?></td>
              <td class="fw-semibold"><?= h($r['user_login'] ?? '') ?></td>
              <td><?= h($r['movie_title'] ?? '') ?></td>
              <td>
                <span class="badge text-bg-warning">
                  <i class="bi bi-star-fill me-1"></i><?= (int)$r['rating'] ?>
                </span>
              </td>
              <td class="text-muted"><?= h($r['updated_at'] ?? '') ?></td>
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
