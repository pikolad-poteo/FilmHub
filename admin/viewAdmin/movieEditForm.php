<?php
$pageTitle = 'Edit Movie';
ob_start();
?>
<h1>Редактировать фильм #<?= (int)$m['id'] ?></h1>

<form method="post" action="movieEditResult?id=<?= (int)$m['id'] ?>">
  <div>Title*: <br><input name="title" value="<?= htmlspecialchars($m['title'] ?? '') ?>"></div>
  <div>Original title: <br><input name="original_title" value="<?= htmlspecialchars($m['original_title'] ?? '') ?>"></div>
  <div>Description: <br><textarea name="description" rows="4" cols="60"><?= htmlspecialchars($m['description'] ?? '') ?></textarea></div>
  <div>Year: <br><input name="year" type="number" value="<?= htmlspecialchars((string)($m['year'] ?? '')) ?>"></div>
  <div>Duration (min): <br><input name="duration_minutes" type="number" value="<?= htmlspecialchars((string)($m['duration_minutes'] ?? '')) ?>"></div>
  <div>Country: <br><input name="country" value="<?= htmlspecialchars($m['country'] ?? '') ?>"></div>
  <div>Director: <br><input name="director" value="<?= htmlspecialchars($m['director'] ?? '') ?>"></div>
  <div>Poster: <br><input name="poster" value="<?= htmlspecialchars($m['poster'] ?? '') ?>"></div>
  <div>YouTube trailer id: <br><input name="youtube_trailer_id" value="<?= htmlspecialchars($m['youtube_trailer_id'] ?? '') ?>"></div>

  <div>Genre:
    <br>
    <select name="genre_id">
      <option value="">-- none --</option>
      <?php foreach (($genres ?? []) as $g): ?>
        <option value="<?= (int)$g['id'] ?>" <?= ((int)($m['genre_id'] ?? 0) === (int)$g['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($g['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div style="margin-top:10px;">
    <button type="submit">Сохранить</button>
    <a href="moviesAdmin">Отмена</a>
  </div>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
