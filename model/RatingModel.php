<?php

class RatingModel extends BaseModel
{
    public static function setRating(int $userId, int $movieId, int $rating): bool
    {
        $rating = max(1, min(10, (int)$rating));

        $db = self::db();

        // Обновить если есть, иначе вставить
        $existing = $db->getOne("
            SELECT id FROM ratings
            WHERE user_id = :uid AND movie_id = :mid
            LIMIT 1
        ", [
            ':uid' => $userId,
            ':mid' => $movieId
        ]);

        if ($existing) {
            $ok = $db->executeRun("
                UPDATE ratings
                SET rating = :r, updated_at = NOW()
                WHERE id = :id
            ", [
                ':r'  => $rating,
                ':id' => (int)$existing['id']
            ]);
        } else {
            $ok = $db->executeRun("
                INSERT INTO ratings (user_id, movie_id, rating, created_at, updated_at)
                VALUES (:uid, :mid, :r, NOW(), NOW())
            ", [
                ':uid' => $userId,
                ':mid' => $movieId,
                ':r'   => $rating
            ]);
        }

        if ($ok) {
            self::recalcStats($movieId);
        }

        return $ok;
    }

    public static function getUserRating(int $userId, int $movieId): ?int
    {
        $db = self::db();
        $row = $db->getOne("
            SELECT rating
            FROM ratings
            WHERE user_id = :uid AND movie_id = :mid
            LIMIT 1
        ", [
            ':uid' => $userId,
            ':mid' => $movieId
        ]);

        return $row ? (int)$row['rating'] : null;
    }

    public static function getMovieStats(int $movieId): array
    {
        $db = self::db();
        $row = $db->getOne("
            SELECT rating_avg, rating_count
            FROM movie_rating_stats
            WHERE movie_id = :mid
            LIMIT 1
        ", [':mid' => $movieId]);

        if (!$row) {
            return ['rating_avg' => null, 'rating_count' => 0];
        }

        return [
            'rating_avg'   => $row['rating_avg'],
            'rating_count' => (int)$row['rating_count'],
        ];
    }

    private static function recalcStats(int $movieId): void
    {
        $db = self::db();

        $row = $db->getOne("
            SELECT AVG(rating) AS avg_rating, COUNT(*) AS cnt
            FROM ratings
            WHERE movie_id = :mid
        ", [':mid' => $movieId]);

        $avg = $row && $row['avg_rating'] !== null ? (float)$row['avg_rating'] : null;
        $cnt = (int)($row['cnt'] ?? 0);

        // upsert
        $exists = $db->getOne("SELECT movie_id FROM movie_rating_stats WHERE movie_id = :mid LIMIT 1", [
            ':mid' => $movieId
        ]);

        if ($exists) {
            $db->executeRun("
                UPDATE movie_rating_stats
                SET rating_avg = :a, rating_count = :c
                WHERE movie_id = :mid
            ", [
                ':a'   => $avg,
                ':c'   => $cnt,
                ':mid' => $movieId
            ]);
        } else {
            $db->executeRun("
                INSERT INTO movie_rating_stats (movie_id, rating_avg, rating_count)
                VALUES (:mid, :a, :c)
            ", [
                ':mid' => $movieId,
                ':a'   => $avg,
                ':c'   => $cnt
            ]);
        }
    }

    /* ===== для админки ===== */
    public static function getAllRatings(): array
    {
        $db = self::db();
        $sql = "
            SELECT r.*,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM ratings r
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN movies m ON m.id = r.movie_id
            ORDER BY r.id DESC
        ";
        return $db->getAll($sql);
    }
}
