<?php
declare(strict_types=1);

session_start();

/*
 * Путь к корню проекта.
 * Если проект в подпапке (например /filmhub), то роутер сам это учтёт.
 */

require_once __DIR__ . '/inc/Database.php';

// Models
require_once __DIR__ . '/model/BaseModel.php';
require_once __DIR__ . '/model/GenreModel.php';
require_once __DIR__ . '/model/MovieModel.php';
require_once __DIR__ . '/model/CommentModel.php';
require_once __DIR__ . '/model/FavoriteModel.php';
require_once __DIR__ . '/model/RatingModel.php';
require_once __DIR__ . '/model/UserModel.php';

// Controller
require_once __DIR__ . '/controller/controller.php';

// Routing (последним)
require_once __DIR__ . '/route/routing.php';
