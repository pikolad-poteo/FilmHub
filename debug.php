<?php
require_once __DIR__ . '/inc/Database.php';
$db = new Database();

$info = $db->getOne("SELECT DATABASE() AS db");
$tables = $db->getAll("SHOW TABLES");

$countMovies = $db->getOne("SELECT COUNT(*) AS c FROM movies");

echo "<pre>";
echo "DB: " . ($info['db'] ?? 'NULL') . PHP_EOL;
echo "movies count: " . ($countMovies['c'] ?? 'NULL') . PHP_EOL;
echo "tables:" . PHP_EOL;
print_r($tables);
echo "</pre>";
