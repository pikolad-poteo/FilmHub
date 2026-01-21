<?php
$pageTitle = 'Users';
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Users</h1>
    <div class="text-muted small">Управление пользователями</div>
  </div>
</div>

<div class="fh-card p-3">
  <div class="table-responsive">
    <table class="table fh-table align-middle">
      <thead>
        <tr>
          <th style="width:90px;">ID</th>
          <th>Login</th>
          <th>Email</th>
          <th style="width:120px;">Role</th>
          <th style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr>
            <td colspan="5" class="text-muted py-4">
              <i class="bi bi-inbox me-1"></i> Нет пользователей
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="text-muted"><?= (int)$u['id'] ?></td>
              <td class="fw-semibold"><?= h($u['login'] ?? '') ?></td>
              <td class="text-muted"><?= h($u['email'] ?? '') ?></td>
              <td>
                <span class="badge <?= (($u['role'] ?? '') === 'admin') ? 'text-bg-warning' : 'text-bg-secondary' ?>">
                  <?= h($u['role'] ?? 'user') ?>
                </span>
              </td>
              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-danger"
                   href="userDelete?id=<?= (int)$u['id'] ?>"
                   title="Delete"
                   data-confirm="delete"
                   data-title="Удалить пользователя?"
                   data-text="Пользователь и связанные данные будут удалены. Продолжить?">
                  <i class="bi bi-trash3"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
