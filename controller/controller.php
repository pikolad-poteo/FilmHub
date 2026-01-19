<?php
declare(strict_types=1);

class Controller
{
    /* ========= VIEW helper ========= */
    private static function view(string $file, array $vars = []): void
    {
        // пробрасываем переменные в scope view
        if (!empty($vars)) {
            extract($vars, EXTR_SKIP);
        }

        $path = __DIR__ . '/../view/' . $file;
        if (!is_file($path)) {
            // если view не найден — покажем 404 (и не уйдём в рекурсию)
            include __DIR__ . '/../view/templates/error404.php';
            return;
        }

        include $path;
    }

    /* ========= redirect helper ========= */
    private static function redirect(string $to): void
    {
        header("Location: {$to}");
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
        // если ты сделал обёртку view/error404.php — оставь так:
        if (is_file(__DIR__ . '/../view/error404.php')) {
            self::view('error404.php');
            return;
        }
        // иначе используем шаблон напрямую (по твоему дереву он точно есть)
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
            // без жёсткого /filmhub/, чтобы работало в любой папке
            self::redirect('index.php');
        }

        $_SESSION['errorString'] = 'Неправильный логин/email или пароль';
        self::view('formLogin.php');
    }

    public static function logoutUser(): void
    {
        UserModel::logout();
        self::redirect('index.php');
    }
}
