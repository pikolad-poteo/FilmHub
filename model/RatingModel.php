<?php
// model/RatingModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class RatingModel extends BaseModel
{
    public function getUserRating(int $userId, int $movieId): ?int
    {
        $row = $this->db->fetchOne(
            "SELECT rating FROM ratings WHERE user_id = :u AND movie_id = :m LIMIT 1",
            [':u' => $userId, ':m' => $movieId]
        );

        return $row ? (int)$row['rating'] : null;
    }

    public function setRating(int $userId, int $movieId, int $rating): void
    {
        if ($rating < 1 || $rating > 10) {
            throw new InvalidArgumentException('Рейтинг должен быть от 1 до 10.');
        }

        // Ставим/обновляем рейтинг и пересчитываем stats атомарно
        try {
            $this->db->begin();

            $this->db->exec(
                "INSERT INTO ratings (user_id, movie_id, rating, created_at, updated_at)
                 VALUES (:u, :m, :r, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE rating = VALUES(rating), updated_at = VALUES(updated_at)",
                [':u' => $userId, ':m' => $movieId, ':r' => $rating]
            );

            $this->recalcStats($movieId);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function removeRating(int $userId, int $movieId): void
    {
        try {
            $this->db->begin();

            $this->db->exec(
                "DELETE FROM ratings WHERE user_id = :u AND movie_id = :m",
                [':u' => $userId, ':m' => $movieId]
            );

            $this->recalcStats($movieId);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function recalcStats(int $movieId): void
    {
        // Если оценок нет — можно хранить 0/0 либо удалить строку.
        $row = $this->db->fetchOne(
            "SELECT ROUND(AVG(rating), 2) AS avg_rating, COUNT(*) AS cnt
             FROM ratings
             WHERE movie_id = :m",
            [':m' => $movieId]
        );

        $avg = $row ? (float)$row['avg_rating'] : 0.0;
        $cnt = $row ? (int)$row['cnt'] : 0;

        if ($cnt === 0) {
            // вариант: удалить stats, чтобы LEFT JOIN давал NULL -> COALESCE -> 0
            $this->db->exec("DELETE FROM movie_rating_stats WHERE movie_id = :m", [':m' => $movieId]);
            return;
        }

        $this->db->exec(
            "INSERT INTO movie_rating_stats (movie_id, rating_avg, rating_count)
             VALUES (:m, :avg, :cnt)
             ON DUPLICATE KEY UPDATE rating_avg = VALUES(rating_avg), rating_count = VALUES(rating_count)",
            [':m' => $movieId, ':avg' => $avg, ':cnt' => $cnt]
        );
    }

    public function adminListAll(int $limit = 200, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $sql = "
            SELECT
              r.id,
              r.rating,
              r.created_at,
              r.updated_at,
              u.id AS user_id, u.login AS user_login,
              m.id AS movie_id, m.title AS movie_title
            FROM ratings r
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN movies m ON m.id = r.movie_id
            ORDER BY r.updated_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->db->fetchAll($sql);
    }
}
