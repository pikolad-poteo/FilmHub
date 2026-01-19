<?php
$pageTitle = 'Жанры';

ob_start();
?>
<h1>Жанры</h1>

<?php if (!empty($genre) && is_array($genre)): ?>
    <h2>Фильмы жанра: <?= htmlspecialchars($genre['name'] ?? '') ?></h2>
<?php endif; ?>

<?php if (empty($arr)): ?>
    <p>Нет данных.</p>
<?php else: ?>

    <?php
    // Если в $arr есть поле "slug" — это жанры, иначе — фильмы
    $isGenresList = isset($arr[0]['slug']);
    ?>

    <?php if ($isGenresList): ?>
        <ul>
            <?php foreach ($arr as $g): ?>
                <li>
                    <a href="genre?id=<?= (int)$g['id'] ?>">
                        <?= htmlspecialchars($g['name'] ?? '') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <ul>
            <?php foreach ($arr as $m): ?>
                <li>
                    <a href="movie?id=<?= (int)$m['id'] ?>">
                        <?= htmlspecialchars($m['title'] ?? '') ?>
                    </a>
                    <?php if (!empty($m['year'])): ?>
                        (<?= (int)$m['year'] ?>)
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><a href="genres">← ко всем жанрам</a></p>
    <?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
