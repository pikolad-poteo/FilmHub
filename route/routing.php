<?php
// route/routing.php

$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriPath = trim((string)$uriPath, '/');
$parts   = ($uriPath === '') ? [] : explode('/', $uriPath);

// Если проект лежит в подпапке /filmhub — убираем её из пути
if (isset($parts[0]) && $parts[0] === 'filmhub') {
    array_shift($parts);
}

$path = strtolower($parts[0] ?? ''); // ✅ нормализуем
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($path === '' || $path === 'index' || $path === 'index.php') {
    Controller::StartSite();
}
elseif ($path === 'all') {
    Controller::AllMovies();
}
elseif ($path === 'genres') {
    Controller::AllGenres();
}
elseif ($path === 'genre' && isset($_GET['id'])) {
    Controller::MoviesByGenreID((int)$_GET['id']);
}
elseif ($path === 'movie' && isset($_GET['id'])) {
    Controller::MovieByID((int)$_GET['id']);
}

/* ----------- Комментарии ----------- */
elseif ($path === 'insertcomment') {
    // ✅ поддержка POST (лучше), и старый GET (если используешь)
    if ($method === 'POST') {
        $text = (string)($_POST['comment'] ?? '');
        $id   = (int)($_POST['id'] ?? 0);
        Controller::InsertComment($text, $id);
    } elseif (isset($_GET['comment'], $_GET['id'])) {
        Controller::InsertComment((string)$_GET['comment'], (int)$_GET['id']);
    } else {
        Controller::error404();
    }
}

/* ----------- Избранное ----------- */
elseif ($path === 'favoritetoggle') {
    // ✅ это то, что нужно твоей кнопке <form action="favoriteToggle">
    if ($method === 'POST') {
        Controller::favoriteToggle();
    } else {
        // если вдруг кто-то открыл /favoriteToggle в браузере
        Controller::error404();
    }
}
elseif ($path === 'favorite' && isset($_GET['id'])) {
    // ✅ старый вариант (по ссылке): /favorite?id=12
    // ВНИМАНИЕ: метод в Controller называется favoriteToggle(), а не ToggleFavorite()
    // Поэтому вызываем toggle через POST — но если хочешь оставить GET:
    // можно сделать отдельный метод ToggleFavorite($id) в Controller.
    //
    // Быстрый способ: используем текущий toggle через модель:
    if (!empty($_SESSION['user_id'])) {
        FavoriteModel::toggle((int)$_SESSION['user_id'], (int)$_GET['id']);
        // вернем на страницу фильма
        Controller::MovieByID((int)$_GET['id']);
    } else {
        Controller::loginForm();
    }
}
elseif ($path === 'myfavorites') {
    Controller::MyFavorites();
}

/* ----------- Оценки ----------- */
elseif ($path === 'rate' && isset($_GET['id'], $_GET['rating'])) {
    Controller::RateMovie((int)$_GET['id'], (int)$_GET['rating']);
}

/* ----------- Авторизация ----------- */
elseif ($path === 'loginform') {
    Controller::loginForm();
}
elseif ($path === 'login') {
    Controller::loginUser();
}
elseif ($path === 'logout') {
    Controller::logoutUser();
}

/* ----------- Регистрация ----------- */
elseif ($path === 'registerform') {
    Controller::registerForm();
}
elseif ($path === 'registeranswer') {
    Controller::registerUser();
}

/* ----------- Profile ----------- */
elseif ($path === 'profile') {
    Controller::profile();
}
elseif ($path === 'profileupdate') {
    Controller::profileUpdate();
}
elseif ($path === 'profileavatar') {
    Controller::profileAvatarUpdate();
}
elseif ($path === 'profiledelete') {
    Controller::profileDelete();
}
elseif ($path === 'profileavatardelete') {
    Controller::profileAvatarDelete();
}
else {
    Controller::error404();
}
