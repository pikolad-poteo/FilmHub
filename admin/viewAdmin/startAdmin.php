<?php
$pageTitle = 'Dashboard';
ob_start();
?>
<div class="fh-card p-4">
  <h1 class="h4 mb-2">Dashboard</h1>
  <p class="text-muted mb-0">Вы в админке. Выберите раздел в меню слева.</p>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
