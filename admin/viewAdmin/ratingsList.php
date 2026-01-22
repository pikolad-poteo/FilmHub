<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Ratings';
ob_start();
?>

<h1 class="h4 mb-3">Ratings</h1>

<?php if (empty($arr)): ?>
  <div class="alert alert-info">Ratings not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Movie</th>
          <th>Rating</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($arr as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= h($r['user_login'] ?? '') ?></td>
          <td><?= h($r['movie_title'] ?? '') ?></td>
          <td>
            <span class="badge bg-warning text-dark">
              <?= (int)$r['rating'] ?>/10
            </span>
          </td>
          <td><?= h($r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
