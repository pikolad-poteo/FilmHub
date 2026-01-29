<?php
class modelAdminMovies
{
    private static function normalizePoster(?string $poster): ?string
    {
        $p = trim((string)$poster);
        if ($p === '') return null;

        // внешняя ссылка допустима
        if (preg_match('~^https?://~i', $p)) return $p;

        // ✅ всегда сохраняем только имя файла
        return basename($p);
    }

    public static function getAllMovies(): array {
        $db = new Database();
        return $db->getAll("
            SELECT m.*,
                   g.name AS genre_name
            FROM movies m
            LEFT JOIN genres g ON g.id = m.genre_id
            ORDER BY m.id DESC
        ");
    }

    public static function getMovieByID(int $id): ?array {
        $db = new Database();
        return $db->getOne("
            SELECT * FROM movies WHERE id = :id LIMIT 1
        ", [':id' => $id]);
    }

    public static function createFromPost(): bool {
        $title = trim((string)($_POST['title'] ?? ''));
        if ($title === '') return false;

        $db = new Database();

        $sql = "
            INSERT INTO movies
            (title, original_title, description, year, duration_minutes, country, director, poster, youtube_trailer_id, genre_id, created_at, updated_at)
            VALUES
            (:title, :ot, :descr, :year, :dur, :country, :director, :poster, :yt, :gid, NOW(), NOW())
        ";

        $params = [
            ':title'    => $title,
            ':ot'       => trim((string)($_POST['original_title'] ?? '')) ?: null,
            ':descr'    => trim((string)($_POST['description'] ?? '')) ?: null,
            ':year'     => ($_POST['year'] ?? '') !== '' ? (int)$_POST['year'] : null,
            ':dur'      => ($_POST['duration_minutes'] ?? '') !== '' ? (int)$_POST['duration_minutes'] : null,
            ':country'  => trim((string)($_POST['country'] ?? '')) ?: null,
            ':director' => trim((string)($_POST['director'] ?? '')) ?: null,
            ':poster'   => self::normalizePoster($_POST['poster'] ?? null),
            ':yt'       => trim((string)($_POST['youtube_trailer_id'] ?? '')) ?: null,
            ':gid'      => ($_POST['genre_id'] ?? '') !== '' ? (int)$_POST['genre_id'] : null,
        ];

        return $db->executeRun($sql, $params);
    }

    public static function updateFromPost(int $id): bool {
        $title = trim((string)($_POST['title'] ?? ''));
        if ($title === '') return false;

        $db = new Database();

        $sql = "
            UPDATE movies
            SET title = :title,
                original_title = :ot,
                description = :descr,
                year = :year,
                duration_minutes = :dur,
                country = :country,
                director = :director,
                poster = :poster,
                youtube_trailer_id = :yt,
                genre_id = :gid,
                updated_at = NOW()
            WHERE id = :id
        ";

        $params = [
            ':id'       => $id,
            ':title'    => $title,
            ':ot'       => trim((string)($_POST['original_title'] ?? '')) ?: null,
            ':descr'    => trim((string)($_POST['description'] ?? '')) ?: null,
            ':year'     => ($_POST['year'] ?? '') !== '' ? (int)$_POST['year'] : null,
            ':dur'      => ($_POST['duration_minutes'] ?? '') !== '' ? (int)$_POST['duration_minutes'] : null,
            ':country'  => trim((string)($_POST['country'] ?? '')) ?: null,
            ':director' => trim((string)($_POST['director'] ?? '')) ?: null,
            ':poster'   => self::normalizePoster($_POST['poster'] ?? null),
            ':yt'       => trim((string)($_POST['youtube_trailer_id'] ?? '')) ?: null,
            ':gid'      => ($_POST['genre_id'] ?? '') !== '' ? (int)$_POST['genre_id'] : null,
        ];

        return $db->executeRun($sql, $params);
    }

    public static function deleteByID(int $id): bool {
        $db = new Database();
        return $db->executeRun("DELETE FROM movies WHERE id = :id", [':id' => $id]);
    }

    // (опционально) алиас, если где-то уже зовёшь deleteMovie()
    public static function deleteMovie(int $id): bool {
        return self::deleteByID($id);
    }


}
