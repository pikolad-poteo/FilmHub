<?php

class CommentModel extends BaseModel
{
    public static function insertComment(int $userId, int $movieId, string $text): bool
    {
        $text = trim($text);
        if ($text === '') return false;

        $db = self::db();
        $sql = "
            INSERT INTO comments (movie_id, user_id, text, status, created_at, updated_at)
            VALUES (:mid, :uid, :txt, 'visible', NOW(), NOW())
        ";
        return $db->executeRun($sql, [
            ':mid' => $movieId,
            ':uid' => $userId,
            ':txt' => $text,
        ]);
    }

    public static function getVisibleCommentsByMovieID(int $movieId): array
    {
        $db = self::db();
        $sql = "
            SELECT c.*,
                   u.login AS user_login
            FROM comments c
            LEFT JOIN users u ON u.id = c.user_id
            WHERE c.movie_id = :mid
              AND c.status = 'visible'
            ORDER BY c.id DESC
        ";
        return $db->getAll($sql, [':mid' => $movieId]);
    }

    public static function getCommentCountByMovieID(int $movieId): int
    {
        $db = self::db();
        $row = $db->getOne("
            SELECT COUNT(id) AS cnt
            FROM comments
            WHERE movie_id = :mid
              AND status = 'visible'
        ", [':mid' => $movieId]);

        return (int)($row['cnt'] ?? 0);
    }

    /* ===== для админки (модерация) ===== */
    public static function getAllComments(): array
    {
        $db = self::db();
        $sql = "
            SELECT c.*,
                   u.login AS user_login,
                   m.title AS movie_title
            FROM comments c
            LEFT JOIN users u ON u.id = c.user_id
            LEFT JOIN movies m ON m.id = c.movie_id
            ORDER BY c.id DESC
        ";
        return $db->getAll($sql);
    }

    public static function setStatus(int $commentId, string $status): bool
    {
        $status = ($status === 'hidden') ? 'hidden' : 'visible';

        $db = self::db();
        return $db->executeRun("
            UPDATE comments
            SET status = :st, updated_at = NOW()
            WHERE id = :id
        ", [
            ':st' => $status,
            ':id' => $commentId,
        ]);
    }
}
