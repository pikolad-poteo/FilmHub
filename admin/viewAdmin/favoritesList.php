<?php
$pageTitle = 'Favorites';
ob_start();
?>
<h1>Избранное</h1>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>User</th><th>Movie</th><th>Created</th></tr>
  <?php foreach ($arr as $f): ?>
    <tr>
      <td><?= (int)$f['id'] ?></td>
      <td><?= htmlspecialchars($f['user_login'] ?? '') ?></td>
      <td><?= htmlspecialchars($f['movie_title'] ?? '') ?></td>
      <td><?= htmlspecialchars($f['created_at'] ?? '') ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
