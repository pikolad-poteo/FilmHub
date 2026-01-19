<?php
class controllerAdminUsers
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

    public static function usersList(): void {
        self::requireAdmin();
        $arr = modelAdminUsers::getAllUsers();
        self::view('usersList.php', compact('arr'));
    }

    public static function commentsList(): void {
        self::requireAdmin();
        $arr = modelAdminComments::getAllComments();
        self::view('commentsList.php', compact('arr'));
    }

    public static function commentToggle(int $id): void {
        self::requireAdmin();
        modelAdminComments::toggleStatus($id);
        self::redirect('commentsAdmin');
    }

    public static function favoritesList(): void {
        self::requireAdmin();
        $arr = modelAdminFavorites::getAllFavorites();
        self::view('favoritesList.php', compact('arr'));
    }

    public static function ratingsList(): void {
        self::requireAdmin();
        $arr = modelAdminRatings::getAllRatings();
        self::view('ratingsList.php', compact('arr'));
    }

    public static function genresList(): void {
        self::requireAdmin();
        $arr = modelAdminGenres::getAllGenres();
        self::view('genresList.php', compact('arr'));
    }

    public static function genreAddForm(): void {
        self::requireAdmin();
        self::view('genreAddForm.php');
    }

    public static function genreAddResult(): void {
        self::requireAdmin();
        $ok = modelAdminGenres::createFromPost();
        if (!$ok) $_SESSION['errorString'] = 'Не удалось добавить жанр (возможно дубликат name/slug).';
        self::redirect('genresAdmin');
    }

    public static function genreDelete(int $id): void {
        self::requireAdmin();
        $ok = modelAdminGenres::deleteGenre($id);
        if (!$ok) $_SESSION['errorString'] = 'Не удалось удалить жанр.';
        self::redirect('genresAdmin');
    }
}
