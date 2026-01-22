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
        // __DIR__ = /projectRoot/admin/controllerAdmin
        // projectRoot = /projectRoot
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

    private static function cropAndResizeAvatar(string $srcPath, string $destPath, int $size, string $mime): bool
    {
        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($srcPath); break;
            case 'image/png':  $src = imagecreatefrompng($srcPath);  break;
            case 'image/webp': $src = imagecreatefromwebp($srcPath); break;
            default: return false;
        }
        if (!$src) return false;

        $w = imagesx($src);
        $h = imagesy($src);

        $min = min($w, $h);
        $srcX = (int)(($w - $min) / 2);
        $srcY = (int)(($h - $min) / 2);

        $dst = imagecreatetruecolor($size, $size);

        // прозрачность для PNG/WebP
        if ($mime !== 'image/jpeg') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $min, $min);

        $ok = false;
        switch ($mime) {
            case 'image/jpeg': $ok = imagejpeg($dst, $destPath, 90); break;
            case 'image/png':  $ok = imagepng($dst, $destPath, 6);   break;
            case 'image/webp': $ok = imagewebp($dst, $destPath, 90); break;
        }

        imagedestroy($src);
        imagedestroy($dst);
        return $ok;
    }

    private static function getProjectBase(): string
    {
        // /filmhub/admin/index.php -> /filmhub/admin -> /filmhub
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
        self::redirect('commentsAdmin');
    }

    public static function commentDelete(int $id): void
    {
        self::requireAdmin();
        $id = (int)$id;
        if ($id > 0) modelAdminComments::deleteById($id);
        self::redirect('commentsAdmin');
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

        // строим URL из поля avatar (если есть)
        $avatarUrl = '';
        $avatar = trim((string)($u['avatar'] ?? ''));
        if ($avatar !== '') {
            if (preg_match('~^https?://~i', $avatar)) $avatarUrl = $avatar;
            elseif ($avatar[0] === '/') $avatarUrl = $projectBase . $avatar;
            elseif (str_starts_with($avatar, 'img/')) $avatarUrl = $projectBase . '/' . $avatar;
            else $avatarUrl = $projectBase . '/img/users/' . $avatar;
        } else {
            // fallback: попробуем найти файл по id.ext
            $usersDir = self::getUsersImgDir();
            if ($usersDir) {
                foreach (['jpg','jpeg','png','webp'] as $ext) {
                    $fn = $id . '.' . $ext;
                    if (is_file($usersDir . DIRECTORY_SEPARATOR . $fn)) {
                        $avatarUrl = $projectBase . '/img/users/' . $fn;
                        break;
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

    if (empty($_FILES['avatar']['tmp_name'])) {
        $_SESSION['errorString'] = 'Файл не выбран.';
        self::redirect('userAvatar?id=' . $id);
    }

    $f = $_FILES['avatar'];

    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $_SESSION['errorString'] = 'Ошибка загрузки файла.';
        self::redirect('userAvatar?id=' . $id);
    }

    $max = 2 * 1024 * 1024;
    if (!empty($f['size']) && $f['size'] > $max) {
        $_SESSION['errorString'] = 'Файл слишком большой (макс 2MB).';
        self::redirect('userAvatar?id=' . $id);
    }

    $usersDir = self::getUsersImgDir();
    if (!$usersDir || !is_writable($usersDir)) {
        $_SESSION['errorString'] = 'Папка img/users недоступна для записи.';
        self::redirect('userAvatar?id=' . $id);
    }

    $tmp = $f['tmp_name'];

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

    // Доп. проверка: это реально картинка
    $imgInfo = @getimagesize($tmp);
    if (!$imgInfo) {
        $_SESSION['errorString'] = 'Файл не является изображением.';
        self::redirect('userAvatar?id=' . $id);
    }

    $ext = $allowed[$mime];

    // удалить старые id.*
    self::deleteAvatarFiles($usersDir, $id);

    // финальный файл (всегда 256x256)
    $filename = $id . '.' . $ext;
    $dest     = $usersDir . DIRECTORY_SEPARATOR . $filename;

    // Сначала переместим upload во временный файл внутри usersDir
    $uploadTmp = $usersDir . DIRECTORY_SEPARATOR . $id . '._upload';
    if (!move_uploaded_file($tmp, $uploadTmp)) {
        $_SESSION['errorString'] = 'Не удалось сохранить файл.';
        self::redirect('userAvatar?id=' . $id);
    }

    // Crop + resize в dest
    $ok = self::cropAndResizeAvatar($uploadTmp, $dest, 256, $mime);
    @unlink($uploadTmp);

    if (!$ok) {
        $_SESSION['errorString'] = 'Не удалось обработать изображение (GD).';
        self::redirect('userAvatar?id=' . $id);
    }

    // записать в БД (если есть users.avatar)
    try {
        $db = new Database();
        if (self::colExists($db, 'users', 'avatar')) {
            $db->execute(
                "UPDATE users SET avatar = :a WHERE id = :id",
                [':a' => $filename, ':id' => $id]
            );
        }
    } catch (Throwable $e) {
        // файл уже сохранён — не критично
    }

    // если админ обновил СВОЙ аватар — обновим сессию
    if ((int)($_SESSION['user_id'] ?? 0) === $id) {
        $_SESSION['avatar'] = $filename;
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
        if ($usersDir) self::deleteAvatarFiles($usersDir, $id);

        try {
            $db = new Database();
            if (self::colExists($db, 'users', 'avatar')) {
                $db->execute("UPDATE users SET avatar = '' WHERE id = :id", [':id' => $id]);
            }
        } catch (Throwable $e) {}

        self::redirect('userAvatar?id=' . $id);
    }

    /* ===== ADMIN AVATAR DELETE (для dashboard кнопки) ===== */
    public static function adminAvatarDelete(): void
    {
        self::requireAdmin();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        if ($adminId <= 0) self::redirect('dashboard');

        $usersDir = self::getUsersImgDir();
        if ($usersDir) self::deleteAvatarFiles($usersDir, $adminId);

        try {
            $db = new Database();
            if (self::colExists($db, 'users', 'avatar')) {
                $db->execute("UPDATE users SET avatar = '' WHERE id = :id", [':id' => $adminId]);
            }
        } catch (Throwable $e) {}

        self::redirect('dashboard');
    }

    /* ===== OTHERS (оставляю как было) ===== */
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
