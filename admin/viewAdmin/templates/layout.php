<?php
declare(strict_types=1);

$viewFile = $GLOBALS['__admin_view'] ?? null;
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>FilmHub Admin</title>
  <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
  <header>
    <nav>
      <a href="/admin/">Admin</a> |
      <a href="/admin/movies">Фильмы</a> |
      <a href="/admin/ratings">Оценки</a> |
      <a href="/admin/favorites">Избранное</a> |
      <a href="/admin/logout">Выход</a>
    </nav>
  </header>

  <main>
    <?php
      if ($viewFile) {
          require __DIR__ . '/' . $viewFile;
      } else {
          echo '<p>Admin view not found</p>';
      }
    ?>
  </main>
</body>
</html>
