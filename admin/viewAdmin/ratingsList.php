<?php
// admin/viewAdmin/ratingsList.php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Ratings';
ob_start();

$ratings = $arr ?? [];
if ($ratings instanceof Traversable) {
  $tmp = [];
  foreach ($ratings as $x) $tmp[] = $x;
  $ratings = $tmp;
}

/**
 * СОРТИРОВКА
 * ?sort=id|movie|rating|created_at&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'created_at';
$dir  = strtolower($_GET['dir'] ?? 'desc');
$dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'desc';

$allowedSort = ['id','movie','rating','created_at'];
if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';

if (!empty($ratings)) {
  usort($ratings, function($a, $b) use ($sort, $dir) {

    if ($sort === 'movie') {
      $av = (string)($a['movie_title'] ?? $a['title'] ?? ('movie#' . (int)($a['movie_id'] ?? 0)));
      $bv = (string)($b['movie_title'] ?? $b['title'] ?? ('movie#' . (int)($b['movie_id'] ?? 0)));
      $cmp = mb_strtolower($av) <=> mb_strtolower($bv);

    } elseif ($sort === 'rating') {
      $cmp = (int)($a['rating'] ?? 0) <=> (int)($b['rating'] ?? 0);

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

function ratingBadge(int $r): string {
  if ($r >= 8) return 'text-bg-success';
  if ($r >= 6) return 'text-bg-primary';
  if ($r >= 4) return 'text-bg-warning';
  return 'text-bg-danger';
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-0">Ratings</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
        Date<?= $iconDir($sort,$dir,'created_at') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('movie', nextDir($sort,$dir,'movie'))) ?>">
        Movie<?= $iconDir($sort,$dir,'movie') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('rating', nextDir($sort,$dir,'rating'))) ?>">
        Rating<?= $iconDir($sort,$dir,'rating') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">
        ID<?= $iconDir($sort,$dir,'id') ?>
      </a>
    </div>
  </div>
</div>

<?php if (empty($ratings)): ?>
  <div class="alert alert-info">Ratings not found</div>
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

          <th style="width:220px;">User</th>

          <th>
            <a class="text-decoration-none" href="<?= h(buildSortLink('movie', nextDir($sort,$dir,'movie'))) ?>">
              Movie<?= $iconDir($sort,$dir,'movie') ?>
            </a>
          </th>

          <th style="width:120px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('rating', nextDir($sort,$dir,'rating'))) ?>">
              Rating<?= $iconDir($sort,$dir,'rating') ?>
            </a>
          </th>

          <th style="width:170px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
              Created<?= $iconDir($sort,$dir,'created_at') ?>
            </a>
          </th>

          <th class="text-end" style="width:130px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($ratings as $r): ?>
        <?php
          $id      = (int)($r['id'] ?? 0);
          $login   = (string)($r['user_login'] ?? $r['login'] ?? ('user#' . (int)($r['user_id'] ?? 0)));
          $movie   = (string)($r['movie_title'] ?? $r['title'] ?? ('movie#' . (int)($r['movie_id'] ?? 0)));
          $rating  = (int)($r['rating'] ?? 0);
          $created = (string)($r['created_at'] ?? '');
        ?>
        <tr>
          <td><?= $id ?></td>
          <td><strong><?= h($login) ?></strong></td>
          <td><?= h($movie) ?></td>
          <td><span class="badge <?= h(ratingBadge($rating)) ?>"><?= $rating ?></span></td>
          <td class="text-muted small"><?= h($created) ?></td>

          <td class="text-end">
            <a
              href="ratingDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
              data-confirm="delete"
              data-title="Удалить оценку?"
              data-text="Оценка будет удалена. Действие нельзя отменить."
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
