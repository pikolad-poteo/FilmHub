<?php
class modelAdminUsers
{
    public static function getAllUsers(): array {
        $db = new Database();
        return $db->getAll("
            SELECT id, login, email, role, created_at
            FROM users
            ORDER BY id DESC
        ");
    }
    public static function getUserByID(int $id): ?array
    {
        $db = new Database();
        return $db->getOne("
            SELECT id, login, email, role, created_at
            FROM users
            WHERE id = :id
            LIMIT 1
        ", [':id' => $id]);
    }

    public static function deleteUser(int $id): bool
    {
        $db = new Database();
        // В твоей схеме FK на comments/favorites/ratings стоят ON DELETE CASCADE,
        // поэтому удаление пользователя автоматически удалит его данные.
        return $db->executeRun("DELETE FROM users WHERE id = :id", [':id' => $id]);
    }
}
