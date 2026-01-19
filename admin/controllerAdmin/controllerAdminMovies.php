<?php
class controllerAdminMovies
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
        if (empty($_SESSION['is_admin'])) self::redirect('index.php');
    }

    public static function movieList(): void {
        self::requireAdmin();
        $arr = modelAdminMovies::getAllMovies();
        self::view('moviesList.php', compact('arr'));
    }

    public static function movieAddForm(): void {
        self::requireAdmin();
        $genres = modelAdminGenres::getAllGenres();
        self::view('movieAddForm.php', compact('genres'));
    }

    public static function movieAddResult(): void {
        self::requireAdmin();

        $ok = modelAdminMovies::createFromPost();
        if (!$ok) $_SESSION['errorString'] = 'Не удалось добавить фильм (проверь поля/уникальность title+year).';

        self::redirect('moviesAdmin');
    }

    public static function movieEditForm(int $id): void {
        self::requireAdmin();
        $m = modelAdminMovies::getMovieByID($id);
        if (!$m) {
            $_SESSION['errorString'] = 'Фильм не найден';
            self::redirect('moviesAdmin');
        }
        $genres = modelAdminGenres::getAllGenres();
        self::view('movieEditForm.php', compact('m','genres'));
    }

    public static function movieEditResult(int $id): void {
        self::requireAdmin();
        $ok = modelAdminMovies::updateFromPost($id);
        if (!$ok) $_SESSION['errorString'] = 'Не удалось обновить фильм.';
        self::redirect('moviesAdmin');
    }

    public static function movieDeleteForm(int $id): void {
        self::requireAdmin();
        $m = modelAdminMovies::getMovieByID($id);
        if (!$m) {
            $_SESSION['errorString'] = 'Фильм не найден';
            self::redirect('moviesAdmin');
        }
        self::view('movieDeleteForm.php', compact('m'));
    }

    public static function movieDeleteResult(int $id): void {
        self::requireAdmin();
        $ok = modelAdminMovies::deleteMovie($id);
        if (!$ok) $_SESSION['errorString'] = 'Не удалось удалить фильм.';
        self::redirect('moviesAdmin');
    }
}
