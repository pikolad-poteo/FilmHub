<?php
// admin/viewAdmin/favoritesList.php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Favorites';
ob_start();

$favorites = $arr ?? [];
if ($favorites instanceof Traversable) {
  $tmp = [];
  foreach ($favorites as $x) $tmp[] = $x;
  $favorites = $tmp;
}

/**
 * СОРТИРОВКА
 * ?sort=id|user|created_at&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'created_at';
$dir  = strtolower($_GET['dir'] ?? 'desc');
$dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'desc';

$allowedSort = ['id','user','created_at'];
if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';

if (!empty($favorites)) {
  usort($favorites, function($a, $b) use ($sort, $dir) {

    if ($sort === 'user') {
      $av = (string)($a['user_login'] ?? $a['login'] ?? ('user#' . (int)($a['user_id'] ?? 0)));
      $bv = (string)($b['user_login'] ?? $b['login'] ?? ('user#' . (int)($b['user_id'] ?? 0)));
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
    <h1 class="h4 mb-0">Favorites</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
        Date<?= $iconDir($sort,$dir,'created_at') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('user', nextDir($sort,$dir,'user'))) ?>">
        User<?= $iconDir($sort,$dir,'user') ?>
      </a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">
        ID<?= $iconDir($sort,$dir,'id') ?>
      </a>
    </div>
  </div>
</div>

<?php if (empty($favorites)): ?>
  <div class="alert alert-info">Favorites not found</div>
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

          <th style="width:240px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('user', nextDir($sort,$dir,'user'))) ?>">
              User<?= $iconDir($sort,$dir,'user') ?>
            </a>
          </th>

          <th>Movie</th>

          <th style="width:170px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('created_at', nextDir($sort,$dir,'created_at'))) ?>">
              Created<?= $iconDir($sort,$dir,'created_at') ?>
            </a>
          </th>

          <th class="text-end" style="width:130px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($favorites as $f): ?>
        <?php
          $id      = (int)($f['id'] ?? 0);
          $login   = (string)($f['user_login'] ?? $f['login'] ?? ('user#' . (int)($f['user_id'] ?? 0)));
          $movie   = (string)($f['movie_title'] ?? $f['title'] ?? ('movie#' . (int)($f['movie_id'] ?? 0)));
          $created = (string)($f['created_at'] ?? '');
        ?>
        <tr>
          <td><?= $id ?></td>
          <td><strong><?= h($login) ?></strong></td>
          <td><?= h($movie) ?></td>
          <td class="text-muted small"><?= h($created) ?></td>

          <td class="text-end">
            <a
              href="favoriteDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
              data-confirm="delete"
              data-title="Удалить из избранного?"
              data-text="Запись будет удалена. Действие нельзя отменить."
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
