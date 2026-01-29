<?php
$pageTitle = '404';
ob_start();
?>
<h1>404</h1>
<p>Админ-страница не найдена.</p>
<p><a href="dashboard">На dashboard</a></p>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
