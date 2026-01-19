<?php
$pageTitle = 'Movies';
ob_start();
?>
<h1>Фильмы</h1>
<p><a href="movieAdd">+ Добавить фильм</a></p>

<?php if (empty($arr)): ?>
  <p>Пусто.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
  <tr>
    <th>ID</th><th>Title</th><th>Year</th><th>Genre</th><th>Actions</th>
  </tr>
  <?php foreach ($arr as $m): ?>
    <tr>
      <td><?= (int)$m['id'] ?></td>
      <td><?= htmlspecialchars($m['title'] ?? '') ?></td>
      <td><?= !empty($m['year']) ? (int)$m['year'] : '-' ?></td>
      <td><?= htmlspecialchars($m['genre_name'] ?? '-') ?></td>
      <td>
        <a href="movieEdit?id=<?= (int)$m['id'] ?>">edit</a> |
        <a href="movieDelete?id=<?= (int)$m['id'] ?>">delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
