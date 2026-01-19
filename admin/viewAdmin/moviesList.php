<?php
$pageTitle = 'Movies';
ob_start();

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Базовый путь сайта (чтобы постеры img/movies/... работали и в подпапке)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$siteBase  = preg_replace('~/admin$~', '', $scriptDir);
if ($siteBase === '') $siteBase = '';
function site_url_local($path=''){
  global $siteBase;
  $path = ltrim($path, '/');
  return $siteBase . ($path ? '/' . $path : '/');
}
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Фильмы</h1>
    <div class="text-muted small">Список фильмов/сериалов в базе</div>
  </div>
  <a class="btn btn-success" href="movieAdd">
    <i class="bi bi-plus-lg me-1"></i> Добавить фильм
  </a>
</div>

<div class="fh-card p-3">
  <?php if (empty($arr)): ?>
    <div class="text-muted">Пусто.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table fh-table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:70px;">Poster</th>
            <th style="width:70px;">ID</th>
            <th>Title</th>
            <th style="width:90px;">Year</th>
            <th style="width:160px;">Genre</th>
            <th style="width:130px;" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($arr as $m): ?>
          <?php
            $poster = trim((string)($m['poster'] ?? ''));
            $posterSrc = $poster ? site_url_local($poster) : '';
          ?>
          <tr>
            <td>
              <?php if ($posterSrc): ?>
                <img class="fh-poster" src="<?= h($posterSrc) ?>" alt="poster">
              <?php else: ?>
                <div class="fh-poster d-flex align-items-center justify-content-center">
                  <i class="bi bi-image text-muted"></i>
                </div>
              <?php endif; ?>
            </td>
            <td class="text-muted"><?= (int)$m['id'] ?></td>
            <td>
              <div class="fw-semibold"><?= h($m['title'] ?? '') ?></div>
              <?php if (!empty($m['original_title'])): ?>
                <div class="text-muted small"><?= h($m['original_title']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= !empty($m['year']) ? (int)$m['year'] : '<span class="text-muted">—</span>' ?></td>
            <td>
              <?php if (!empty($m['genre_name'])): ?>
                <span class="badge text-bg-primary-subtle border"><?= h($m['genre_name']) ?></span>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="movieEdit?id=<?= (int)$m['id'] ?>" title="Edit">
                <i class="bi bi-pencil-square"></i>
              </a>
              <a class="btn btn-sm btn-outline-danger" href="movieDelete?id=<?= (int)$m['id'] ?>" title="Delete">
                <i class="bi bi-trash3"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
