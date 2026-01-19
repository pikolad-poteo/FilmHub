<?php
$pageTitle = 'Delete User';
ob_start();
?>
<h1>Удалить пользователя</h1>

<p>
  Точно удалить пользователя:
  <b><?= htmlspecialchars($u['login'] ?? '') ?></b>
  (ID <?= (int)$u['id'] ?>, <?= htmlspecialchars($u['email'] ?? '') ?>)?
</p>

<p style="color:#900;">
  Внимание: будут удалены связанные данные пользователя (комментарии/избранное/оценки) из-за каскадов.
</p>

<p>
  <a href="userDeleteResult?id=<?= (int)$u['id'] ?>" style="color:red;">ДА, удалить</a>
  | <a href="usersAdmin">Отмена</a>
</p>

<?php
$content = ob_get_clean();
$layout = __DIR__ . '/layout.php';
if (!is_file($layout)) $layout = __DIR__ . '/templates/layout.php';
require $layout;
