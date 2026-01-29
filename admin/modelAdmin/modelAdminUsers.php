<?php
class modelAdminUsers
{
    private static ?bool $hasAvatarCol = null;

    private static function avatarColumnExists(Database $db): bool
    {
        if (self::$hasAvatarCol !== null) return self::$hasAvatarCol;

        try {
            $row = $db->getOne(
                "SELECT 1 AS ok
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'users'
                   AND COLUMN_NAME = 'avatar'
                 LIMIT 1"
            );
            self::$hasAvatarCol = (bool)$row;
        } catch (Throwable $e) {
            self::$hasAvatarCol = false;
        }

        return self::$hasAvatarCol;
    }

    public static function getAllUsers(): array
    {
        $db = new Database();
        $select = "id, login, email, role, created_at";
        if (self::avatarColumnExists($db)) $select .= ", avatar";

        return $db->getAll("
            SELECT {$select}
            FROM users
            ORDER BY id DESC
        ");
    }

    public static function getUserByID(int $id): ?array
    {
        $db = new Database();
        $select = "id, login, email, role, created_at";
        if (self::avatarColumnExists($db)) $select .= ", avatar";

        return $db->getOne("
            SELECT {$select}
            FROM users
            WHERE id = :id
            LIMIT 1
        ", [':id' => $id]);
    }

    public static function deleteUser(int $id): bool
    {
        $db = new Database();
        return $db->executeRun("DELETE FROM users WHERE id = :id", [':id' => $id]);
    }
}
