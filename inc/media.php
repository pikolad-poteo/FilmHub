<?php
declare(strict_types=1);

/**
 * Унифицированный base проекта: /filmhub или ''.
 */
function fh_base(): string
{
    $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $base = rtrim($base, '/');
    return $base === '' ? '' : $base;
}

/**
 * Унифицированный URL ассета (css/js/img), учитывает base.
 */
function fh_abs_asset(string $path, ?string $base = null): string
{
    $path = trim((string)$path);
    if ($path === '') return '';

    if (preg_match('~^https?://~i', $path)) return $path;

    $base = $base ?? fh_base();
    $base = rtrim($base, '/');

    if ($path[0] === '/') return ($base ?: '') . $path;

    return ($base ? $base . '/' : '/') . $path;
}

/**
 * ЕДИНАЯ система постеров:
 * - в БД хранится ТОЛЬКО имя файла (например: avatar_2009.jpg)
 * - или внешняя ссылка https://...
 * - URL всегда: {base}/img/movies/{filename}
 */
function movie_poster_url(?string $poster, ?string $base = null): string
{
    $base = $base ?? fh_base();
    $base = rtrim($base, '/');

    $poster = trim((string)$poster);
    if ($poster === '') {
        // если у тебя нет default.png — можешь вернуть '' вместо дефолта
        return ($base ? $base . '/' : '/') . 'img/movies/default.png';
    }

    if (preg_match('~^https?://~i', $poster)) {
        return $poster;
    }

    // если вдруг в БД сохранили путь — берём только имя
    $file = basename($poster);

    return ($base ? $base . '/' : '/') . 'img/movies/' . $file;
}
function fh_slug(string $s): string
{
    $s = trim($s);
    $s = mb_strtolower($s, 'UTF-8');

    // попытка транслитерации (если доступна iconv)
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($t !== false && $t !== '') $s = $t;
    }

    $s = preg_replace('~[^a-z0-9]+~i', '_', $s);
    $s = trim($s, '_');

    return $s !== '' ? $s : 'movie';
}

function fh_movie_poster_filename(string $title, ?int $year, string $ext): string
{
    $base = fh_slug($title);
    if ($year) $base .= '_' . $year;

    $ext = strtolower($ext);
    if ($ext === 'jpeg') $ext = 'jpg';

    return $base . '.' . $ext;
}

