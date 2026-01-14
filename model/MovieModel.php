<?php
// model/MovieModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class MovieModel extends BaseModel
{
    public function getAll(int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $sql = "
            SELECT
              m.*,
              g.name AS genre_name, g.slug AS genre_slug,
              COALESCE(s.rating_avg, 0) AS rating_avg,
              COALESCE(s.rating_count, 0) AS rating_count
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            ORDER BY m.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->db->fetchAll($sql);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT
              m.*,
              g.name AS genre_name, g.slug AS genre_slug,
              COALESCE(s.rating_avg, 0) AS rating_avg,
              COALESCE(s.rating_count, 0) AS rating_count
             FROM movies m
             LEFT JOIN genres g ON g.id = m.genre_id
             LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
             WHERE m.id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }

    public function getByGenreSlug(string $slug, int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $sql = "
            SELECT
              m.*,
              g.name AS genre_name, g.slug AS genre_slug,
              COALESCE(s.rating_avg, 0) AS rating_avg,
              COALESCE(s.rating_count, 0) AS rating_count
            FROM movies m
            INNER JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE g.slug = :slug
            ORDER BY m.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->db->fetchAll($sql, [':slug' => $slug]);
    }

    public function search(string $q, int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        $q = trim($q);
        if ($q === '') return [];

        // LIKE с экранированием специальных символов
        $like = '%' . addcslashes($q, "\\%_") . '%';

        $sql = "
            SELECT
              m.*,
              g.name AS genre_name, g.slug AS genre_slug,
              COALESCE(s.rating_avg, 0) AS rating_avg,
              COALESCE(s.rating_count, 0) AS rating_count
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE m.title LIKE :q OR m.original_title LIKE :q OR m.director LIKE :q
            ORDER BY m.created_at DESC
            LIMIT {$limit}
        ";

        return $this->db->fetchAll($sql, [':q' => $like]);
    }

    public function create(array $data): int
    {
        $this->db->exec(
            "INSERT INTO movies
             (title, original_title, description, year, duration_minutes, country, director, poster, youtube_trailer_id, genre_id, created_at, updated_at)
             VALUES
             (:title, :original_title, :description, :year, :duration_minutes, :country, :director, :poster, :youtube_trailer_id, :genre_id, NOW(), NOW())",
            [
                ':title'             => $data['title'] ?? '',
                ':original_title'    => $data['original_title'] ?? null,
                ':description'       => $data['description'] ?? null,
                ':year'              => $data['year'] ?? null,
                ':duration_minutes'  => $data['duration_minutes'] ?? null,
                ':country'           => $data['country'] ?? null,
                ':director'          => $data['director'] ?? null,
                ':poster'            => $data['poster'] ?? null,
                ':youtube_trailer_id'=> $data['youtube_trailer_id'] ?? null,
                ':genre_id'          => $data['genre_id'] ?? null,
            ]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $affected = $this->db->exec(
            "UPDATE movies SET
               title = :title,
               original_title = :original_title,
               description = :description,
               year = :year,
               duration_minutes = :duration_minutes,
               country = :country,
               director = :director,
               poster = :poster,
               youtube_trailer_id = :youtube_trailer_id,
               genre_id = :genre_id,
               updated_at = NOW()
             WHERE id = :id",
            [
                ':id'                => $id,
                ':title'             => $data['title'] ?? '',
                ':original_title'    => $data['original_title'] ?? null,
                ':description'       => $data['description'] ?? null,
                ':year'              => $data['year'] ?? null,
                ':duration_minutes'  => $data['duration_minutes'] ?? null,
                ':country'           => $data['country'] ?? null,
                ':director'          => $data['director'] ?? null,
                ':poster'            => $data['poster'] ?? null,
                ':youtube_trailer_id'=> $data['youtube_trailer_id'] ?? null,
                ':genre_id'          => $data['genre_id'] ?? null,
            ]
        );

        return $affected > 0;
    }

    public function delete(int $id): bool
    {
        // В зависимости от FK-правил может потребоваться сначала удалить favorites/ratings/comments.
        $affected = $this->db->exec(
            "DELETE FROM movies WHERE id = :id",
            [':id' => $id]
        );
        return $affected > 0;
    }
}
