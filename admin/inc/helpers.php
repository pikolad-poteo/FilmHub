<?php
declare(strict_types=1);

if (!function_exists('h')) {
  function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

if (!function_exists('view_value')) {
  /**
   * Берёт значение из массива по списку ключей, если есть
   */
  function view_value(array $arr, array $keys, $default = '—') {
    foreach ($keys as $k) {
      if (array_key_exists($k, $arr) && $arr[$k] !== null && $arr[$k] !== '') {
        return $arr[$k];
      }
    }
    return $default;
  }
}
if (!function_exists('user_avatar_url')) {
  function user_avatar_url(array $user, string $projectBase = ''): string {
    $projectBase = rtrim($projectBase, '/');

    $id     = (int)($user['id'] ?? 0);
    $avatar = trim((string)($user['avatar'] ?? ''));

    // 1) если в БД есть прямой url
    if ($avatar !== '' && preg_match('~^https?://~i', $avatar)) {
      return $avatar;
    }

    // 2) если в БД лежит относительный путь
    if ($avatar !== '') {
      if ($avatar[0] === '/') return $projectBase . $avatar;
      if (str_starts_with($avatar, 'img/')) return $projectBase . '/' . $avatar;

      // 3) если в БД лежит только имя файла
      return $projectBase . '/img/users/' . $avatar;
    }

    // 4) fallback: ищем реальный файл по id.ext (ПРОВЕРЯЕМ что он существует)
    if ($id > 0) {
      $projectRoot = realpath(__DIR__ . '/../../');          // admin/inc -> корень проекта
      $imgDir      = $projectRoot ? ($projectRoot . '/img/users') : null;

      if ($imgDir && is_dir($imgDir)) {
        foreach (['jpg','jpeg','png','webp'] as $ext) {
          $abs = $imgDir . '/' . $id . '.' . $ext;
          if (is_file($abs)) {
            return $projectBase . '/img/users/' . $id . '.' . $ext;
          }
        }
      }
    }

    // 5) дефолт
    return $projectBase . '/img/users/default.png';
  }
}

