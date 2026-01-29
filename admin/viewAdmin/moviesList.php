<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Movies';
ob_start();

$movies = $arr ?? [];
if ($movies instanceof Traversable) {
  $tmp = [];
  foreach ($movies as $x) $tmp[] = $x;
  $movies = $tmp;
}

/**
 * base пути (без хардкода /filmhub)
 */
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$projectBase = preg_replace('~/admin$~', '', $adminBase); // /filmhub

/**
 * СОРТИРОВКА (внутри страницы)
 * ?sort=id|title&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'id';
$dir  = strtolower($_GET['dir'] ?? 'asc');
$dir  = in_array($dir, ['asc', 'desc'], true) ? $dir : 'asc';

$allowedSort = ['id', 'title'];
if (!in_array($sort, $allowedSort, true)) $sort = 'id';

if (!empty($movies)) {
  usort($movies, function($a, $b) use ($sort, $dir) {
    $av = $a[$sort] ?? '';
    $bv = $b[$sort] ?? '';

    if ($sort === 'id') {
      $cmp = (int)$av <=> (int)$bv;
    } else {
      $cmp = mb_strtolower((string)$av) <=> mb_strtolower((string)$bv);
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
    <h1 class="h4 mb-0">Movies</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort, $dir, 'id'))) ?>">
        ID<?= $iconDir($sort, $dir, 'id') ?>
      </a>
      ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('title', nextDir($sort, $dir, 'title'))) ?>">
        Title<?= $iconDir($sort, $dir, 'title') ?>
      </a>
    </div>
  </div>

  <a href="movieAdd" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Add movie
  </a>
</div>

<?php if (empty($movies)): ?>
  <div class="alert alert-info">Movies not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table fh-table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort, $dir, 'id'))) ?>">
              ID<?= $iconDir($sort, $dir, 'id') ?>
            </a>
          </th>
          <th style="width:110px;">Poster</th>
          <th>
            <a class="text-decoration-none" href="<?= h(buildSortLink('title', nextDir($sort, $dir, 'title'))) ?>">
              Title<?= $iconDir($sort, $dir, 'title') ?>
            </a>
          </th>
          <th style="width:90px;">Year</th>
          <th style="width:160px;">Genre</th>
          <th class="text-end" style="width:130px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($movies as $m): ?>
        <?php
          $id    = (int)($m['id'] ?? 0);
          $title = (string)($m['title'] ?? '');
          $orig  = (string)($m['original_title'] ?? '');
          $year  = (string)($m['year'] ?? '');
          $genre = (string)($m['genre_name'] ?? $m['genre'] ?? '—');
          $posterUrl = movie_poster_url($m['poster'] ?? '', $projectBase);
        ?>
        <tr>
          <td><?= $id ?></td>

          <td>
            <?php if ($posterUrl !== ''): ?>
              <img
                class="fh-poster"
                src="<?= h($posterUrl) ?>"
                alt="<?= h($title) ?>"
                loading="lazy"
              >
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>

          <td>
            <strong><?= h($title) ?></strong>
            <?php if ($orig !== '' && $orig !== $title): ?>
              <div class="text-muted small"><?= h($orig) ?></div>
            <?php endif; ?>
          </td>

          <td><?= h($year) ?></td>
          <td><?= h($genre) ?></td>

          <td class="text-end">
            <a
              href="movieEdit?id=<?= $id ?>"
              class="btn btn-sm btn-outline-primary"
              title="Edit"
              aria-label="Edit"
            >
              <i class="bi bi-pencil-square"></i>
            </a>

            <a
              href="movieDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
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
