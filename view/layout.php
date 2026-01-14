<?php
declare(strict_types=1);

/**
 * Ожидаем, что $viewFile передан в render().
 * В render() я передал имя файла как строку (например 'start.php').
 */
$viewFile = $viewFile ?? null;

// Если Controller->render() вызван как render('start.php'), то layout.php не знает $viewFile.
// Поэтому в render() мы передаём $viewFile не напрямую, а как параметр.
// Самый простой способ: положим имя view в $GLOBALS из render().
if (!$viewFile && isset($GLOBALS['__view'])) {
    $viewFile = $GLOBALS['__view'];
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>FilmHub</title>
  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
  <header>
    <nav>
      <a href="/">Главная</a> |
      <a href="/movies">Фильмы</a> |
      <a href="/genres">Жанры</a> |
      <a href="/my-favorites">Моё избранное</a> |
      <?php if (!empty($_SESSION['user']['id'])): ?>
        <a href="/profile">Профиль</a> |
        <a href="/logout">Выход</a>
      <?php else: ?>
        <a href="/login">Вход</a> |
        <a href="/register">Регистрация</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <?php
      if ($viewFile) {
          require __DIR__ . '/' . $viewFile;
      } else {
          echo '<p>View not found</p>';
      }
    ?>
  </main>
</body>
</html>
