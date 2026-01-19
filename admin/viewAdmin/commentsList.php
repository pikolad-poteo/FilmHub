<?php
$pageTitle = 'Comments';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="mb-3">
  <h1 class="h4 mb-1">Комментарии</h1>
  <div class="text-muted small">Модерация видимости комментариев</div>
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
            <th>Movie</th>
            <th style="width:160px;">User</th>
            <th style="width:120px;">Status</th>
            <th>Text</th>
            <th style="width:120px;" class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arr as $c): ?>
            <?php $status = $c['status'] ?? 'visible'; ?>
            <tr>
              <td class="text-muted"><?= (int)$c['id'] ?></td>
              <td class="fw-semibold"><?= h($c['movie_title'] ?? '') ?></td>
              <td><?= h($c['user_login'] ?? '') ?></td>
              <td>
                <span class="badge <?= $status === 'visible' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                  <?= h($status) ?>
                </span>
              </td>
              <td class="text-muted"><?= h(mb_strimwidth($c['text'] ?? '', 0, 90, '...')) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-light" href="commentToggle?id=<?= (int)$c['id'] ?>">
                  <i class="bi bi-arrow-repeat me-1"></i> toggle
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
