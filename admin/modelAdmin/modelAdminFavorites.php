<?php
class modelAdminFavorites
{
    public static function getAllFavorites(): array {
        $db = new Database();
        return $db->getAll("
            SELECT f.id, f.created_at,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM favorites f
            LEFT JOIN users u ON u.id = f.user_id
            LEFT JOIN movies m ON m.id = f.movie_id
            ORDER BY f.id DESC
        ");
    }
        public static function deleteById(int $id): bool {
        $db = new Database();
        try {
            $db->executeRun("DELETE FROM favorites WHERE id = :id LIMIT 1", [':id' => $id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
