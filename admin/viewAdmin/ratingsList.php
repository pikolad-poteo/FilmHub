<?php
$pageTitle = 'Ratings';
ob_start();
?>
<h1>Оценки</h1>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>User</th><th>Movie</th><th>Rating</th><th>Updated</th></tr>
  <?php foreach ($arr as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= htmlspecialchars($r['user_login'] ?? '') ?></td>
      <td><?= htmlspecialchars($r['movie_title'] ?? '') ?></td>
      <td><?= (int)$r['rating'] ?></td>
      <td><?= htmlspecialchars($r['updated_at'] ?? '') ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
