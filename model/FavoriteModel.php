<?php

class FavoriteModel extends BaseModel
{
    public static function isFavorite(int $userId, int $movieId): bool
    {
        $db = self::db();
        $row = $db->getOne("
            SELECT id
            FROM favorites
            WHERE user_id = :uid AND movie_id = :mid
            LIMIT 1
        ", [
            ':uid' => $userId,
            ':mid' => $movieId
        ]);

        return !empty($row);
    }

    public static function add(int $userId, int $movieId): bool
    {
        $db = self::db();

        // Если есть уникальный ключ — можно ON DUPLICATE KEY.
        // Если нет — сначала проверяем, потом вставляем.
        $sql = "
            INSERT INTO favorites (user_id, movie_id, created_at)
            VALUES (:uid, :mid, NOW())
        ";
        try {
            return $db->executeRun($sql, [':uid' => $userId, ':mid' => $movieId]);
        } catch (Throwable $e) {
            // если уже существует — считаем успехом
            return true;
        }
    }

    public static function remove(int $userId, int $movieId): bool
    {
        $db = self::db();
        return $db->executeRun("
            DELETE FROM favorites
            WHERE user_id = :uid AND movie_id = :mid
        ", [
            ':uid' => $userId,
            ':mid' => $movieId
        ]);
    }

    public static function toggle(int $userId, int $movieId): bool
    {
        if (self::isFavorite($userId, $movieId)) {
            return self::remove($userId, $movieId);
        }
        return self::add($userId, $movieId);
    }

    public static function getUserFavorites(int $userId): array
    {
        $db = self::db();
        $sql = "
            SELECT m.*,
                   g.name AS genre_name,
                   s.rating_avg,
                   s.rating_count,
                   f.created_at AS favorited_at
            FROM favorites f
            INNER JOIN movies m ON m.id = f.movie_id
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE f.user_id = :uid
            ORDER BY f.id DESC
        ";
        return $db->getAll($sql, [':uid' => $userId]);
    }

    /* ===== для админки ===== */
    public static function getAllFavorites(): array
    {
        $db = self::db();
        $sql = "
            SELECT f.*,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM favorites f
            LEFT JOIN users u ON u.id = f.user_id
            LEFT JOIN movies m ON m.id = f.movie_id
            ORDER BY f.id DESC
        ";
        return $db->getAll($sql);
    }
}
