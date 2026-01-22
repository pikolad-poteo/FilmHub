<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Genres';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Genres</h1>
  <a href="genreAdd" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Add genre
  </a>
</div>

<?php if (empty($arr)): ?>
  <div class="alert alert-info">Genres not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($arr as $g): ?>
        <tr>
          <td><?= (int)$g['id'] ?></td>
          <td><?= h($g['name']) ?></td>
          <td><?= h($g['slug']) ?></td>
          <td><?= h($g['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
