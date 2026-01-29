<?php
class modelAdminGenres
{
    /**
     * ЖАНРЫ + количество фильмов
     */
    public static function getAllGenres(): array {
        $db = new Database();

        return $db->getAll("
            SELECT
                g.id,
                g.name,
                g.slug,
                g.created_at,
                COUNT(m.id) AS movies_count
            FROM genres g
            LEFT JOIN movies m ON m.genre_id = g.id
            GROUP BY g.id, g.name, g.slug, g.created_at
            ORDER BY g.name ASC
        ");
    }

    private static function slugify(string $s): string {
        $s = mb_strtolower(trim($s));
        $s = preg_replace('~[^a-z0-9а-яё]+~u', '-', $s);
        $s = trim($s, '-');
        // простая замена ё
        $s = str_replace('ё', 'е', $s);
        return $s ?: 'genre';
    }

    public static function createFromPost(): bool {
        $name = trim((string)($_POST['name'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));

        if ($name === '') return false;
        if ($slug === '') $slug = self::slugify($name);

        $db = new Database();
        return $db->executeRun("
            INSERT INTO genres (name, slug, created_at)
            VALUES (:n, :s, NOW())
        ", [
            ':n' => $name,
            ':s' => $slug
        ]);
    }

    public static function deleteGenre(int $id): bool {
        $db = new Database();
        return $db->executeRun(
            "DELETE FROM genres WHERE id = :id",
            [':id' => $id]
        );
    }
}
