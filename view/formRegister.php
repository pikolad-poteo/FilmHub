<?php
$pageTitle = 'Регистрация';

ob_start();
?>
<h1>Регистрация</h1>

<form method="post" action="registerAnswer">
    <div>
        <label>Логин:</label><br>
        <input type="text" name="login" value="">
    </div>
    <div style="margin-top:8px;">
        <label>Email:</label><br>
        <input type="text" name="email" value="">
    </div>
    <div style="margin-top:8px;">
        <label>Пароль:</label><br>
        <input type="password" name="password" value="">
    </div>
    <div style="margin-top:8px;">
        <label>Повтор пароля:</label><br>
        <input type="password" name="confirm" value="">
    </div>
    <div style="margin-top:10px;">
        <button type="submit" name="save" value="1">Зарегистрироваться</button>
    </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
