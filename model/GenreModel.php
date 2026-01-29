<?php

class GenreModel extends BaseModel
{
    public static function getAllGenres(): array
    {
        $db = self::db();
        return $db->getAll("SELECT id, name, slug, created_at FROM genres ORDER BY name ASC");
    }

    public static function getGenreByID(int $id): ?array
    {
        $db = self::db();
        return $db->getOne("SELECT id, name, slug, created_at FROM genres WHERE id = :id LIMIT 1", [
            ':id' => $id
        ]);
    }

    public static function getGenreBySlug(string $slug): ?array
    {
        $db = self::db();
        return $db->getOne("SELECT id, name, slug, created_at FROM genres WHERE slug = :s LIMIT 1", [
            ':s' => $slug
        ]);
    }
}
