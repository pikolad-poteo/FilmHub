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
}
