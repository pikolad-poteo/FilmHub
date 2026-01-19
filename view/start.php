<?php
$pageTitle = 'FilmHub — Главная';

ob_start();
?>
<h1>Последние фильмы</h1>

<?php if (empty($arr)): ?>
    <p>Пока нет фильмов в базе.</p>
<?php else: ?>
    <ul>
        <?php foreach ($arr as $m): ?>
            <li>
                <a href="movie?id=<?= (int)$m['id'] ?>">
                    <?= htmlspecialchars($m['title'] ?? 'Без названия') ?>
                </a>
                <?php if (!empty($m['year'])): ?>
                    (<?= (int)$m['year'] ?>)
                <?php endif; ?>
                <?php if (!empty($m['genre_name'])): ?>
                    — <?= htmlspecialchars($m['genre_name']) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="all">Перейти ко всем фильмам →</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
