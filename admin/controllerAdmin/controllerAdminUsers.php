<?php
class controllerAdminUsers
{
    private static function view(string $file, array $vars = []): void
    {
        if (!empty($vars)) extract($vars, EXTR_SKIP);

        $path = __DIR__ . '/../viewAdmin/' . $file;
        if (!is_file($path)) $path = __DIR__ . '/../viewAdmin/templates/' . $file;

        if (!is_file($path)) {
            die('Admin view not found: ' . htmlspecialchars($file));
        }

        include $path;
    }

    private static function redirect(string $to): void
    {
        header("Location: {$to}");
        exit;
    }

    private static function requireAdmin(): void
    {
        if (empty($_SESSION['is_admin'])) self::redirect('index.php');
    }

    private static function getProjectRoot(): ?string
    {
        $root = realpath(__DIR__ . '/../../');
        return $root ?: null;
    }

    private static function getUsersImgDir(): ?string
    {
        $root = self::getProjectRoot();
        if (!$root) return null;

        $dir = $root . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'users';
        if (!is_dir($dir)) return null;

        return $dir;
    }

    private static function colExists(Database $db, string $table, string $col): bool
    {
        $row = $db->getOne(
            "SELECT 1 AS ok
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :t
               AND COLUMN_NAME = :c
             LIMIT 1",
            [':t' => $table, ':c' => $col]
        );
        return (bool)$row;
    }

    private static function deleteAvatarFiles(string $usersDir, int $userId): bool
    {
        $candidates = [
            $usersDir . DIRECTORY_SEPARATOR . $userId . '.jpg',
            $usersDir . DIRECTORY_SEPARATOR . $userId . '.jpeg',
            $usersDir . DIRECTORY_SEPARATOR . $userId . '.png',
            $usersDir . DIRECTORY_SEPARATOR . $userId . '.webp',
        ];

        $deleted = false;
        foreach ($candidates as $f) {
            if (is_file($f)) {
                @unlink($f);
                $deleted = true;
            }
        }
        return $deleted;
    }

    private static function deleteAvatarVariants(string $usersDir, int $userId): bool
    {
        $deleted = false;
        $prefix = 'u' . $userId . '_';

        foreach (['jpg','jpeg','png','webp'] as $ext) {
            foreach (glob($usersDir . DIRECTORY_SEPARATOR . $prefix . '*.' . $ext) ?: [] as $path) {
                if (is_file($path)) {
                    @unlink($path);
                    $deleted = true;
                }
            }
        }
        return $deleted;
    }

    private static function getProjectBase(): string
    {
        $adminBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
        return preg_replace('~/admin$~', '', $adminBase) ?: '';
    }

    /* ===== USERS LIST ===== */
    public static function usersList(): void
    {
        self::requireAdmin();
        $arr = modelAdminUsers::getAllUsers();
        self::view('usersList.php', compact('arr'));
    }

    /* ===== COMMENTS LIST ===== */
    public static function commentsList(): void
    {
        self::requireAdmin();
        $arr = modelAdminComments::getAllComments();
        self::view('commentsList.php', compact('arr'));
    }

    public static function commentToggle(int $id): void
    {
        self::requireAdmin();
        modelAdminComments::toggleStatus((int)$id);

        $qs = $_GET ?? [];
        unset($qs['id']);
        $back = 'commentsAdmin' . (!empty($qs) ? ('?' . http_build_query($qs)) : '');
        self::redirect($back);
    }

    public static function commentDelete(int $id): void
    {
        self::requireAdmin();

        $id = (int)$id;
        if ($id > 0) modelAdminComments::deleteById($id);

        $qs = $_GET ?? [];
        unset($qs['id']);
        $back = 'commentsAdmin' . (!empty($qs) ? ('?' . http_build_query($qs)) : '');
        self::redirect($back);
    }

    /* ===== AVATAR: FORM ===== */
    public static function userAvatarForm(int $id): void
    {
        self::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) self::redirect('usersAdmin');

        $u = modelAdminUsers::getUserByID($id);
        if (!$u) {
            $_SESSION['errorString'] = 'Пользователь не найден';
            self::redirect('usersAdmin');
        }

        $projectBase = self::getProjectBase();

        $avatarUrl = '';
        $avatar = trim((string)($u['avatar'] ?? ''));
        if ($avatar !== '') {
            if (preg_match('~^https?://~i', $avatar)) $avatarUrl = $avatar;
            elseif ($avatar[0] === '/') $avatarUrl = $projectBase . $avatar;
            elseif (str_starts_with($avatar, 'img/')) $avatarUrl = $projectBase . '/' . $avatar;
            else $avatarUrl = $projectBase . '/img/users/' . $avatar;
        } else {
            // fallback: id.ext + u{id}_*.ext
            $usersDir = self::getUsersImgDir();
            if ($usersDir) {
                foreach (['jpg','jpeg','png','webp'] as $ext) {
                    $fn = $id . '.' . $ext;
                    if (is_file($usersDir . DIRECTORY_SEPARATOR . $fn)) {
                        $avatarUrl = $projectBase . '/img/users/' . $fn;
                        break;
                    }
                }
                if ($avatarUrl === '') {
                    $prefix = 'u' . $id . '_';
                    foreach (['jpg','jpeg','png','webp'] as $ext) {
                        $hits = glob($usersDir . DIRECTORY_SEPARATOR . $prefix . '*.' . $ext) ?: [];
                        if (!empty($hits)) {
                            // берём самый свежий
                            usort($hits, fn($a,$b) => filemtime($b) <=> filemtime($a));
                            $avatarUrl = $projectBase . '/img/users/' . basename($hits[0]);
                            break;
                        }
                    }
                }
            }
        }

        self::view('userAvatarForm.php', compact('u', 'avatarUrl'));
    }

    /* ===== AVATAR: UPDATE ===== */
    public static function userAvatarUpdate(int $id): void
    {
        self::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) self::redirect('usersAdmin');

        $u = modelAdminUsers::getUserByID($id);
        if (!$u) {
            $_SESSION['errorString'] = 'Пользователь не найден';
            self::redirect('usersAdmin');
        }

        if (empty($_FILES['avatar']) || !is_array($_FILES['avatar']) || empty($_FILES['avatar']['tmp_name'])) {
            $_SESSION['errorString'] = 'Файл не выбран.';
            self::redirect('userAvatar?id=' . $id);
        }

        $f = $_FILES['avatar'];

        if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $_SESSION['errorString'] = 'Ошибка загрузки файла.';
            self::redirect('userAvatar?id=' . $id);
        }

        $max = 2 * 1024 * 1024;
        if (!empty($f['size']) && (int)$f['size'] > $max) {
            $_SESSION['errorString'] = 'Файл слишком большой (макс 2MB).';
            self::redirect('userAvatar?id=' . $id);
        }

        $usersDir = self::getUsersImgDir();
        if (!$usersDir) {
            $_SESSION['errorString'] = 'Папка img/users не найдена.';
            self::redirect('userAvatar?id=' . $id);
        }
        if (!is_writable($usersDir)) {
            $_SESSION['errorString'] = 'Папка img/users недоступна для записи.';
            self::redirect('userAvatar?id=' . $id);
        }

        $tmp = (string)$f['tmp_name'];

        $fi   = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fi->file($tmp) ?: '';

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            $_SESSION['errorString'] = 'Разрешены только JPG/PNG/WebP.';
            self::redirect('userAvatar?id=' . $id);
        }

        if (!@getimagesize($tmp)) {
            $_SESSION['errorString'] = 'Файл не является изображением.';
            self::redirect('userAvatar?id=' . $id);
        }

        $ext = $allowed[$mime];

        // чистим старые файлы
        self::deleteAvatarFiles($usersDir, $id);
        self::deleteAvatarVariants($usersDir, $id);

        // сохраняем новый
        $filename = 'u' . $id . '_' . time() . '.' . $ext;
        $dest     = $usersDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmp, $dest)) {
            $_SESSION['errorString'] = 'Не удалось сохранить файл.';
            self::redirect('userAvatar?id=' . $id);
        }

        // ✅ ОБЯЗАТЕЛЬНО обновляем БД через executeRun()
        try {
            $db = new Database();
            if (self::colExists($db, 'users', 'avatar')) {
                $db->executeRun(
                    "UPDATE users SET avatar = :a WHERE id = :id",
                    [':a' => $filename, ':id' => $id]
                );
            }
        } catch (Throwable $e) {
            $_SESSION['errorString'] = 'Файл сохранён, но не удалось обновить БД.';
        }

        // ✅ если админ обновил свой аватар — обновим сессию правильно
        if ((int)($_SESSION['user_id'] ?? 0) === $id) {
            $_SESSION['avatar'] = $filename;
            if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                $_SESSION['user']['avatar'] = $filename;
            }
        }

        self::redirect('userAvatar?id=' . $id);
    }

    /* ===== AVATAR: DELETE ===== */
    public static function userAvatarDelete(int $id): void
    {
        self::requireAdmin();

        $id = (int)$id;
        if ($id <= 0) self::redirect('usersAdmin');

        $usersDir = self::getUsersImgDir();
        if ($usersDir) {
            self::deleteAvatarFiles($usersDir, $id);
            self::deleteAvatarVariants($usersDir, $id);
        }

        try {
            $db = new Database();
            if (self::colExists($db, 'users', 'avatar')) {
                $db->executeRun("UPDATE users SET avatar = '' WHERE id = :id", [':id' => $id]);
            }
        } catch (Throwable $e) {}

        if ((int)($_SESSION['user_id'] ?? 0) === $id) {
            $_SESSION['avatar'] = '';
            if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
                $_SESSION['user']['avatar'] = '';
            }
        }

        self::redirect('userAvatar?id=' . $id);
    }

    /* ===== ADMIN AVATAR DELETE ===== */
    public static function adminAvatarDelete(): void
    {
        self::requireAdmin();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        if ($adminId <= 0) self::redirect('dashboard');

        $usersDir = self::getUsersImgDir();
        if ($usersDir) {
            self::deleteAvatarFiles($usersDir, $adminId);
            self::deleteAvatarVariants($usersDir, $adminId);
        }

        try {
            $db = new Database();
            if (self::colExists($db, 'users', 'avatar')) {
                $db->executeRun("UPDATE users SET avatar = '' WHERE id = :id", [':id' => $adminId]);
            }
        } catch (Throwable $e) {}

        $_SESSION['avatar'] = '';
        if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
            $_SESSION['user']['avatar'] = '';
        }

        self::redirect('dashboard');
    }

    /* ===== OTHERS ===== */
    public static function favoritesList(): void { self::requireAdmin(); $arr = modelAdminFavorites::getAllFavorites(); self::view('favoritesList.php', compact('arr')); }
    public static function ratingsList(): void { self::requireAdmin(); $arr = modelAdminRatings::getAllRatings(); self::view('ratingsList.php', compact('arr')); }
    public static function genresList(): void { self::requireAdmin(); $arr = modelAdminGenres::getAllGenres(); self::view('genresList.php', compact('arr')); }
    public static function genreAddForm(): void { self::requireAdmin(); self::view('genreAddForm.php'); }
    public static function genreAddResult(): void { self::requireAdmin(); $ok = modelAdminGenres::createFromPost(); if(!$ok) $_SESSION['errorString']='Не удалось добавить жанр (возможно дубликат name/slug).'; self::redirect('genresAdmin'); }
    public static function genreDelete(int $id): void { self::requireAdmin(); $ok = modelAdminGenres::deleteGenre((int)$id); if(!$ok) $_SESSION['errorString']='Не удалось удалить жанр.'; self::redirect('genresAdmin'); }

    public static function userDeleteForm(int $id): void
    {
        self::requireAdmin();
        $u = modelAdminUsers::getUserByID((int)$id);
        if (!$u) { $_SESSION['errorString'] = 'Пользователь не найден'; self::redirect('usersAdmin'); }
        if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$u['id']) {
            $_SESSION['errorString'] = 'Нельзя удалить текущего администратора (самого себя).';
            self::redirect('usersAdmin');
        }
        self::view('userDeleteForm.php', compact('u'));
    }

    public static function userDeleteResult(int $id): void
    {
        self::requireAdmin();
        if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$id) {
            $_SESSION['errorString'] = 'Нельзя удалить текущего администратора (самого себя).';
            self::redirect('usersAdmin');
        }
        $ok = modelAdminUsers::deleteUser((int)$id);
        if (!$ok) $_SESSION['errorString'] = 'Не удалось удалить пользователя.';
        self::redirect('usersAdmin');
    }
}
