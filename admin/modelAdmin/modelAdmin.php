<?php
declare(strict_types=1);

class modelAdmin
{
    public static function userAuthentication(): bool
    {
        // если уже залогинен как админ
        if (!empty($_SESSION['sessionId']) && !empty($_SESSION['is_admin'])) {
            return true;
        }

        // поле name="email" оставляем как в старом проекте (можно вводить login или email)
        $loginOrEmail = trim((string)($_POST['email'] ?? ''));
        $pass         = (string)($_POST['password'] ?? '');

        if ($loginOrEmail === '' || $pass === '') return false;

        $db = new Database();

        $user = $db->getOne(
            "SELECT * FROM users WHERE email = :v OR login = :v LIMIT 1",
            [':v' => $loginOrEmail]
        );

        if (!$user) return false;

        // только admin
        if (($user['role'] ?? '') !== 'admin') return false;

        $hash = (string)($user['password'] ?? '');
        if ($hash === '') return false;

        if (!password_verify($pass, $hash)) return false;

        $_SESSION['sessionId'] = session_id();
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['login']     = (string)$user['login'];
        $_SESSION['role']      = (string)$user['role'];
        $_SESSION['is_admin']  = true;
        $_SESSION['avatar']    = (string)($user['avatar'] ?? '');


        return true;
    }

    public static function userLogout(): void
    {
        unset($_SESSION['sessionId'], $_SESSION['user_id'], $_SESSION['login'], $_SESSION['role'], $_SESSION['is_admin']);
        session_destroy();
    }
}
