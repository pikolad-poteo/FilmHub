<?php
$pageTitle = 'Users';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="mb-3">
  <h1 class="h4 mb-1">Пользователи</h1>
  <div class="text-muted small">Список пользователей системы</div>
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
            <th>Login</th>
            <th>Email</th>
            <th style="width:120px;">Role</th>
            <th style="width:210px;">Created</th>
            <th style="width:110px;" class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($arr as $u): ?>
            <tr>
              <td class="text-muted"><?= (int)$u['id'] ?></td>
              <td class="fw-semibold"><?= h($u['login'] ?? '') ?></td>
              <td><?= h($u['email'] ?? '') ?></td>
              <td>
                <?php $role = $u['role'] ?? 'user'; ?>
                <span class="badge <?= $role === 'admin' ? 'text-bg-warning' : 'text-bg-secondary' ?>">
                  <?= h($role) ?>
                </span>
              </td>
              <td class="text-muted"><?= h($u['created_at'] ?? '') ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-danger"
                   href="userDelete?id=<?= (int)$u['id'] ?>"
                   title="Delete user">
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
$layout = __DIR__ . '/layout.php';
if (!is_file($layout)) $layout = __DIR__ . '/templates/layout.php';
require $layout;
