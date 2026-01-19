<?php

class UserModel extends BaseModel
{
    public static function registerFromPost(): array
    {
        // вернём формат как в NewsPortal: [true] либо [false, "ошибка..."]
        $result = [0 => false, 1 => 'error'];

        if (!isset($_POST['save'])) {
            return $result;
        }

        $errors = '';

        $login = trim((string)($_POST['login'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        $password = (string)($_POST['password'] ?? '');
        $confirm  = (string)($_POST['confirm'] ?? '');

        if ($login === '' || mb_strlen($login) < 3) {
            $errors .= "Логин должен быть минимум 3 символа<br />";
        }

        if (!$email) {
            $errors .= "Неправильный email<br />";
        }

        if (mb_strlen($password) < 6) {
            $errors .= "Пароль должен быть больше 6 символов<br />";
        }

        if ($password !== $confirm) {
            $errors .= "Пароли не совпадают<br />";
        }

        $db = self::db();

        if ($email) {
            $existsEmail = $db->getOne("SELECT id FROM users WHERE email = :e LIMIT 1", [':e' => $email]);
            if ($existsEmail) $errors .= "Email уже используется<br />";
        }

        if ($login !== '') {
            $existsLogin = $db->getOne("SELECT id FROM users WHERE login = :l LIMIT 1", [':l' => $login]);
            if ($existsLogin) $errors .= "Логин уже используется<br />";
        }

        if ($errors !== '') {
            return [0 => false, 1 => $errors];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $ok = $db->executeRun("
            INSERT INTO users (login, email, password, role, avatar, created_at, updated_at)
            VALUES (:l, :e, :p, 'user', NULL, NOW(), NOW())
        ", [
            ':l' => $login,
            ':e' => $email,
            ':p' => $hash
        ]);

        return $ok ? [0 => true] : [0 => false, 1 => 'error'];
    }

    public static function loginFromPost(): bool
    {
        // поддержка логина по email или login
        $loginOrEmail = trim((string)($_POST['email'] ?? '')); // оставил имя поля email как в newsportal admin
        $pass         = (string)($_POST['password'] ?? '');

        if ($loginOrEmail === '' || $pass === '') return false;

        $db = self::db();
        $user = $db->getOne("
            SELECT * FROM users
            WHERE email = :v OR login = :v
            LIMIT 1
        ", [':v' => $loginOrEmail]);

        if (!$user) return false;

        $hash = (string)($user['password'] ?? '');
        if ($hash === '') return false;

        if (!password_verify($pass, $hash)) return false;

        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['login']     = (string)$user['login'];
        $_SESSION['role']      = (string)$user['role'];
        $_SESSION['is_admin']  = ((string)$user['role'] === 'admin');
        $_SESSION['sessionId'] = session_id();

        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['login'], $_SESSION['role']);
        session_destroy();
    }

    public static function getByID(int $id): ?array
    {
        $db = self::db();
        return $db->getOne("SELECT id, login, email, role, avatar, created_at, updated_at FROM users WHERE id = :id LIMIT 1", [
            ':id' => $id
        ]);
    }
}
