<?php
$pageTitle = 'Результат регистрации';

ob_start();
?>
<h1>Регистрация</h1>

<?php
// $result приходит из Controller::registerUser()
$ok = !empty($result[0]);
?>

<?php if ($ok): ?>
    <p>✅ Регистрация успешна.</p>
    <p><a href="loginForm">Перейти к входу</a></p>
<?php else: ?>
    <p>❌ Регистрация не удалась.</p>
    <div style="padding:10px;border:1px solid #f00;color:#900;">
        <?= $result[1] ?? 'Ошибка' ?>
    </div>
    <p><a href="registerForm">Назад</a></p>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
