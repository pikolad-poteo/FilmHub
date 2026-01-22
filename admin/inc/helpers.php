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
