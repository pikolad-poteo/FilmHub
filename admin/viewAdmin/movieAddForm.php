<?php
$pageTitle = 'Add Movie';
ob_start();
?>
<h1>Добавить фильм</h1>

<form method="post" action="movieAddResult">
  <div>Title*: <br><input name="title"></div>
  <div>Original title: <br><input name="original_title"></div>
  <div>Description: <br><textarea name="description" rows="4" cols="60"></textarea></div>
  <div>Year: <br><input name="year" type="number"></div>
  <div>Duration (min): <br><input name="duration_minutes" type="number"></div>
  <div>Country: <br><input name="country"></div>
  <div>Director: <br><input name="director"></div>
  <div>Poster (filename/url): <br><input name="poster"></div>
  <div>YouTube trailer id: <br><input name="youtube_trailer_id"></div>

  <div>Genre:
    <br>
    <select name="genre_id">
      <option value="">-- none --</option>
      <?php foreach (($genres ?? []) as $g): ?>
        <option value="<?= (int)$g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
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
