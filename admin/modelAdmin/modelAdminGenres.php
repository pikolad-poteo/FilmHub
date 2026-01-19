<?php
class modelAdminGenres
{
    public static function getAllGenres(): array {
        $db = new Database();
        return $db->getAll("SELECT id, name, slug, created_at FROM genres ORDER BY name ASC");
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
        return $db->executeRun("DELETE FROM genres WHERE id = :id", [':id' => $id]);
    }
}
