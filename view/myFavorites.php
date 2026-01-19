<?php
$pageTitle = 'Моё избранное';

ob_start();
?>
<h1>Моё избранное</h1>

<?php if (empty($arr)): ?>
    <p>Пусто.</p>
<?php else: ?>
    <ul>
        <?php foreach ($arr as $m): ?>
            <li>
                <a href="movie?id=<?= (int)$m['id'] ?>">
                    <?= htmlspecialchars($m['title'] ?? '') ?>
                </a>
                <?php if (!empty($m['year'])): ?> (<?= (int)$m['year'] ?>)<?php endif; ?>
                <?php if (!empty($m['genre_name'])): ?> — <?= htmlspecialchars($m['genre_name']) ?><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
