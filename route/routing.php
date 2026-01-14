<?php
declare(strict_types=1);

/**
 * Роутер: метод + regex + handler
 * handler: ['Controller', 'methodName'] — вызываем через Controller->callHandler()
 */
function getPublicRoutes(): array
{
    return [
        // Главная
        ['GET',  '#^/$#',                         ['Controller', 'start']],

        // Фильмы
        ['GET',  '#^/movies$#',                   ['Controller', 'allMovies']],
        ['GET',  '#^/movie/(?P<id>\d+)$#',        ['Controller', 'readMovie']],

        // Жанры
        ['GET',  '#^/genres$#',                   ['Controller', 'genres']],
        // Например: /genre/sci-fi
        ['GET',  '#^/genre/(?P<slug>[a-z0-9\-]+)$#', ['Controller', 'moviesByGenre']],

        // Комментарии (пример: POST добавление)
        ['POST', '#^/comment/add$#',              ['Controller', 'commentAdd']],

        // Авторизация
        ['GET',  '#^/login$#',                    ['Controller', 'loginForm']],
        ['POST', '#^/login$#',                    ['Controller', 'loginSubmit']],
        ['GET',  '#^/logout$#',                   ['Controller', 'logout']],

        // Регистрация
        ['GET',  '#^/register$#',                 ['Controller', 'registerForm']],
        ['POST', '#^/register$#',                 ['Controller', 'registerSubmit']],

        // Избранное / Оценки
        ['POST', '#^/favorite/toggle$#',          ['Controller', 'favoriteToggle']],
        ['POST', '#^/rate$#',                     ['Controller', 'rate']],
        ['GET',  '#^/my-favorites$#',             ['Controller', 'myFavorites']],

        // Профиль (опционально)
        ['GET',  '#^/profile$#',                  ['Controller', 'profile']],

        // 404 — последним
        ['GET',  '#^/.*$#',                       ['Controller', 'error404']],
        ['POST', '#^/.*$#',                       ['Controller', 'error404']],
    ];
}
