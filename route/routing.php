<?php
// route/routing.php

$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriPath = trim($uriPath, '/');
$parts   = ($uriPath === '') ? [] : explode('/', $uriPath);

// Если проект лежит в подпапке /filmhub — убираем её из пути
if (isset($parts[0]) && $parts[0] === 'filmhub') {
    array_shift($parts);
}

$path = $parts[0] ?? '';

if ($path === '' || $path === 'index' || $path === 'index.php') {
    $response = Controller::StartSite();
}
elseif ($path === 'all') {
    $response = Controller::AllMovies();
}
elseif ($path === 'genres') {
    $response = Controller::AllGenres();
}
elseif ($path === 'genre' && isset($_GET['id'])) {
    $response = Controller::MoviesByGenreID((int)$_GET['id']);
}
elseif ($path === 'movie' && isset($_GET['id'])) {
    $response = Controller::MovieByID((int)$_GET['id']);
}

/* ----------- Комментарии ----------- */
elseif ($path === 'insertcomment' && isset($_GET['comment'], $_GET['id'])) {
    $response = Controller::InsertComment($_GET['comment'], (int)$_GET['id']);
}

/* ----------- Избранное ----------- */
elseif ($path === 'favorite' && isset($_GET['id'])) {
    $response = Controller::ToggleFavorite((int)$_GET['id']); // movie_id
}
elseif ($path === 'myfavorites') {
    $response = Controller::MyFavorites();
}

/* ----------- Оценки ----------- */
elseif ($path === 'rate' && isset($_GET['id'], $_GET['rating'])) {
    $response = Controller::RateMovie((int)$_GET['id'], (int)$_GET['rating']);
}

/* ----------- Авторизация (пока примитивно) ----------- */
elseif ($path === 'loginForm') {
    $response = Controller::loginForm();
}
elseif ($path === 'login') {
    $response = Controller::loginUser();
}
elseif ($path === 'logout') {
    $response = Controller::logoutUser();
}

/* ----------- Регистрация ----------- */
elseif ($path === 'registerForm') {
    $response = Controller::registerForm();
}
elseif ($path === 'registerAnswer') {
    $response = Controller::registerUser();
}

/* ----------- Profile ----------- */
elseif ($path === 'profile') {
    $response = Controller::profile();
}
elseif ($path === 'profileUpdate') {
    $response = Controller::profileUpdate();
}
elseif ($path === 'profileAvatar') {
    $response = Controller::profileAvatarUpdate();
}
elseif ($path === 'profileDelete') {
    $response = Controller::profileDelete();
}
elseif ($path === 'profileAvatarDelete') {
    $response = Controller::profileAvatarDelete();
}

else {
    $response = Controller::error404();
}
