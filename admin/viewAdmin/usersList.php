<?php
$pageTitle = 'Users';

ob_start();
?>
<h1>Пользователи</h1>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Login</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created</th>
    </tr>
    <?php foreach ($arr as $u): ?>
      <tr>
        <td><?= (int)$u['id'] ?></td>
        <td><?= htmlspecialchars($u['login'] ?? '') ?></td>
        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
        <td><?= htmlspecialchars($u['role'] ?? '') ?></td>
        <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
        <td>
        <a href="userDelete?id=<?= (int)$u['id'] ?>" style="color:red;">delete</a>
      </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php
$content = ob_get_clean();

// layout может лежать либо в viewAdmin/, либо в viewAdmin/templates/ — сделаем оба варианта
$layout = __DIR__ . '/layout.php';
if (!is_file($layout)) $layout = __DIR__ . '/templates/layout.php';

require $layout;
