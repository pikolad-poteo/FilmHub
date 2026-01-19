<?php
// model/GenreModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class GenreModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, slug, created_at
             FROM genres
             ORDER BY name ASC"
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, name, slug, created_at
             FROM genres
             WHERE id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, name, slug, created_at
             FROM genres
             WHERE slug = :slug
             LIMIT 1",
            [':slug' => $slug]
        );
    }

    public function create(string $name, string $slug): int
    {
        $this->db->exec(
            "INSERT INTO genres (name, slug, created_at)
             VALUES (:name, :slug, NOW())",
            [':name' => $name, ':slug' => $slug]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $slug): bool
    {
        $affected = $this->db->exec(
            "UPDATE genres SET name = :name, slug = :slug WHERE id = :id",
            [':name' => $name, ':slug' => $slug, ':id' => $id]
        );
        return $affected > 0;
    }

    public function delete(int $id): bool
    {
        $affected = $this->db->exec(
            "DELETE FROM genres WHERE id = :id",
            [':id' => $id]
        );
        return $affected > 0;
    }
}
