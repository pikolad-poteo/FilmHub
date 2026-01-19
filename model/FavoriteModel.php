<?php
// model/FavoriteModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class FavoriteModel extends BaseModel
{
    public function isFavorite(int $userId, int $movieId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM favorites WHERE user_id = :u AND movie_id = :m LIMIT 1",
            [':u' => $userId, ':m' => $movieId]
        );
        return $row !== null;
    }

    public function add(int $userId, int $movieId): void
    {
        // Требует UNIQUE(user_id, movie_id) в favorites
        $this->db->exec(
            "INSERT INTO favorites (user_id, movie_id, created_at)
             VALUES (:u, :m, NOW())
             ON DUPLICATE KEY UPDATE created_at = VALUES(created_at)",
            [':u' => $userId, ':m' => $movieId]
        );
    }

    public function remove(int $userId, int $movieId): void
    {
        $this->db->exec(
            "DELETE FROM favorites WHERE user_id = :u AND movie_id = :m",
            [':u' => $userId, ':m' => $movieId]
        );
    }

    public function toggle(int $userId, int $movieId): bool
    {
        if ($this->isFavorite($userId, $movieId)) {
            $this->remove($userId, $movieId);
            return false;
        }
        $this->add($userId, $movieId);
        return true;
    }

    public function getUserFavorites(int $userId): array
    {
        $sql = "
            SELECT
              m.*,
              g.name AS genre_name, g.slug AS genre_slug,
              COALESCE(s.rating_avg, 0) AS rating_avg,
              COALESCE(s.rating_count, 0) AS rating_count,
              f.created_at AS favorited_at
            FROM favorites f
            INNER JOIN movies m ON m.id = f.movie_id
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE f.user_id = :u
            ORDER BY f.created_at DESC
        ";

        return $this->db->fetchAll($sql, [':u' => $userId]);
    }

    public function adminListAll(int $limit = 200, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $sql = "
            SELECT
              f.id,
              f.created_at,
              u.id AS user_id, u.login AS user_login,
              m.id AS movie_id, m.title AS movie_title
            FROM favorites f
            INNER JOIN users u ON u.id = f.user_id
            INNER JOIN movies m ON m.id = f.movie_id
            ORDER BY f.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->db->fetchAll($sql);
    }
}
