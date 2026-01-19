<?php
// model/UserModel.php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT id, login, email, role, avatar, created_at, updated_at
             FROM users
             WHERE id = :id
             LIMIT 1",
            [':id' => $id]
        );
    }

    public function findByLogin(string $login): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE login = :login LIMIT 1",
            [':login' => $login]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            [':email' => $email]
        );
    }

    public function findByLoginOrEmail(string $loginOrEmail): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM users
             WHERE login = :v OR email = :v
             LIMIT 1",
            [':v' => $loginOrEmail]
        );
    }

    public function createUser(string $login, string $email, string $plainPassword, string $role = 'user', ?string $avatar = null): int
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);

        $this->db->exec(
            "INSERT INTO users (login, email, password, role, avatar, created_at, updated_at)
             VALUES (:login, :email, :password, :role, :avatar, NOW(), NOW())",
            [
                ':login'    => $login,
                ':email'    => $email,
                ':password' => $hash,
                ':role'     => $role,
                ':avatar'   => $avatar,
            ]
        );

        return $this->db->lastInsertId();
    }

    public function verifyPassword(array $userRow, string $plainPassword): bool
    {
        if (!isset($userRow['password'])) {
            return false;
        }
        return password_verify($plainPassword, (string)$userRow['password']);
    }

    public function updateAvatar(int $userId, ?string $avatarPath): bool
    {
        $affected = $this->db->exec(
            "UPDATE users SET avatar = :avatar, updated_at = NOW() WHERE id = :id",
            [':avatar' => $avatarPath, ':id' => $userId]
        );
        return $affected > 0;
    }

    public function updatePassword(int $userId, string $newPlainPassword): bool
    {
        $hash = password_hash($newPlainPassword, PASSWORD_BCRYPT);

        $affected = $this->db->exec(
            "UPDATE users SET password = :p, updated_at = NOW() WHERE id = :id",
            [':p' => $hash, ':id' => $userId]
        );
        return $affected > 0;
    }
}