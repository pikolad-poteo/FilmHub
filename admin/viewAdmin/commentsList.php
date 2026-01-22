<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Comments';
ob_start();
?>

<h1 class="h4 mb-3">Comments</h1>

<?php if (empty($arr)): ?>
  <div class="alert alert-info">Comments not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Movie</th>
          <th>Text</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($arr as $c): ?>
        <tr>
          <td><?= (int)$c['id'] ?></td>
          <td><?= h($c['user_login'] ?? '') ?></td>
          <td><?= h($c['movie_title'] ?? '') ?></td>
          <td style="max-width:400px">
            <?= nl2br(h($c['text'])) ?>
          </td>
          <td>
            <span class="badge <?= $c['status'] === 'visible' ? 'bg-success' : 'bg-secondary' ?>">
              <?= h($c['status']) ?>
            </span>
          </td>
          <td><?= h($c['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
