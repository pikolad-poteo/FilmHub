<?php
$pageTitle = 'Add Genre';
ob_start();
?>
<h1>Добавить жанр</h1>

<form method="post" action="genreAddResult">
  <div>Name*: <br><input name="name"></div>
  <div>Slug (optional): <br><input name="slug" placeholder="если пусто — сгенерируется"></div>

  <div style="margin-top:10px;">
    <button type="submit">Сохранить</button>
    <a href="genresAdmin">Отмена</a>
  </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
