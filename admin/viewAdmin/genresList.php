<?php
$pageTitle = 'Genres';
ob_start();
?>
<h1>Жанры</h1>
<p><a href="genreAdd">+ Добавить жанр</a></p>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr><th>ID</th><th>Name</th><th>Slug</th><th>Action</th></tr>
  <?php foreach ($arr as $g): ?>
    <tr>
      <td><?= (int)$g['id'] ?></td>
      <td><?= htmlspecialchars($g['name'] ?? '') ?></td>
      <td><?= htmlspecialchars($g['slug'] ?? '') ?></td>
      <td><a href="genreDelete?id=<?= (int)$g['id'] ?>" style="color:red;">delete</a></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
