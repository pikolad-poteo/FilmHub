<?php
class controllerAdminMovies
{
    private static function view(string $file, array $vars = []): void {
        if (!empty($vars)) extract($vars, EXTR_SKIP);
        include __DIR__ . '/../viewAdmin/' . $file;
    }

    private static function redirect(string $to): void {
        header("Location: {$to}");
        exit;
    }

    private static function requireAdmin(): void {
        if (empty($_SESSION['is_admin'])) self::redirect('index.php');
    }

    /**
     * Загружает постер в /img/movies и возвращает ИМЯ файла для БД: "xxx.ext"
     * Возвращает null если файл не загружали.
     */
    private static function handlePosterUpload(string $fileField, string $title, ?int $year): ?string
    {
        if (empty($_FILES[$fileField]) || !is_array($_FILES[$fileField])) {
            return null;
        }

        $f = $_FILES[$fileField];

        if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('Ошибка загрузки файла (код: ' . (int)$f['error'] . ').');
        }

        $tmp = (string)($f['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new Exception('Временный файл не найден.');
        }

        $size = (int)($f['size'] ?? 0);
        if ($size <= 0) throw new Exception('Файл пустой.');
        if ($size > 12 * 1024 * 1024) throw new Exception('Файл слишком большой (лимит 12MB).');

        $origName = (string)($f['name'] ?? '');
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed, true)) {
            throw new Exception('Разрешены только: ' . implode(', ', $allowed));
        }

        // MIME finfo (мягко)
        if (function_exists('finfo_open')) {
            $fi = finfo_open(FILEINFO_MIME_TYPE);
            if ($fi) {
                $mime = (string)finfo_file($fi, $tmp);
                finfo_close($fi);
                if ($mime !== '' && !preg_match('~^image/(jpeg|png|webp)$~i', $mime)) {
                    throw new Exception('Файл не похож на изображение (mime: ' . $mime . ').');
                }
            }
        }

        // Корень проекта: /filmhub
        $projectRoot = realpath(__DIR__ . '/../../'); // admin/controllerAdmin -> admin -> project root
        if (!$projectRoot) throw new Exception('Не удалось определить корень проекта.');

        $targetDir = $projectRoot . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'movies';
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
                throw new Exception('Не удалось создать папку: ' . $targetDir);
            }
        }

        require_once __DIR__ . '/../../inc/media.php'; // на всякий, если где-то не подцепился

        $baseName = fh_movie_poster_filename($title, $year, $ext);
        $fileName = $baseName;

        // если занято — добавим -2, -3 ...
        $i = 2;
        while (is_file($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $fileName = preg_replace('~\.[a-z0-9]+$~i', '', $baseName) . '-' . $i . '.' . $ext;
            $i++;
        }

        $absTarget = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($tmp, $absTarget)) {
            throw new Exception('Не удалось сохранить файл постера.');
        }

        // ✅ в БД — только имя
        return $fileName;
    }

    public static function movieList(): void {
        self::requireAdmin();
        $arr = modelAdminMovies::getAllMovies();
        self::view('moviesList.php', compact('arr'));
    }

    public static function movieAddForm(): void {
    self::requireAdmin();
    $genres = modelAdminGenres::getAllGenres();
    self::view('movieAddForm.php', compact('genres'));
}

public static function movieAddResult(): void {
    self::requireAdmin();

    try {
        // 1) сначала пробуем загрузку файла (она всегда приоритетнее ручного поля)
        $title = trim((string)($_POST['title'] ?? ''));
        $year  = ($_POST['year'] ?? '') !== '' ? (int)$_POST['year'] : null;

        $uploaded = self::handlePosterUpload('poster_file', $title, $year);

        if ($uploaded) {
            // сохраняем в БД только имя файла
            $_POST['poster'] = basename($uploaded);
        } else {
            // загрузки нет — нормализуем ручной ввод (URL оставляем, путь режем до имени)
            $_POST['poster'] = self::normalizePosterInput($_POST['poster'] ?? '');
        }

        $ok = modelAdminMovies::createFromPost();
        if (!$ok) {
            $_SESSION['errorString'] = 'Не удалось добавить фильм (проверь поля/уникальность title+year).';
        }
    } catch (Throwable $e) {
        $_SESSION['errorString'] = 'Постер: ' . $e->getMessage();
    }

    self::redirect('moviesAdmin');
}

    public static function movieEditForm(int $id): void {
        self::requireAdmin();
        $m = modelAdminMovies::getMovieByID($id);
        if (!$m) {
            $_SESSION['errorString'] = 'Фильм не найден';
            self::redirect('moviesAdmin');
        }
        $genres = modelAdminGenres::getAllGenres();
        self::view('movieEditForm.php', compact('m','genres'));
    }

    public static function movieEditResult(int $id): void {
        self::requireAdmin();

        try {
            $old = modelAdminMovies::getMovieByID($id);
            if (!$old) {
                $_SESSION['errorString'] = 'Фильм не найден';
                self::redirect('moviesAdmin');
            }
            $oldPoster = (string)($old['poster'] ?? '');

            $title = trim((string)($_POST['title'] ?? ''));
            $year  = ($_POST['year'] ?? '') !== '' ? (int)$_POST['year'] : null;

            // 1) загрузка файла имеет приоритет над ручным вводом
            $uploaded = self::handlePosterUpload('poster_file', $title, $year);

            if ($uploaded) {
                // новый локальный файл → удаляем старый локальный (если был)
                self::deleteLocalPosterIfNeeded($oldPoster);

                // в БД — только имя
                $_POST['poster'] = basename($uploaded);
            } else {
                // 2) нет загрузки → берём ручное поле
                $newPoster = self::normalizePosterInput($_POST['poster'] ?? '');

                // если постер поменяли:
                // - если новый стал URL → старый локальный больше не нужен → удаляем
                // - если новый стал другим именем → тоже удаляем старый локальный (чтобы не копился мусор)
                if ($newPoster !== '' && $newPoster !== $oldPoster) {
                    self::deleteLocalPosterIfNeeded($oldPoster);
                }

                $_POST['poster'] = $newPoster;
            }

            $ok = modelAdminMovies::updateFromPost($id);
            if (!$ok) $_SESSION['errorString'] = 'Не удалось обновить фильм.';
        } catch (Throwable $e) {
            $_SESSION['errorString'] = 'Постер: ' . $e->getMessage();
        }

        self::redirect('moviesAdmin');
    }

    public static function movieDeleteForm(int $id): void {
        self::requireAdmin();
        $m = modelAdminMovies::getMovieByID($id);
        if (!$m) {
            $_SESSION['errorString'] = 'Фильм не найден';
            self::redirect('moviesAdmin');
        }
        self::view('movieDeleteForm.php', compact('m'));
    }

    public static function movieDeleteResult(int $id): void {
    self::requireAdmin();

    $m = modelAdminMovies::getMovieByID($id);
    if (!$m) {
        $_SESSION['errorString'] = 'Фильм не найден';
        self::redirect('moviesAdmin');
    }

    $poster = (string)($m['poster'] ?? '');

    // удаляем запись из БД
    $ok = modelAdminMovies::deleteByID($id); // или deleteMovie($id), если добавил алиас
    if (!$ok) {
        $_SESSION['errorString'] = 'Не удалось удалить фильм.';
        self::redirect('moviesAdmin');
    }

    // удаляем локальный файл постера (URL не трогаем)
    self::deleteLocalPosterIfNeeded($poster);

    self::redirect('moviesAdmin');
}


    /* =========================
    Helpers: poster normalize/delete
    ========================= */

    private static function isUrl(string $s): bool {
        return (bool)preg_match('~^https?://~i', $s);
    }

    private static function normalizePosterInput(?string $poster): string {
        $poster = trim((string)$poster);
        if ($poster === '') return '';

        // URL сохраняем как есть
        if (self::isUrl($poster)) return $poster;

        // режем всё до имени файла (и для img/movies/x.jpg, и для /filmhub/img/movies/x.jpg)
        $poster = str_replace('\\', '/', $poster);
        return basename($poster);
    }

    private static function deleteLocalPosterIfNeeded(?string $poster): void {
        $poster = trim((string)$poster);
        if ($poster === '' || self::isUrl($poster)) return;

        $file = basename(str_replace('\\', '/', $poster));
        if ($file === '' || $file === 'default.png') return;

        $projectRoot = realpath(__DIR__ . '/../../');
        if (!$projectRoot) return;

        $abs = $projectRoot . '/img/movies/' . $file;
        if (is_file($abs)) @unlink($abs);
    }
}