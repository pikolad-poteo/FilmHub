<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'FilmHub';
$content   = $content ?? '';

$isAuth = !empty($_SESSION['user_id']);
$login  = $_SESSION['login'] ?? '';
$role   = $_SESSION['role'] ?? '';

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin: 20px;">

<nav style="margin-bottom: 15px; padding: 10px; border: 1px solid #ccc;">
    <a href="index.php">Главная</a> |
    <a href="all">Все фильмы</a> |
    <a href="genres">Жанры</a> |
    <?php if ($isAuth): ?>
        <a href="myfavorites">Моё избранное</a> |
        <span>Вы вошли как: <b><?= htmlspecialchars($login) ?></b> (<?= htmlspecialchars($role) ?>)</span> |
        <a href="logout">Выйти</a>
        <?php if ($role === 'admin'): ?>
            | <a href="admin/">Админка</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="loginForm">Вход</a> |
        <a href="registerForm">Регистрация</a>
    <?php endif; ?>
</nav>

<?php if (!empty($_SESSION['errorString'])): ?>
    <div style="padding:10px;border:1px solid #f00;color:#900;margin-bottom:10px;">
        <?= $_SESSION['errorString']; unset($_SESSION['errorString']); ?>
    </div>
<?php endif; ?>

<?= $content ?>

<hr>
<div style="color:#777;">FilmHub (primitive mode)</div>

</body>
</html>
