<?php
class Database {

    public PDO $dbh;

    public function __construct() {
        // ✅ Поменяй при необходимости под свой хост/пользователя/пароль
        $host   = "localhost";
        $dbname = "filmhub";
        $user   = "root";
        $pass   = "";

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        try {
            $this->dbh = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch(PDOException $e) {
            die("DB connection error: " . $e->getMessage());
        }
    }

    public function getAll(string $sql, array $params = []): array {
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOne(string $sql, array $params = []): ?array {
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function executeRun(string $sql, array $params = []): bool {
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string {
        return $this->dbh->lastInsertId();
    }
}

