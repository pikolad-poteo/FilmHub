<?php
$pageTitle = 'Add Movie';
ob_start();
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Добавить фильм</h1>
    <div class="text-muted small">Заполни основные поля — остальное можно позже</div>
  </div>
  <a class="btn btn-outline-secondary" href="moviesAdmin">
    <i class="bi bi-arrow-left me-1"></i> Назад
  </a>
</div>

<div class="fh-card p-4">
  <form method="post" action="movieAddResult">
    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <label class="form-label">Title*</label>
        <input class="form-control" name="title" required>

        <div class="mt-3">
          <label class="form-label">Original title</label>
          <input class="form-control" name="original_title">
        </div>

        <div class="mt-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="5"></textarea>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-6 col-lg-3">
            <label class="form-label">Year</label>
            <input class="form-control" name="year" type="number" min="1880" max="2100">
          </div>
          <div class="col-6 col-lg-3">
            <label class="form-label">Duration (min)</label>
            <input class="form-control" name="duration_minutes" type="number" min="0" max="10000">
          </div>
          <div class="col-12 col-lg-6">
            <label class="form-label">Genre</label>
            <select class="form-select" name="genre_id">
              <option value="">— none —</option>
              <?php foreach (($genres ?? []) as $g): ?>
                <option value="<?= (int)$g['id'] ?>"><?= h($g['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-12 col-lg-6">
            <label class="form-label">Country</label>
            <input class="form-control" name="country">
          </div>
          <div class="col-12 col-lg-6">
            <label class="form-label">Director</label>
            <input class="form-control" name="director">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-12 col-lg-6">
            <label class="form-label">Poster (path / url)</label>
            <input class="form-control" name="poster" placeholder="img/movies/title.jpg или https://...">
            <div class="form-text text-muted">Если путь относительный — лучше как: <code>img/movies/...</code></div>
          </div>
          <div class="col-12 col-lg-6">
            <label class="form-label">YouTube trailer id</label>
            <input class="form-control" name="youtube_trailer_id" placeholder="dQw4w9WgXcQ">
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button class="btn btn-success">
            <i class="bi bi-check2-circle me-1"></i> Сохранить
          </button>
          <a class="btn btn-outline-secondary ms-auto" href="moviesAdmin">Отмена</a>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="fh-card p-3">
          <div class="fw-semibold mb-2">
            <i class="bi bi-image me-1"></i> Предпросмотр постера
          </div>
          <img id="fhPosterPreview" class="fh-poster d-none" style="width:100%;height:380px;border-radius:14px;">
          <div class="text-muted small mt-2">Появится автоматически при вводе Poster.</div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
