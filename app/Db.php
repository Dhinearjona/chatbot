<?php

class Db
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = self::connect();
    }

    private static function connect(): PDO
    {
        $host = 'localhost';
        $dbname = 'k12_chatbot';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        try {
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    // Prepare statement
    public function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    // Select query
    public function query(string $sql, array $params = []): array
    {
        if (empty($params)) {
            $stmt = $this->pdo->query($sql);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}