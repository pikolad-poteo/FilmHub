<?php
$pageTitle = 'Dashboard';
ob_start();
?>
<h1>Dashboard</h1>
<p>Вы в админке. Выберите раздел в меню сверху.</p>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
