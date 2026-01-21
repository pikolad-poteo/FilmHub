<?php
$pageTitle = 'Movies';
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Movies</h1>
    <div class="text-muted small">Управление фильмами</div>
  </div>

  <a class="btn btn-success" href="movieAdd">
    <i class="bi bi-plus-circle me-1"></i> Add movie
  </a>
</div>

<div class="fh-card p-3">
  <div class="table-responsive">
    <table class="table fh-table align-middle">
      <thead>
        <tr>
          <th style="width:80px;">Poster</th>
          <th>Title</th>
          <th style="width:90px;">Year</th>
          <th style="width:140px;">Genre</th>
          <th style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($movies)): ?>
          <tr>
            <td colspan="5" class="text-muted py-4">
              <i class="bi bi-inbox me-1"></i> Нет фильмов
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($movies as $m): ?>
            <?php
              $poster = trim((string)($m['poster'] ?? ''));
              // если poster хранится как относительный путь "img/movies/.."
              // то браузер сам возьмет от корня сайта если начинается с /
              if ($poster !== '' && $poster[0] !== '/' && !preg_match('~^https?://~i', $poster)) {
                // сделаем относительный к /filmhub/
                $poster = '/filmhub/' . ltrim($poster, '/');
              }
            ?>
            <tr>
              <td>
                <?php if (!empty($m['poster'])): ?>
                  <img class="fh-poster" src="<?= h($poster) ?>" alt="poster">
                <?php else: ?>
                  <div class="fh-poster d-grid place-items-center" style="display:grid;place-items:center;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div class="fw-semibold"><?= h($m['title'] ?? '') ?></div>
                <div class="text-muted small">ID <?= (int)$m['id'] ?></div>
              </td>

              <td class="text-muted"><?= h($m['year'] ?? '') ?></td>

              <td>
                <span class="badge text-bg-secondary">
                  <?= h($m['genre_name'] ?? ($m['genre'] ?? '—')) ?>
                </span>
              </td>

              <td class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-light"
                   href="movieEdit?id=<?= (int)$m['id'] ?>"
                   title="Edit">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <a class="btn btn-sm btn-outline-danger"
                   href="movieDelete?id=<?= (int)$m['id'] ?>"
                   title="Delete"
                   data-confirm="delete"
                   data-title="Удалить фильм?"
                   data-text="Фильм будет удалён из базы. Продолжить?">
                  <i class="bi bi-trash3"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
