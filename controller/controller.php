<?php
declare(strict_types=1);

final class Controller
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            [$httpMethod, $pattern, $handler] = $route;

            if (strtoupper($method) !== $httpMethod) {
                continue;
            }

            if (preg_match($pattern, $path, $matches)) {
                $params = [];
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) $params[$k] = $v;
                }
                $this->callHandler($handler, $params);
                return;
            }
        }

        $this->error404();
    }

    private function callHandler(array $handler, array $params = []): void
    {
        // Формат handler: ['Controller', 'methodName']
        $methodName = $handler[1] ?? null;

        if (!$methodName || !method_exists($this, $methodName)) {
            $this->error404();
            return;
        }

        $this->{$methodName}($params);
    }

    /* =======================
       Helpers (render/redirect/auth)
       ======================= */

    private function render(string $viewFile, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $GLOBALS['__view'] = $viewFile;
        require __DIR__ . '/../view/layout.php';
        unset($GLOBALS['__view']);
    }

    private function redirect(string $to): void
    {
        header('Location: ' . $to);
        exit;
    }

    private function requireLogin(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $this->redirect('/login');
        }
    }

    /* =======================
       Actions (пока каркас, дальше заполним логикой)
       ======================= */

    public function start(): void
    {
        // TODO: подтянуть подборки/новинки
        $this->render('start.php');
    }

    public function allMovies(): void
    {
        // TODO: MovieModel::getAll()
        $this->render('allMovies.php');
    }

    public function readMovie(array $params): void
    {
        // TODO: MovieModel::getById((int)$params['id'])
        $this->render('readMovie.php', ['movieId' => (int)($params['id'] ?? 0)]);
    }

    public function genres(): void
    {
        // TODO: GenreModel::getAll()
        $this->render('genres.php');
    }

    public function moviesByGenre(array $params): void
    {
        // TODO: MovieModel::getByGenreSlug($params['slug'])
        $this->render('allMovies.php', ['genreSlug' => $params['slug'] ?? '']);
    }

    public function commentAdd(): void
    {
        $this->requireLogin();
        // TODO: CommentModel::add($_POST)
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    public function loginForm(): void
    {
        $this->render('formLogin.php');
    }

    public function loginSubmit(): void
    {
        // TODO: UserModel::login($_POST['login'], $_POST['password'])
        // Важно: после реализации логина установи:
        // $_SESSION['user'] = ['id'=>..., 'login'=>..., 'role'=>...];

        $this->redirect('/');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect('/');
    }

    public function registerForm(): void
    {
        $this->render('formRegister.php');
    }

    public function registerSubmit(): void
    {
        // TODO: UserModel::register(...)
        $this->render('answerRegister.php');
    }

    public function favoriteToggle(): void
    {
        $this->requireLogin();
        // TODO: FavoriteModel::toggle($_SESSION['user']['id'], (int)$_POST['movie_id'])
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true]);
    }

    public function rate(): void
    {
        $this->requireLogin();
        // TODO: RatingModel::upsert($_SESSION['user']['id'], (int)$_POST['movie_id'], (int)$_POST['rating'])
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true]);
    }

    public function myFavorites(): void
    {
        $this->requireLogin();
        // TODO: FavoriteModel::listByUser(...)
        $this->render('myFavorites.php');
    }

    public function profile(): void
    {
        $this->requireLogin();
        $this->render('profile.php');
    }

    public function error404(): void
    {
        http_response_code(404);
        $this->render('error404.php');
    }
}
