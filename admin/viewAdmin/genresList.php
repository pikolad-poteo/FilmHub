<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Genres';
ob_start();

$genres = $arr ?? [];
if ($genres instanceof Traversable) {
  $tmp = [];
  foreach ($genres as $x) $tmp[] = $x;
  $genres = $tmp;
}

/**
 * СОРТИРОВКА
 * ?sort=id|name|slug|movies&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'id';
$dir  = strtolower($_GET['dir'] ?? 'asc');
$dir  = in_array($dir, ['asc', 'desc'], true) ? $dir : 'asc';

$allowedSort = ['id', 'name', 'slug', 'movies'];
if (!in_array($sort, $allowedSort, true)) $sort = 'id';

if (!empty($genres)) {
  usort($genres, function($a, $b) use ($sort, $dir) {
    switch ($sort) {
      case 'movies':
        $av = (int)($a['movies_count'] ?? 0);
        $bv = (int)($b['movies_count'] ?? 0);
        break;

      case 'id':
        $av = (int)($a['id'] ?? 0);
        $bv = (int)($b['id'] ?? 0);
        break;

      default:
        $av = mb_strtolower((string)($a[$sort] ?? ''));
        $bv = mb_strtolower((string)($b[$sort] ?? ''));
    }

    $cmp = $av <=> $bv;
    return $dir === 'desc' ? -$cmp : $cmp;
  });
}

function buildSortLink(array $current, string $sort, string $dir): string {
  $qs = $current;
  $qs['sort'] = $sort;
  $qs['dir']  = $dir;
  return '?' . http_build_query($qs);
}

function nextDir(string $currentSort, string $currentDir, string $clickedSort): string {
  if ($currentSort !== $clickedSort) return 'asc';
  return $currentDir === 'asc' ? 'desc' : 'asc';
}

function iconDir(string $currentSort, string $currentDir, string $col): string {
  if ($currentSort !== $col) return '';
  return $currentDir === 'asc'
    ? ' <i class="bi bi-arrow-up"></i>'
    : ' <i class="bi bi-arrow-down"></i>';
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-0">Genres</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'id', nextDir($sort, $dir, 'id'))) ?>">ID<?= iconDir($sort, $dir, 'id') ?></a>
      ·
      <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'name', nextDir($sort, $dir, 'name'))) ?>">Name<?= iconDir($sort, $dir, 'name') ?></a>
      ·
      <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'slug', nextDir($sort, $dir, 'slug'))) ?>">Slug<?= iconDir($sort, $dir, 'slug') ?></a>
      ·
      <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'movies', nextDir($sort, $dir, 'movies'))) ?>">Movies<?= iconDir($sort, $dir, 'movies') ?></a>
    </div>
  </div>

  <a href="genreAdd" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Add genre
  </a>
</div>

<?php if (empty($genres)): ?>
  <div class="alert alert-info">Genres not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table fh-table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'id', nextDir($sort, $dir, 'id'))) ?>">
              ID<?= iconDir($sort, $dir, 'id') ?>
            </a>
          </th>

          <th>
            <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'name', nextDir($sort, $dir, 'name'))) ?>">
              Name<?= iconDir($sort, $dir, 'name') ?>
            </a>
          </th>

          <th style="width:260px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'slug', nextDir($sort, $dir, 'slug'))) ?>">
              Slug<?= iconDir($sort, $dir, 'slug') ?>
            </a>
          </th>

          <th style="width:120px; text-align:center;">
            <a class="text-decoration-none" href="<?= h(buildSortLink($_GET, 'movies', nextDir($sort, $dir, 'movies'))) ?>">
              Movies<?= iconDir($sort, $dir, 'movies') ?>
            </a>
          </th>

          <th class="text-end" style="width:110px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($genres as $g): ?>
        <?php
          $id     = (int)($g['id'] ?? 0);
          $name   = (string)($g['name'] ?? '');
          $slug   = (string)($g['slug'] ?? '');
          $movies = (int)($g['movies_count'] ?? 0);
          $date   = (string)($g['created_at'] ?? '');
        ?>
        <tr>
          <td><?= $id ?></td>
          <td><strong><?= h($name) ?></strong></td>
          <td><code><?= h($slug) ?></code></td>

          <td class="text-center">
            <span class="badge <?= $movies > 0 ? 'bg-primary' : 'bg-secondary' ?>">
              <?= $movies ?>
            </span>
          </td>

          <td class="text-end">
            <a
              href="genreDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
              aria-label="Delete"
              data-confirm="delete"
              data-title="Delete genre?"
              data-text="Genre '<?= h($name) ?>' will be deleted. Continue?"
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
