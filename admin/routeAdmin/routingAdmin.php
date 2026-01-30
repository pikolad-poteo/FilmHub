<?php
$host = explode('?', $_SERVER['REQUEST_URI'])[0];
$num  = substr_count($host,'/');
$path = explode('/', $host)[$num];

if ($path == '' || $path == 'index.php') {
    $response = controllerAdmin::formLoginSite();
}
elseif ($path == 'login') {
    $response = controllerAdmin::loginAction();
}
elseif ($path == 'logout') {
    $response = controllerAdmin::logoutAction();
}
elseif ($path == 'dashboard') {
    $response = controllerAdmin::dashboard();
}

/* ✅ NEW: delete admin avatar (file) */
elseif ($path == 'adminAvatarDelete') {
    $response = controllerAdminUsers::adminAvatarDelete();
}

/* ===== MOVIES ===== */
elseif ($path == 'moviesAdmin') {
    $response = controllerAdminMovies::movieList();
}
elseif ($path == 'movieAdd') {
    $response = controllerAdminMovies::movieAddForm();
}
elseif ($path == 'movieAddResult') {
    $response = controllerAdminMovies::movieAddResult();
}
elseif ($path == 'movieEdit' && isset($_GET['id'])) {
    $response = controllerAdminMovies::movieEditForm((int)$_GET['id']);
}
elseif ($path == 'movieEditResult' && isset($_GET['id'])) {
    $response = controllerAdminMovies::movieEditResult((int)$_GET['id']);
}
elseif ($path == 'movieDelete' && isset($_GET['id'])) {
    $response = controllerAdminMovies::movieDeleteForm((int)$_GET['id']);
}
elseif ($path == 'movieDeleteResult' && isset($_GET['id'])) {
    $response = controllerAdminMovies::movieDeleteResult((int)$_GET['id']);
}

/* ===== USERS ===== */
elseif ($path == 'usersAdmin') {
    $response = controllerAdminUsers::usersList();
}
elseif ($path == 'userAvatar' && isset($_GET['id'])) {
    $response = controllerAdminUsers::userAvatarForm((int)$_GET['id']);
}
elseif ($path == 'userAvatarUpdate' && isset($_GET['id'])) {
    $response = controllerAdminUsers::userAvatarUpdate((int)$_GET['id']);
}
elseif ($path == 'userAvatarDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::userAvatarDelete((int)$_GET['id']);
}


/* ===== MODERATION LISTS ===== */
elseif ($path == 'commentsAdmin') {
    $response = controllerAdminUsers::commentsList();
}
elseif ($path == 'commentToggle' && isset($_GET['id'])) {
    $response = controllerAdminUsers::commentToggle((int)$_GET['id']);
}
elseif ($path == 'commentDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::commentDelete((int)$_GET['id']);
}

elseif ($path == 'favoritesAdmin') {
    $response = controllerAdminUsers::favoritesList();
}
elseif ($path == 'ratingsAdmin') {
    $response = controllerAdminUsers::ratingsList();
}

elseif ($path == 'favoriteDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::favoriteDelete((int)$_GET['id']);
}
elseif ($path == 'ratingDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::ratingDelete((int)$_GET['id']);
}


/* ===== GENRES ===== */
elseif ($path == 'genresAdmin') {
    $response = controllerAdminUsers::genresList();
}
elseif ($path == 'genreAdd') {
    $response = controllerAdminUsers::genreAddForm();
}
elseif ($path == 'genreAddResult') {
    $response = controllerAdminUsers::genreAddResult();
}
elseif ($path == 'genreDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::genreDelete((int)$_GET['id']);
}
elseif ($path == 'userDelete' && isset($_GET['id'])) {
    $response = controllerAdminUsers::userDeleteForm((int)$_GET['id']);
}
elseif ($path == 'userDeleteResult' && isset($_GET['id'])) {
    $response = controllerAdminUsers::userDeleteResult((int)$_GET['id']);
}
else {
    $response = controllerAdmin::error404();
}
