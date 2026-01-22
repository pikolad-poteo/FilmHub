<?php
// admin/viewAdmin/usersList.php
require_once __DIR__ . '/../inc/helpers.php';

$pageTitle = 'Users';
ob_start();

$users = $arr ?? [];
if ($users instanceof Traversable) {
  $tmp = [];
  foreach ($users as $x) $tmp[] = $x;
  $users = $tmp;
}

/**
 * base пути (без хардкода /filmhub)
 * Пример:
 *   SCRIPT_NAME = /filmhub/admin/index.php
 *   adminBase   = /filmhub/admin
 *   projectBase = /filmhub
 */
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
$projectBase = preg_replace('~/admin$~', '', $adminBase);
$projectBase = rtrim($projectBase, '/'); // может быть '' если сайт в корне домена

/**
 * Абсолютный путь к корню проекта и к img/users
 * usersList.php лежит в admin/viewAdmin/
 * значит корень проекта = ../../
 */
$projectRoot = realpath(__DIR__ . '/../../');
$usersImgDir = $projectRoot ? realpath($projectRoot . '/img/users') : false;

/**
 * Находит существующий файл аватара по id (id.jpg / id.png / id.webp ...)
 * Возвращает URL или пустую строку.
 */
function findAvatarById(string $projectBase, $usersImgDir, int $userId): string
{
  if (!$usersImgDir || $userId <= 0) return '';

  foreach (['jpg','jpeg','png','webp'] as $ext) {
    $file = $userId . '.' . $ext;
    $abs  = $usersImgDir . DIRECTORY_SEPARATOR . $file;
    if (is_file($abs)) {
      return ($projectBase !== '' ? $projectBase : '') . '/img/users/' . $file;
    }
  }
  return '';
}

/**
 * Строит URL аватарки:
 * - если в БД лежит https://...
 * - если /img/... (абсолютный от корня сайта)
 * - если img/... (относительный)
 * - если просто имя файла (в /img/users/)
 * - если avatar пустой -> ищем по id (реальный ext)
 */
function buildAvatarUrl(string $projectBase, ?string $avatar, ?int $userId, $usersImgDir): string
{
  $projectBase = rtrim($projectBase, '/');
  $p = trim((string)$avatar);

  // 1) если в БД задано
  if ($p !== '') {
    if (preg_match('~^https?://~i', $p)) return $p;

    if ($p[0] === '/') {
      // путь от корня сайта: /img/users/...
      return ($projectBase !== '' ? $projectBase : '') . $p;
    }

    if (str_starts_with($p, 'img/')) {
      return ($projectBase !== '' ? $projectBase : '') . '/' . $p;
    }

    // просто имя файла
    return ($projectBase !== '' ? $projectBase : '') . '/img/users/' . $p;
  }

  // 2) fallback по id -> ищем реально существующий файл
  if ($userId) {
    $byId = findAvatarById($projectBase, $usersImgDir, (int)$userId);
    if ($byId !== '') return $byId;
  }

  // 3) если есть default.png — вернём его, иначе пусто (будет иконка)
  if ($usersImgDir && is_file($usersImgDir . DIRECTORY_SEPARATOR . 'default.png')) {
    return ($projectBase !== '' ? $projectBase : '') . '/img/users/default.png';
  }

  return '';
}

/**
 * СОРТИРОВКА
 * ?sort=id|login|email|role&dir=asc|desc
 */
$sort = $_GET['sort'] ?? 'id';
$dir  = strtolower($_GET['dir'] ?? 'asc');
$dir  = in_array($dir, ['asc','desc'], true) ? $dir : 'asc';

$allowedSort = ['id','login','email','role'];
if (!in_array($sort, $allowedSort, true)) $sort = 'id';

if (!empty($users)) {
  usort($users, function($a, $b) use ($sort, $dir) {
    $av = $a[$sort] ?? '';
    $bv = $b[$sort] ?? '';

    if ($sort === 'id') $cmp = (int)$av <=> (int)$bv;
    else $cmp = mb_strtolower((string)$av) <=> mb_strtolower((string)$bv);

    return $dir === 'desc' ? -$cmp : $cmp;
  });
}

function buildSortLink(string $sort, string $dir): string {
  $qs = $_GET;
  $qs['sort'] = $sort;
  $qs['dir']  = $dir;
  return '?' . http_build_query($qs);
}
function nextDir(string $currentSort, string $currentDir, string $clickedSort): string {
  if ($currentSort !== $clickedSort) return 'asc';
  return $currentDir === 'asc' ? 'desc' : 'asc';
}
$iconDir = function(string $currentSort, string $currentDir, string $col): string {
  if ($currentSort !== $col) return '';
  return $currentDir === 'asc' ? ' <i class="bi bi-arrow-up"></i>' : ' <i class="bi bi-arrow-down"></i>';
};
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-0">Users</h1>
    <div class="text-muted small">
      Sort:
      <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">ID<?= $iconDir($sort,$dir,'id') ?></a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('login', nextDir($sort,$dir,'login'))) ?>">Login<?= $iconDir($sort,$dir,'login') ?></a> ·
      <a class="text-decoration-none" href="<?= h(buildSortLink('role', nextDir($sort,$dir,'role'))) ?>">Role<?= $iconDir($sort,$dir,'role') ?></a>
    </div>
  </div>

  <a href="userAdd" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Add user
  </a>
</div>

<?php if (empty($users)): ?>
  <div class="alert alert-info">Users not found</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table fh-table table-hover align-middle">
      <thead>
        <tr>
          <th style="width:70px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('id', nextDir($sort,$dir,'id'))) ?>">
              ID<?= $iconDir($sort,$dir,'id') ?>
            </a>
          </th>
          <th style="width:70px;">Avatar</th>
          <th>
            <a class="text-decoration-none" href="<?= h(buildSortLink('login', nextDir($sort,$dir,'login'))) ?>">
              Login<?= $iconDir($sort,$dir,'login') ?>
            </a>
          </th>
          <th>
            <a class="text-decoration-none" href="<?= h(buildSortLink('email', nextDir($sort,$dir,'email'))) ?>">
              Email<?= $iconDir($sort,$dir,'email') ?>
            </a>
          </th>
          <th style="width:120px;">
            <a class="text-decoration-none" href="<?= h(buildSortLink('role', nextDir($sort,$dir,'role'))) ?>">
              Role<?= $iconDir($sort,$dir,'role') ?>
            </a>
          </th>
          <th style="width:170px;">Created</th>
          <th class="text-end" style="width: 140px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($users as $u): ?>
        <?php
          $id     = (int)($u['id'] ?? 0);
          $login  = (string)($u['login'] ?? '');
          $email  = (string)($u['email'] ?? '');
          $role   = (string)($u['role'] ?? 'user');

          // ВАЖНО: передаём $id и $usersImgDir
          $avatarUrl = buildAvatarUrl($projectBase, $u['avatar'] ?? null, $id, $usersImgDir);

          $created = (string)($u['created_at'] ?? '');
          $roleBadge = ($role === 'admin') ? 'text-bg-primary' : 'text-bg-secondary';
        ?>
        <tr>
          <td><?= $id ?></td>

          <td>
            <?php if ($avatarUrl !== ''): ?>
              <img
                src="<?= h($avatarUrl) ?>"
                alt="<?= h($login) ?>"
                style="width:40px;height:40px;border-radius:14px;object-fit:cover;border:1px solid rgba(15,23,42,.10);background:#f1f5f9"
                loading="lazy"
              >
            <?php else: ?>
              <div
                style="width:40px;height:40px;border-radius:14px;display:grid;place-items:center;border:1px solid rgba(15,23,42,.10);background:#f1f5f9;color:rgba(15,23,42,.35)"
                title="No avatar"
              >
                <i class="bi bi-person"></i>
              </div>
            <?php endif; ?>
          </td>

          <td><strong><?= h($login) ?></strong></td>
          <td><?= h($email) ?></td>

          <td>
            <span class="badge <?= h($roleBadge) ?>">
              <?= h($role) ?>
            </span>
          </td>

          <td class="text-muted small"><?= h($created) ?></td>

          <td class="text-end">
            <a
              href="userAvatar?id=<?= $id ?>"
              class="btn btn-sm btn-outline-primary"
              title="Edit avatar"
              aria-label="Edit avatar"
            >
              <i class="bi bi-image"></i>
            </a>

            <a
              href="userAvatarDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-purple"
              title="Delete avatar"
              data-confirm="delete"
              data-title="Удалить аватар?"
              data-text="Аватар пользователя будет удалён (файл)."
              aria-label="Delete avatar"
            >
              <i class="bi bi-trash3"></i>
            </a>

            <a
              href="userDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete user"
              data-confirm="delete"
              data-title="Удалить пользователя?"
              data-text="Пользователь будет удалён. Действие нельзя отменить."
              aria-label="Delete user"
            >
              <i class="bi bi-person-x"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
