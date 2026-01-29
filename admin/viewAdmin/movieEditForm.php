<?php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Edit Movie';
ob_start();

$id = (int)($m['id'] ?? 0);

// base пути проекта (чтобы постер корректно показывался из /admin)
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$projectBase = preg_replace('~/admin$~', '', $adminBase); // /filmhub

$posterUrl = movie_poster_url($m['poster'] ?? '', $projectBase);
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

    <button form="movieEditForm" type="submit" class="btn btn-primary">
      <i class="bi bi-check2-circle"></i> Save
    </button>
  </div>
</div>

<div class="fh-card p-4">
  <form id="movieEditForm" method="post" action="movieEditResult?id=<?= $id ?>" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input class="form-control" name="title" required value="<?= h($m['title'] ?? '') ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Original title</label>
            <input class="form-control" name="original_title" value="<?= h($m['original_title'] ?? '') ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="6"><?= h($m['description'] ?? '') ?></textarea>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Year</label>
            <input class="form-control" name="year" type="number" min="1888" max="2100" value="<?= h((string)($m['year'] ?? '')) ?>">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Duration (min)</label>
            <input class="form-control" name="duration_minutes" type="number" min="1" max="999" value="<?= h((string)($m['duration_minutes'] ?? '')) ?>">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre_id">
              <option value="">-- none --</option>
              <?php foreach (($genres ?? []) as $g): ?>
                <option value="<?= (int)$g['id'] ?>" <?= ((int)($m['genre_id'] ?? 0) === (int)$g['id']) ? 'selected' : '' ?>>
                  <?= h($g['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Country</label>
            <input class="form-control" name="country" value="<?= h($m['country'] ?? '') ?>">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Director</label>
            <input class="form-control" name="director" value="<?= h($m['director'] ?? '') ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Poster file (replace)</label>
            <input class="form-control" type="file" name="poster_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <div class="form-text">Если выберешь файл — он загрузится в <code>/img/movies</code>, а в БД сохранится только имя.</div>
          </div>

          <div class="col-12">
            <label class="form-label">Poster (filename or URL)</label>
            <input class="form-control" name="poster" value="<?= h($m['poster'] ?? '') ?>" placeholder="avatar_2009.jpg или https://...">
            <div class="form-text">Рекомендуется хранить только имя файла (например <code>avatar_2009.jpg</code>).</div>
          </div>

          <div class="col-12">
            <label class="form-label">YouTube trailer id</label>
            <input class="form-control" name="youtube_trailer_id" value="<?= h($m['youtube_trailer_id'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="border rounded-3 p-3">
          <div class="fw-semibold mb-2"><i class="bi bi-image me-1"></i> Poster preview</div>

          <img
            id="posterPreview"
            src="<?= h($posterUrl) ?>"
            alt="<?= h($m['title'] ?? '') ?>"
            style="width:100%; max-width:280px; border-radius:12px; display:block;"
            loading="lazy"
          >
        </div>
      </div>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
