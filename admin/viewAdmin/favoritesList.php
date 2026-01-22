<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Favorites';
ob_start();
?>

<h1 class="h4 mb-3">Favorites</h1>

<?php if (empty($arr)): ?>
  <div class="alert alert-info">Favorites not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Movie</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($arr as $f): ?>
        <tr>
          <td><?= (int)$f['id'] ?></td>
          <td><?= h($f['user_login'] ?? '') ?></td>
          <td><?= h($f['movie_title'] ?? '') ?></td>
          <td><?= h($f['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
