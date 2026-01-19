<?php
$pageTitle = 'Comments';
ob_start();
?>
<h1>Комментарии</h1>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>Movie</th><th>User</th><th>Status</th><th>Text</th><th>Action</th></tr>
  <?php foreach ($arr as $c): ?>
    <tr>
      <td><?= (int)$c['id'] ?></td>
      <td><?= htmlspecialchars($c['movie_title'] ?? '') ?></td>
      <td><?= htmlspecialchars($c['user_login'] ?? '') ?></td>
      <td><?= htmlspecialchars($c['status'] ?? '') ?></td>
      <td><?= htmlspecialchars(mb_strimwidth($c['text'] ?? '', 0, 80, '...')) ?></td>
      <td><a href="commentToggle?id=<?= (int)$c['id'] ?>">toggle</a></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
