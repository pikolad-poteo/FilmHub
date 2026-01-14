<?php
declare(strict_types=1);

function getAdminRoutes(): array
{
    return [
        ['GET',  '#^/$#',                    ['AdminController', 'startAdmin']],

        // Авторизация админки
        ['GET',  '#^/login$#',               ['AdminController', 'loginForm']],
        ['POST', '#^/login$#',               ['AdminController', 'loginSubmit']],
        ['GET',  '#^/logout$#',              ['AdminController', 'logout']],

        // Фильмы (CRUD)
        ['GET',  '#^/movies$#',              ['AdminController', 'movieList']],
        ['GET',  '#^/movie/add$#',           ['AdminController', 'movieAddForm']],
        ['POST', '#^/movie/add$#',           ['AdminController', 'movieAddSubmit']],
        ['GET',  '#^/movie/edit/(?P<id>\d+)$#', ['AdminController', 'movieEditForm']],
        ['POST', '#^/movie/edit/(?P<id>\d+)$#', ['AdminController', 'movieEditSubmit']],
        ['GET',  '#^/movie/delete/(?P<id>\d+)$#', ['AdminController', 'movieDeleteForm']],
        ['POST', '#^/movie/delete/(?P<id>\d+)$#', ['AdminController', 'movieDeleteSubmit']],

        // Просмотр оценок / избранного
        ['GET',  '#^/ratings$#',             ['AdminController', 'ratingsList']],
        ['GET',  '#^/favorites$#',           ['AdminController', 'favoritesList']],

        // 404
        ['GET',  '#^/.*$#',                  ['AdminController', 'error404']],
        ['POST', '#^/.*$#',                  ['AdminController', 'error404']],
    ];
}

/**
 * Админ-контроллер в этом же файле, чтобы не плодить новые папки.
 * Ты можешь позже вынести его в отдельный файл, но на этапе роутинга — ок.
 */
final class AdminController
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
            if (strtoupper($method) !== $httpMethod) continue;

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
        $methodName = $handler[1] ?? null;
        if (!$methodName || !method_exists($this, $methodName)) {
            $this->error404();
            return;
        }
        $this->{$methodName}($params);
    }

    private function render(string $templateFile, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $GLOBALS['__admin_view'] = $templateFile;
        require __DIR__ . '/../viewAdmin/templates/layout.php';
        unset($GLOBALS['__admin_view']);
    }

    private function redirect(string $to): void
    {
        header('Location: ' . $to);
        exit;
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user']['id']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->redirect('/admin/login');
        }
    }

    /* ===== actions ===== */

    public function startAdmin(): void
    {
        $this->requireAdmin();
        $this->render('startAdmin.php');
    }

    public function loginForm(): void
    {
        $this->render('formLogin.php');
    }

    public function loginSubmit(): void
    {
        // TODO: реальная проверка через UserModel (и role=admin)
        // После логина установить $_SESSION['user'] с role=admin
        $this->redirect('/admin/');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect('/admin/login');
    }

    public function movieList(): void
    {
        $this->requireAdmin();
        $this->render('movieList.php');
    }

    public function movieAddForm(): void
    {
        $this->requireAdmin();
        $this->render('movieAddForm.php');
    }

    public function movieAddSubmit(): void
    {
        $this->requireAdmin();
        // TODO: MovieModel::create($_POST)
        $this->redirect('/admin/movies');
    }

    public function movieEditForm(array $params): void
    {
        $this->requireAdmin();
        $this->render('movieEditForm.php', ['movieId' => (int)($params['id'] ?? 0)]);
    }

    public function movieEditSubmit(array $params): void
    {
        $this->requireAdmin();
        // TODO: MovieModel::update((int)$params['id'], $_POST)
        $this->redirect('/admin/movies');
    }

    public function movieDeleteForm(array $params): void
    {
        $this->requireAdmin();
        $this->render('movieDeleteForm.php', ['movieId' => (int)($params['id'] ?? 0)]);
    }

    public function movieDeleteSubmit(array $params): void
    {
        $this->requireAdmin();
        // TODO: MovieModel::delete((int)$params['id'])
        $this->redirect('/admin/movies');
    }

    public function ratingsList(): void
    {
        $this->requireAdmin();
        $this->render('ratingsList.php');
    }

    public function favoritesList(): void
    {
        $this->requireAdmin();
        $this->render('favoritesList.php');
    }

    public function error404(): void
    {
        http_response_code(404);
        $this->render('error404.php');
    }
}
