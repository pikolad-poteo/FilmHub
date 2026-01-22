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
 */
$adminBase   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/'); // /filmhub/admin
$projectBase = preg_replace('~/admin$~', '', $adminBase); // /filmhub

function buildAvatarUrl(string $projectBase, ?string $avatar): string
{
  $p = trim((string)$avatar);
  if ($p === '') return '';

  if (preg_match('~^https?://~i', $p)) return $p;
  if ($p[0] === '/') return $projectBase . $p;
  if (str_starts_with($p, 'img/')) return $projectBase . '/' . $p;

  // по умолчанию считаем, что это имя файла в /img/users/
  return $projectBase . '/img/users/' . $p;
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

  <!-- Если у тебя есть форма добавления пользователя — поменяешь ссылку -->
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
          <th class="text-end" style="width:130px;">Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach ($users as $u): ?>
        <?php
          $id     = (int)($u['id'] ?? 0);
          $login  = (string)($u['login'] ?? '');
          $email  = (string)($u['email'] ?? '');
          $role   = (string)($u['role'] ?? 'user');
          $avatar = buildAvatarUrl($projectBase, $u['avatar'] ?? '');

          $created = (string)($u['created_at'] ?? '');
          $roleBadge = ($role === 'admin') ? 'text-bg-primary' : 'text-bg-secondary';
        ?>
        <tr>
          <td><?= $id ?></td>

          <td>
            <?php if ($avatar !== ''): ?>
              <img
                src="<?= h($avatar) ?>"
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
            <!-- Если у тебя есть userEdit — включишь кнопку -->
            <!-- <a href="userEdit?id=<?= $id ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a> -->

            <a
              href="userDelete?id=<?= $id ?>"
              class="btn btn-sm btn-outline-danger"
              title="Delete"
              data-confirm="delete"
              data-title="Удалить пользователя?"
              data-text="Пользователь будет удалён. Действие нельзя отменить."
              aria-label="Delete"
            >
              <i class="bi bi-trash3"></i>
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
