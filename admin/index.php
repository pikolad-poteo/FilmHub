<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../inc/Database.php';
require_once __DIR__ . '/../controller/controller.php'; // можно переиспользовать
require_once __DIR__ . '/routeAdmin/routingAdmin.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/admin';
$path = parse_url($uri, PHP_URL_PATH) ?: '/admin';

/**
 * Срезаем basePath /admin
 */
$basePath = '/admin';
if (str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}
if ($path === '') $path = '/';

$routes = getAdminRoutes();
$controller = new AdminController($routes);
$controller->dispatch($method, $path);
