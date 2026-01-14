<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/inc/Database.php';
require_once __DIR__ . '/controller/controller.php';
require_once __DIR__ . '/route/routing.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/**
 * PATH_INFO иногда пустой, поэтому берём REQUEST_URI и вырезаем query string.
 */
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

/**
 * Если проект лежит в подпапке (например /filmhub/), base path нужно срезать.
 * Автоматически пытаемся определить по SCRIPT_NAME.
 */
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}
if ($path === '') $path = '/';

$routes = getPublicRoutes();
$controller = new Controller($routes);

$controller->dispatch($method, $path);
