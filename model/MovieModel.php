<?php

class MovieModel
{
    public static function getLastMovies(int $limit = 10): array
    {
        $limit = max(1, min(50, (int)$limit));
        $db = new Database();

        $sql = "
            SELECT m.*,
                   g.name AS genre_name,
                   g.slug AS genre_slug
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            ORDER BY m.id DESC
            LIMIT {$limit}
        ";

        return $db->getAll($sql);
    }

    public static function getAllMovies(): array
    {
        $db = new Database();

        $sql = "
            SELECT m.*,
                   g.name AS genre_name,
                   g.slug AS genre_slug,
                   s.rating_avg,
                   s.rating_count
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            ORDER BY m.id DESC
        ";

        return $db->getAll($sql);
    }

    public static function getMovieByID(int $id): ?array
    {
        $db = new Database();

        $sql = "
            SELECT m.*,
                   g.name AS genre_name,
                   g.slug AS genre_slug,
                   s.rating_avg,
                   s.rating_count
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE m.id = :id
            LIMIT 1
        ";

        return $db->getOne($sql, [':id' => $id]);
    }

    public static function getMoviesByGenreID(int $genreId): array
    {
        $db = new Database();

        $sql = "
            SELECT m.*,
                   g.name AS genre_name,
                   g.slug AS genre_slug,
                   s.rating_avg,
                   s.rating_count
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            LEFT JOIN movie_rating_stats s ON s.movie_id = m.id
            WHERE m.genre_id = :gid
            ORDER BY m.id DESC
        ";

        return $db->getAll($sql, [':gid' => $genreId]);
    }
}
