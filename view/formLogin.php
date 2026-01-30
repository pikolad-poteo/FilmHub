<?php
$pageTitle = 'Вход';

ob_start();
?>
<h1>Вход</h1>
<p><a href="index.php">← На главную</a></p>

<form method="post" action="login">
    <div>
        <label>Login или Email:</label><br>
        <input type="text" name="email" value="">
    </div>
    <div style="margin-top:8px;">
        <label>Пароль:</label><br>
        <input type="password" name="password" value="">
    </div>
    <div style="margin-top:10px;">
        <button type="submit">Войти</button>
    </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
