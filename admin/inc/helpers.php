<?php
declare(strict_types=1);

if (!function_exists('h')) {
  function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

if (!function_exists('view_value')) {
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
  /**
   * Возвращает URL аватара ТОЛЬКО если он задан в БД.
   * Если avatar пустой/NULL — возвращает пустую строку.
   */
  function user_avatar_url(array $user, string $projectBase = ''): string {
    $projectBase = rtrim($projectBase, '/');
    $avatar = trim((string)($user['avatar'] ?? ''));

    // Нет аватара в БД -> ничего не подставляем
    if ($avatar === '') {
      return '';
    }

    // 1) прямой URL
    if (preg_match('~^https?://~i', $avatar)) {
      return $avatar;
    }

    // 2) абсолютный путь от корня сайта
    if ($avatar[0] === '/') {
      return $projectBase . $avatar;
    }

    // 3) относительный путь от корня проекта
    if (str_starts_with($avatar, 'img/')) {
      return $projectBase . '/' . $avatar;
    }

    // 4) имя файла (храним в БД только filename.ext)
    return $projectBase . '/img/users/' . rawurlencode($avatar);
  }
}
