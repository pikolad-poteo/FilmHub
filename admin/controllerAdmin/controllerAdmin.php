<?php
class controllerAdmin
{
    private static function view(string $file, array $vars = []): void {
        if (!empty($vars)) extract($vars, EXTR_SKIP);
        include __DIR__ . '/../viewAdmin/' . $file;
    }

    private static function redirect(string $to): void {
        header("Location: {$to}");
        exit;
    }

    private static function requireAdmin(): void {
        if (empty($_SESSION['is_admin'])) {
            self::redirect('index.php');
        }
    }

    public static function formLoginSite() {
        if (!empty($_SESSION['is_admin'])) {
            header("Location: dashboard");
            exit;
        }
        include_once('viewAdmin/formLogin.php');
    }



    public static function loginAction(): void {
        $ok = modelAdmin::userAuthentication();
        if ($ok) {
            self::redirect('dashboard');
        }
        $_SESSION['errorString'] = 'Неправильный логин/email или пароль (или не admin)';
        self::view('formLogin.php');
    }

    public static function logoutAction(): void {
        modelAdmin::userLogout();
        self::redirect('index.php');
    }

    public static function dashboard(): void {
        self::requireAdmin();
        self::view('startAdmin.php');
    }

    public static function error404(): void {
        self::view('error404.php');
    }
}
