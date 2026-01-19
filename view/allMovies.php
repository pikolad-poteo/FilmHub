<?php
$pageTitle = 'Все фильмы';

ob_start();
?>
<h1>Все фильмы</h1>

<?php if (empty($arr)): ?>
    <p>Фильмы не найдены.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Год</th>
            <th>Жанр</th>
            <th>Рейтинг</th>
        </tr>
        <?php foreach ($arr as $m): ?>
            <tr>
                <td><?= (int)$m['id'] ?></td>
                <td>
                    <a href="movie?id=<?= (int)$m['id'] ?>">
                        <?= htmlspecialchars($m['title'] ?? '') ?>
                    </a>
                </td>
                <td><?= !empty($m['year']) ? (int)$m['year'] : '-' ?></td>
                <td><?= !empty($m['genre_name']) ? htmlspecialchars($m['genre_name']) : '-' ?></td>
                <td>
                    <?php
                        $avg = $m['rating_avg'] ?? null;
                        $cnt = $m['rating_count'] ?? 0;
                        if ($avg === null) echo '-';
                        else echo htmlspecialchars($avg) . " (" . (int)$cnt . ")";
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
