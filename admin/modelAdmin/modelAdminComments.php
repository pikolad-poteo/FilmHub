<?php
class modelAdminComments
{
    public static function getAllComments(): array {
        $db = new Database();
        return $db->getAll("
            SELECT c.*,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM comments c
            LEFT JOIN users u ON u.id = c.user_id
            LEFT JOIN movies m ON m.id = c.movie_id
            ORDER BY c.id DESC
        ");
    }

    public static function toggleStatus(int $id): bool {
        $db = new Database();
        $row = $db->getOne("SELECT status FROM comments WHERE id = :id LIMIT 1", [':id' => $id]);
        if (!$row) return false;

        $new = ($row['status'] === 'visible') ? 'hidden' : 'visible';

        return $db->executeRun("
            UPDATE comments
            SET status = :st, updated_at = NOW()
            WHERE id = :id
        ", [
            ':st' => $new,
            ':id' => $id
        ]);
    }
}
