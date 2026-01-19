<?php
$pageTitle = 'Фильм';

ob_start();
?>
<?php if (empty($m)): ?>
    <h1>Фильм не найден</h1>
<?php else: ?>

    <h1><?= htmlspecialchars($m['title'] ?? '') ?></h1>

    <p>
        <b>Оригинальное название:</b> <?= htmlspecialchars($m['original_title'] ?? '-') ?><br>
        <b>Год:</b> <?= !empty($m['year']) ? (int)$m['year'] : '-' ?><br>
        <b>Длительность:</b> <?= !empty($m['duration_minutes']) ? (int)$m['duration_minutes'] . ' мин.' : '-' ?><br>
        <b>Страна:</b> <?= htmlspecialchars($m['country'] ?? '-') ?><br>
        <b>Режиссёр:</b> <?= htmlspecialchars($m['director'] ?? '-') ?><br>
        <b>Жанр:</b> <?= htmlspecialchars($m['genre_name'] ?? '-') ?><br>
    </p>

    <?php if (!empty($m['description'])): ?>
        <p><b>Описание:</b><br><?= nl2br(htmlspecialchars($m['description'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($m['youtube_trailer_id'])): ?>
        <p><b>Трейлер (YouTube ID):</b> <?= htmlspecialchars($m['youtube_trailer_id']) ?></p>
        <p>
            <a target="_blank" href="https://www.youtube.com/watch?v=<?= urlencode($m['youtube_trailer_id']) ?>">
                Открыть трейлер на YouTube
            </a>
        </p>
    <?php endif; ?>

    <hr>

    <div id="rating">
        <h2>Рейтинг</h2>
        <?php
            $avg = $ratingStats['rating_avg'] ?? null;
            $cnt = $ratingStats['rating_count'] ?? 0;
        ?>
        <p>
            <b>Средний:</b> <?= ($avg === null) ? '-' : htmlspecialchars($avg) ?>
            <b>Оценок:</b> <?= (int)$cnt ?>
        </p>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <p><b>Ваша оценка:</b> <?= ($myRating === null) ? 'нет' : (int)$myRating ?></p>
            <p>Поставить:</p>
            <?php for ($i=1; $i<=10; $i++): ?>
                <a href="rate?id=<?= (int)$m['id'] ?>&rating=<?= $i ?>"><?= $i ?></a>
                <?= ($i<10) ? ' | ' : '' ?>
            <?php endfor; ?>
        <?php else: ?>
            <p><a href="loginForm">Войдите</a>, чтобы поставить оценку.</p>
        <?php endif; ?>
    </div>

    <hr>

    <div>
        <h2>Избранное</h2>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <?php if (!empty($isFav)): ?>
                <p>✅ В избранном</p>
            <?php else: ?>
                <p>❌ Не в избранном</p>
            <?php endif; ?>
            <p>
                <a href="favorite?id=<?= (int)$m['id'] ?>">
                    <?= !empty($isFav) ? 'Убрать из избранного' : 'Добавить в избранное' ?>
                </a>
            </p>
        <?php else: ?>
            <p><a href="loginForm">Войдите</a>, чтобы добавлять в избранное.</p>
        <?php endif; ?>
    </div>

    <hr>

    <div id="ctable">
        <h2>Комментарии</h2>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <form method="get" action="insertcomment">
                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <textarea name="comment" rows="3" cols="60" placeholder="Ваш комментарий..."></textarea><br>
                <button type="submit">Отправить</button>
            </form>
        <?php else: ?>
            <p><a href="loginForm">Войдите</a>, чтобы писать комментарии.</p>
        <?php endif; ?>

        <h3>Список</h3>
        <?php if (empty($comments)): ?>
            <p>Комментариев пока нет.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($comments as $c): ?>
                    <li>
                        <b><?= htmlspecialchars($c['user_login'] ?? 'user') ?>:</b>
                        <?= nl2br(htmlspecialchars($c['text'] ?? '')) ?>
                        <small style="color:#777;">(<?= htmlspecialchars($c['created_at'] ?? '') ?>)</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
