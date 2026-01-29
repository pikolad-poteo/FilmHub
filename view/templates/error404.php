<?php
$pageTitle = '404';
ob_start();
?>
<h1>404</h1>
<p>Страница не найдена.</p>
<p><a href="index.php">На главную</a></p>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

