<?php
class controllerAdminUsers
{
    private static function view(string $file, array $vars = []): void
    {
        if (!empty($vars)) extract($vars, EXTR_SKIP);

        // основной путь
        $path = __DIR__ . '/../viewAdmin/' . $file;

        // fallback — если файл лежит в templates
        if (!is_file($path)) {
            $path = __DIR__ . '/../viewAdmin/templates/' . $file;
        }

        if (!is_file($path)) {
            die('Admin view not found: ' . htmlspecialchars($file));
        }

        include $path;
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

    /**
     * ✅ GENRES
     * Теперь movies_count приходит из modelAdminGenres::getAllGenres()
     */
    public static function genresList(): void {
        self::requireAdmin();
        $arr = modelAdminGenres::getAllGenres(); // <-- ВАЖНО: вместо getAllGenresWithMoviesCount()
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

    /**
     * USERS DELETE
     */
    public static function userDeleteForm(int $id): void
    {
        self::requireAdmin();

        $u = modelAdminUsers::getUserByID($id);
        if (!$u) {
            $_SESSION['errorString'] = 'Пользователь не найден';
            self::redirect('usersAdmin');
        }

        // Запрет: админ не может удалить сам себя (чтобы не потерять доступ)
        if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$u['id']) {
            $_SESSION['errorString'] = 'Нельзя удалить текущего администратора (самого себя).';
            self::redirect('usersAdmin');
        }

        self::view('userDeleteForm.php', compact('u'));
    }

    public static function userDeleteResult(int $id): void
    {
        self::requireAdmin();

        // ещё раз защитимся от удаления самого себя
        if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id) {
            $_SESSION['errorString'] = 'Нельзя удалить текущего администратора (самого себя).';
            self::redirect('usersAdmin');
        }

        $ok = modelAdminUsers::deleteUser($id);
        if (!$ok) {
            $_SESSION['errorString'] = 'Не удалось удалить пользователя.';
        }

        self::redirect('usersAdmin');
    }
}
