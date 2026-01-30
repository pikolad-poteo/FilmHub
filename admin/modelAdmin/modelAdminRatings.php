<?php
class modelAdminRatings
{
    public static function getAllRatings(): array {
        $db = new Database();
        return $db->getAll("
            SELECT r.id, r.rating, r.created_at, r.updated_at,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM ratings r
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN movies m ON m.id = r.movie_id
            ORDER BY r.id DESC
        ");
    }
        public static function deleteById(int $id): bool {
        $db = new Database();

        // Попробуем получить movie_id для пересчёта статистики
        $movieId = 0;
        try {
            $row = $db->getOne("SELECT movie_id FROM ratings WHERE id = :id LIMIT 1", [':id' => $id]);
            $movieId = (int)($row['movie_id'] ?? 0);
        } catch (Throwable $e) {
            $movieId = 0;
        }

        try {
            $db->executeRun("DELETE FROM ratings WHERE id = :id LIMIT 1", [':id' => $id]);
        } catch (Throwable $e) {
            return false;
        }

        // Если есть movie_rating_stats — обновим, чтобы цифры не зависли
        if ($movieId > 0) {
            try {
                $db->executeRun(
                    "UPDATE movie_rating_stats s
                    JOIN (
                        SELECT movie_id,
                            COALESCE(AVG(rating), 0) AS a,
                            COUNT(*) AS c
                        FROM ratings
                        WHERE movie_id = :mid
                        GROUP BY movie_id
                    ) x ON x.movie_id = s.movie_id
                    SET s.rating_avg = x.a, s.rating_count = x.c",
                    [':mid' => $movieId]
                );

                $db->executeRun(
                    "UPDATE movie_rating_stats
                    SET rating_avg = 0, rating_count = 0
                    WHERE movie_id = :mid
                    AND NOT EXISTS (SELECT 1 FROM ratings WHERE movie_id = :mid)",
                    [':mid' => $movieId]
                );
            } catch (Throwable $e) {
                // таблицы может не быть — игнорируем
            }
        }

        return true;
    }
}
