<?php
declare(strict_types=1);

class Controller
{
    /* ========= base helper ========= */
    private static function base(): string
    {
        // SCRIPT_NAME: /filmhub/index.php -> dirname => /filmhub
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $base = rtrim($base, '/');
        return $base === '' ? '' : $base;
    }

    /* ========= VIEW helper ========= */
    private static function view(string $file, array $vars = []): void
    {
        if (!empty($vars)) {
            extract($vars, EXTR_SKIP);
        }

        $path = __DIR__ . '/../view/' . $file;
        if (!is_file($path)) {
            include __DIR__ . '/../view/templates/error404.php';
            return;
        }

        include $path;
    }

    /* ========= redirect helper ========= */
    private static function redirect(string $to): void
    {
        // Поддержка:
        // - абсолютных URL (http/https)
        // - путей от корня (/profile)
        // - относительных (profile, movie?id=1)
        if (preg_match('~^https?://~i', $to)) {
            header("Location: {$to}");
            exit;
        }

        $base = self::base();

        if ($to === '' || $to === 'index.php' || $to === '/') {
            header("Location: " . ($base ?: '/') . "/");
            exit;
        }

        if ($to[0] === '/') {
            header("Location: {$to}");
            exit;
        }

        header("Location: " . ($base ? $base . '/' : '/') . ltrim($to, '/'));
        exit;
    }

    private static function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            self::redirect('loginForm');
        }
    }

    /* =======================
       PUBLIC PAGES
       ======================= */
    public static function StartSite(): void
    {
        $arr = MovieModel::getLastMovies(10);
        self::view('start.php', compact('arr'));
    }

    public static function AllMovies(): void
    {
        $arr = MovieModel::getAllMovies();
        self::view('allMovies.php', compact('arr'));
    }

    public static function AllGenres(): void
    {
        $arr = GenreModel::getAllGenres();
        self::view('genres.php', compact('arr'));
    }

    public static function MoviesByGenreID(int $id): void
    {
        $genre = GenreModel::getGenreByID($id);
        $arr   = MovieModel::getMoviesByGenreID($id);
        self::view('genres.php', compact('arr', 'genre'));
    }

    public static function MovieByID(int $id): void
    {
        $m = MovieModel::getMovieByID($id);
        if (!$m) {
            self::error404();
            return;
        }

        $comments = CommentModel::getVisibleCommentsByMovieID($id);

        $isFav = false;
        $myRating = null;
        if (!empty($_SESSION['user_id'])) {
            $uid = (int)$_SESSION['user_id'];
            $isFav    = FavoriteModel::isFavorite($uid, $id);
            $myRating = RatingModel::getUserRating($uid, $id);
        }

        $ratingStats = RatingModel::getMovieStats($id);

        self::view('movie.php', compact('m', 'comments', 'isFav', 'myRating', 'ratingStats'));
    }

    public static function error404(): void
    {
        if (is_file(__DIR__ . '/../view/error404.php')) {
            self::view('error404.php');
            return;
        }
        include __DIR__ . '/../view/templates/error404.php';
    }

    /* =======================
       COMMENTS
       ======================= */
    public static function InsertComment(string $text, int $movieId): void
    {
        self::requireAuth();

        $text = trim($text);
        if ($text !== '') {
            CommentModel::insertComment((int)$_SESSION['user_id'], $movieId, $text);
        }

        self::redirect("movie?id={$movieId}#ctable");
    }

    /* =======================
       FAVORITES
       ======================= */
    public static function ToggleFavorite(int $movieId): void
    {
        self::requireAuth();

        FavoriteModel::toggle((int)$_SESSION['user_id'], $movieId);

        self::redirect("movie?id={$movieId}");
    }

    public static function MyFavorites(): void
    {
        self::requireAuth();

        $arr = FavoriteModel::getUserFavorites((int)$_SESSION['user_id']);
        self::view('myFavorites.php', compact('arr'));
    }

    /* =======================
       RATINGS
       ======================= */
    public static function RateMovie(int $movieId, int $rating): void
    {
        self::requireAuth();

        $rating = max(1, min(10, $rating));
        RatingModel::setRating((int)$_SESSION['user_id'], $movieId, $rating);

        self::redirect("movie?id={$movieId}#rating");
    }

    /* =======================
       AUTH: REGISTER
       ======================= */
    public static function registerForm(): void
    {
        self::view('formRegister.php');
    }

    public static function registerUser(): void
    {
        $result = UserModel::registerFromPost();
        self::view('answerRegister.php', compact('result'));
    }

    /* =======================
       AUTH: LOGIN / LOGOUT
       ======================= */
    public static function loginForm(): void
    {
        self::view('formLogin.php');
    }

    public static function loginUser(): void
    {
        $ok = UserModel::loginFromPost();
        if ($ok) {
            self::redirect('/');
        }

        $_SESSION['errorString'] = 'Неправильный логин/email или пароль';
        self::view('formLogin.php');
    }

    public static function logoutUser(): void
    {
        UserModel::logout();
        self::redirect('/');
    }

    /* =======================
       PROFILE
       ======================= */
    public static function profile(): void
    {
        self::requireAuth();

        $uid = (int)$_SESSION['user_id'];
        $user = UserModel::getByID($uid);
        if (!$user) {
            // если пользователь исчез — выкидываем
            UserModel::logout();
            self::redirect('/');
        }

        $favorites = FavoriteModel::getUserFavorites($uid);

        $base = self::base();
        $avatarFile = !empty($user['avatar']) ? $user['avatar'] : 'default.png';
        $avatarUrl  = ($base ? $base . '/' : '/') . 'img/users/' . $avatarFile;


        self::view('profile.php', compact('user', 'favorites', 'avatarUrl'));
    }

    public static function profileUpdate(): void
    {
        self::requireAuth();

        $uid = (int)$_SESSION['user_id'];

        $login = trim((string)($_POST['login'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));

        $result = UserModel::updateProfile($uid, $login, $email);
        if (!$result[0]) {
            $_SESSION['errorString'] = $result[1] ?? 'Ошибка обновления профиля';
            self::redirect('profile');
        }

        // обновим сессию (логин показывается в меню)
        $_SESSION['login'] = $login;

        self::redirect('profile');
    }

    public static function profileAvatarUpdate(): void
    {
        self::requireAuth();

        $uid = (int)$_SESSION['user_id'];

        if (empty($_FILES['avatar']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            $_SESSION['errorString'] = 'Файл не выбран';
            self::redirect('profile');
        }

        $file = $_FILES['avatar'];

        // базовая защита
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $_SESSION['errorString'] = 'Ошибка загрузки файла';
            self::redirect('profile');
        }

        // MIME (надежнее через finfo)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']) ?: '';

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => ''
        };

        if ($ext === '') {
            $_SESSION['errorString'] = 'Разрешены только JPG/PNG/WebP';
            self::redirect('profile');
        }

        $dir = __DIR__ . '/../img/users';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (!is_dir($dir) || !is_writable($dir)) {
            $_SESSION['errorString'] = 'Папка img/users недоступна для записи';
            self::redirect('profile');
        }

        // старый файл — удалим после успешной записи нового
        $old = UserModel::getAvatarFilename($uid);

        $ts = time();
        $newName = "u{$uid}_{$ts}.{$ext}";
        $destAbs = $dir . '/' . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
            $_SESSION['errorString'] = 'Не удалось сохранить файл';
            self::redirect('profile');
        }

        // записали в БД
        $ok = UserModel::updateAvatar($uid, $newName);
        if (!$ok) {
            @unlink($destAbs);
            $_SESSION['errorString'] = 'Не удалось сохранить аватар в базе';
            self::redirect('profile');
        }

        // удаляем старый
        if ($old) {
            $oldAbs = $dir . '/' . $old;
            if (is_file($oldAbs)) {
                @unlink($oldAbs);
            }
        }

        self::redirect('profile');
    }

    public static function profileAvatarDelete(): void
    {
        self::requireAuth();

        $uid = (int)$_SESSION['user_id'];

        $old = UserModel::getAvatarFilename($uid);
        if (!$old) {
            // уже дефолт
            self::redirect('profile');
        }

        // чистим в БД
        $ok = UserModel::clearAvatar($uid);
        if (!$ok) {
            $_SESSION['errorString'] = 'Не удалось удалить аватар';
            self::redirect('profile');
        }

        // удаляем файл
        $dir = __DIR__ . '/../img/users';
        $oldAbs = $dir . '/' . $old;
        if (is_file($oldAbs)) {
            @unlink($oldAbs);
        }

        self::redirect('profile');
    }


    public static function profileDelete(): void
    {
        self::requireAuth();

        $uid = (int)$_SESSION['user_id'];

        // простая защита от случайного клика
        $confirm = (string)($_POST['confirm'] ?? '');
        if ($confirm !== 'DELETE') {
            $_SESSION['errorString'] = 'Для удаления аккаунта введи DELETE';
            self::redirect('profile');
        }

        // удалим аватар файл
        $old = UserModel::getAvatarFilename($uid);
        $dir = __DIR__ . '/../img/users';
        if ($old) {
            $oldAbs = $dir . '/' . $old;
            if (is_file($oldAbs)) {
                @unlink($oldAbs);
            }
        }

        $ok = UserModel::deleteUser($uid);
        UserModel::logout();

        self::redirect('/');
    }
}
