<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle = $pageTitle ?? 'Admin';
$content   = $content ?? '';

?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin:20px;">

<div style="padding:10px;border:1px solid #ccc;margin-bottom:10px;">
  <b>FilmHub Admin</b>
  <?php if (!empty($_SESSION['is_admin'])): ?>
    | <a href="dashboard">Dashboard</a>
    | <a href="moviesAdmin">Movies</a>
    | <a href="genresAdmin">Genres</a>
    | <a href="usersAdmin">Users</a>
    | <a href="commentsAdmin">Comments</a>
    | <a href="favoritesAdmin">Favorites</a>
    | <a href="ratingsAdmin">Ratings</a>
    | <a href="../index.php">На сайт</a>
    | <a href="logout">Logout</a>
  <?php endif; ?>
</div>

<?php if (!empty($_SESSION['errorString'])): ?>
  <div style="padding:10px;border:1px solid #f00;color:#900;margin-bottom:10px;">
    <?= $_SESSION['errorString']; unset($_SESSION['errorString']); ?>
  </div>
<?php endif; ?>

<?= $content ?>

<hr>
<div style="color:#777;">Admin primitive mode</div>
</body>
</html>
