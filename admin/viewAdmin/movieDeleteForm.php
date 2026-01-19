<?php
$pageTitle = 'Delete Movie';
ob_start();
?>
<h1>Удалить фильм</h1>

<p>Точно удалить: <b><?= htmlspecialchars($m['title'] ?? '') ?></b> (ID <?= (int)$m['id'] ?>)?</p>

<p>
  <a href="movieDeleteResult?id=<?= (int)$m['id'] ?>" style="color:red;">ДА, удалить</a>
  | <a href="moviesAdmin">Отмена</a>
</p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
