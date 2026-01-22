<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Edit Movie';
ob_start();

$id = (int)($m['id'] ?? 0);

/**
 * base пути проекта (чтобы постер корректно показывался из /admin)
 */
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$projectBase = preg_replace('~/admin$~', '', $adminBase); // /filmhub

function buildPosterUrl(string $projectBase, ?string $poster): string
{
  $p = trim((string)$poster);
  if ($p === '') return '';

  // абсолютная ссылка
  if (preg_match('~^https?://~i', $p)) return $p;

  // если начинается с /
  if ($p[0] === '/') return $projectBase . $p;

  // если уже img/...
  if (str_starts_with($p, 'img/')) return $projectBase . '/' . $p;

  // если в БД только имя без расширения — пробуем .jpg
  if (!preg_match('~\.[a-z0-9]{2,5}$~i', $p)) {
    $p .= '.jpg';
  }

  return $projectBase . '/img/movies/' . $p;
}

$posterUrl = buildPosterUrl($projectBase, $m['poster'] ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Edit movie</h1>
    <div class="text-muted small">Редактировать фильм #<?= $id ?></div>
  </div>

  <div class="d-flex gap-2">
    <a href="moviesAdmin" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back
    </a>

    <!-- Единственная кнопка сохранения -->
    <button form="movieEditForm" type="submit" class="btn btn-primary">
      <i class="bi bi-check2-circle"></i> Save
    </button>
  </div>
</div>

<div class="fh-card p-4">
  <form
    id="movieEditForm"
    method="post"
    action="movieEditResult?id=<?= $id ?>"
    data-project-base="<?= h($projectBase) ?>"
  >
    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input
              class="form-control"
              name="title"
              required
              value="<?= h($m['title'] ?? '') ?>"
              placeholder="Например: Avatar"
            >
          </div>

          <div class="col-12">
            <label class="form-label">Original title</label>
            <input
              class="form-control"
              name="original_title"
              value="<?= h($m['original_title'] ?? '') ?>"
              placeholder="Например: Avatar (original)"
            >
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea
              class="form-control"
              name="description"
              rows="6"
              placeholder="Краткое описание фильма..."
            ><?= h($m['description'] ?? '') ?></textarea>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Year</label>
            <input
              class="form-control"
              name="year"
              type="number"
              min="1888"
              max="2100"
              value="<?= h((string)($m['year'] ?? '')) ?>"
              placeholder="2009"
            >
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Duration (min)</label>
            <input
              class="form-control"
              name="duration_minutes"
              type="number"
              min="1"
              max="999"
              value="<?= h((string)($m['duration_minutes'] ?? '')) ?>"
              placeholder="162"
            >
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre_id">
              <option value="">-- none --</option>
              <?php foreach (($genres ?? []) as $g): ?>
                <option
                  value="<?= (int)$g['id'] ?>"
                  <?= ((int)($m['genre_id'] ?? 0) === (int)$g['id']) ? 'selected' : '' ?>
                >
                  <?= h($g['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Country</label>
            <input
              class="form-control"
              name="country"
              value="<?= h($m['country'] ?? '') ?>"
              placeholder="USA"
            >
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Director</label>
            <input
              class="form-control"
              name="director"
              value="<?= h($m['director'] ?? '') ?>"
              placeholder="James Cameron"
            >
          </div>

          <div class="col-12">
            <label class="form-label">Poster</label>
            <input
              class="form-control"
              name="poster"
              id="posterInput"
              value="<?= h($m['poster'] ?? '') ?>"
              placeholder="avatar_2009.jpg или /img/movies/avatar_2009.jpg"
            >
            <div class="form-text">
              Если в БД хранится только имя, файл должен лежать в <code>/img/movies/</code>.
              Превью обновится сразу при вводе.
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">YouTube trailer id</label>
            <input
              class="form-control"
              name="youtube_trailer_id"
              value="<?= h($m['youtube_trailer_id'] ?? '') ?>"
              placeholder="Например: d9MyW72ELq0"
            >
            <div class="form-text">
              Это только ID видео (часть после <code>v=</code>).
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="border rounded-3 p-3">
          <div class="fw-semibold mb-2"><i class="bi bi-image me-1"></i> Poster preview</div>

          <div id="posterPlaceholder" class="text-muted" style="<?= $posterUrl ? 'display:none;' : '' ?>">
            No poster
          </div>

          <img
            id="posterPreview"
            src="<?= h($posterUrl) ?>"
            alt="<?= h($m['title'] ?? '') ?>"
            style="width:100%; max-width:280px; border-radius:12px; display:<?= $posterUrl ? 'block' : 'none' ?>;"
            loading="lazy"
          >

          <hr class="my-3">

          <div class="d-grid gap-2">
            <a href="moviesAdmin" class="btn btn-outline-secondary">
              <i class="bi bi-x-circle"></i> Cancel
            </a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
