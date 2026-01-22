<?php
// admin/viewAdmin/commentsList.php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Comments';
ob_start();

$comments = $arr ?? [];
if ($comments instanceof Traversable) {
  $tmp = [];
  foreach ($comments as $x) $tmp[] = $x;
  $comments = $tmp;
}

/**
 * СОРТИРОВКА
 * ?sort=id|user|movie|status|created_at&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'created_at';
$dir  = strtolower($_GET['dir'] ?? 'desc');
$dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'desc';

$allowedSort = ['id','user','movie','status','created_at'];
if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';

if (!empty($comments)) {
  usort($comments, function($a, $b) use ($sort, $dir) {

    // вычисляем поле сортировки
    if ($sort === 'user') {
      $av = (string)($a['user_login'] ?? $a['login'] ?? ('user#' . (int)($a['user_id'] ?? 0)));
      $bv = (string)($b['user_login'] ?? $b['login'] ?? ('user#' . (int)($b['user_id'] ?? 0)));
      $cmp = mb_strtolower($av) <=> mb_strtolower($bv);

    } elseif ($sort === 'movie') {
      $av = (string)($a['movie_title'] ?? $a['title'] ?? ('movie#' . (int)($a['movie_id'] ?? 0)));
      $bv = (string)($b['movie_title'] ?? $b['title'] ?? ('movie#' . (int)($b['movie_id'] ?? 0)));
      $cmp = mb_strtolower($av) <=> mb_strtolower($bv);

    } elseif ($sort === 'id') {
      $cmp = (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0);

    } else {
      $av = (string)($a[$sort] ?? '');
      $bv = (string)($b[$sort] ?? '');
      $cmp = mb_strtolower($av) <=> mb_strtolower($bv);
    }

    return $dir === 'desc' ? -$cmp : $cmp;
  });
}

function buildSortLink(string $sort, string $dir): string {
  $qs = $_GET;
  $qs['sort'] = $sort;
  $qs['dir']  = $dir;
  return '?' . http_build_query($qs);
}
function nextDir(string $currentSort, string $currentDir, string $clickedSort): string {
  if ($currentSort !== $clickedSort) return 'asc';
  return $currentDir === 'asc' ? 'desc' : 'asc';
}
$iconDir = function(string $currentSort, string $currentDir, string $col): string {
  if ($currentSort !== $col) return '';
  return $currentDir === 'asc' ? ' <i class="bi bi-arrow-up"></i>' : ' <i class="bi bi-arrow-down"></i>';
};
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-0">Comments</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
        Date<?= $iconDir($sort,$dir,'created_at') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('user', nextDir($sort,$dir,'user'))) ?>">
        User<?= $iconDir($sort,$dir,'user') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('movie', nextDir($sort,$dir,'movie'))) ?>">
        Movie<?= $iconDir($sort,$dir,'movie') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('status', nextDir($sort,$dir,'status'))) ?>">
        Status<?= $iconDir($sort,$dir,'status') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">
        ID<?= $iconDir($sort,$dir,'id') ?>
      </a>
    </div>
  </div>
</div>

<?php if (empty($comments)): ?>
  <div class="alert alert-info">Comments not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table fh-table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">
              ID<?= $iconDir($sort,$dir,'id') ?>
            </a>
          </th>

          <th style="width:180px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('user', nextDir($sort,$dir,'user'))) ?>">
              User<?= $iconDir($sort,$dir,'user') ?>
            </a>
          </th>

          <th>Comment</th>

          <th style="width:240px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('movie', nextDir($sort,$dir,'movie'))) ?>">
              Movie<?= $iconDir($sort,$dir,'movie') ?>
            </a>
          </th>

          <th style="width:120px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('status', nextDir($sort,$dir,'status'))) ?>">
              Status<?= $iconDir($sort,$dir,'status') ?>
            </a>
          </th>

          <th style="width:170px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
              Created<?= $iconDir($sort,$dir,'created_at') ?>
            </a>
          </th>

          <th class="text-end" style="width:180px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($comments as $c): ?>
        <?php
          $id     = (int)($c['id'] ?? 0);
          $login  = (string)($c['user_login'] ?? $c['login'] ?? ('user#' . (int)($c['user_id'] ?? 0)));
          $movie  = (string)($c['movie_title'] ?? $c['title'] ?? ('movie#' . (int)($c['movie_id'] ?? 0)));
          $text   = (string)($c['text'] ?? '');
          $status = (string)($c['status'] ?? 'visible');
          $created = (string)($c['created_at'] ?? '');

          $isHidden = ($status === 'hidden');
          $badge = $isHidden ? 'text-bg-secondary' : 'text-bg-success';
          $statusLabel = $isHidden ? 'hidden' : 'visible';

          $short = mb_strlen($text) > 160 ? mb_substr($text, 0, 160) . '…' : $text;
        ?>
        <tr>
          <td><?= $id ?></td>

          <td><strong><?= h($login) ?></strong></td>

          <td title="<?= h($text) ?>"><?= h($short) ?></td>

          <td><?= h($movie) ?></td>

          <td>
            <span class="badge <?= h($badge) ?>"><?= h($statusLabel) ?></span>
          </td>

          <td class="text-muted small"><?= h($created) ?></td>

          <td class="text-end">
            <a
              href="commentToggle?id=<?= $id ?>"
              class="btn btn-sm btn-outline-primary"
              title="<?= $isHidden ? 'Show' : 'Hide' ?>"
              aria-label="<?= $isHidden ? 'Show' : 'Hide' ?>"
            >
              <i class="bi <?= $isHidden ? 'bi-eye' : 'bi-eye-slash' ?>"></i>
            </a>

            <a
              href="commentDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
              data-confirm="delete"
              data-title="Удалить комментарий?"
              data-text="Комментарий будет удалён. Действие нельзя отменить."
              aria-label="Delete"
            >
              <i class="bi bi-trash3"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
