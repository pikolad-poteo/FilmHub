<?php
// model/CommentModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class CommentModel extends BaseModel
{
    public function getByMovieId(int $movieId, bool $includeHidden = false): array
    {
        $where = $includeHidden ? "" : "AND c.status = 'visible'";

        $sql = "
            SELECT
              c.*,
              u.login AS user_login,
              u.avatar AS user_avatar
            FROM comments c
            INNER JOIN users u ON u.id = c.user_id
            WHERE c.movie_id = :movie_id
            {$where}
            ORDER BY c.created_at DESC
        ";

        return $this->db->fetchAll($sql, [':movie_id' => $movieId]);
    }

    public function add(int $movieId, int $userId, string $text, string $status = 'visible'): int
    {
        $text = trim($text);
        if ($text === '') {
            throw new InvalidArgumentException('Комментарий не может быть пустым.');
        }

        $this->db->exec(
            "INSERT INTO comments (movie_id, user_id, text, status, created_at, updated_at)
             VALUES (:movie_id, :user_id, :text, :status, NOW(), NOW())",
            [
                ':movie_id' => $movieId,
                ':user_id'  => $userId,
                ':text'     => $text,
                ':status'   => $status,
            ]
        );

        return $this->db->lastInsertId();
    }

    public function setStatus(int $commentId, string $status): bool
    {
        if (!in_array($status, ['visible', 'hidden'], true)) {
            throw new InvalidArgumentException('Некорректный статус комментария.');
        }

        $affected = $this->db->exec(
            "UPDATE comments SET status = :status, updated_at = NOW() WHERE id = :id",
            [':status' => $status, ':id' => $commentId]
        );

        return $affected > 0;
    }

    public function delete(int $commentId): bool
    {
        $affected = $this->db->exec(
            "DELETE FROM comments WHERE id = :id",
            [':id' => $commentId]
        );
        return $affected > 0;
    }
}
