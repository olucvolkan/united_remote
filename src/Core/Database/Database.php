<?php

namespace Core\Database;

use PDO;
use PDOException;

class Database
{
    private string $host;
    private int $port;
    private string $dbName;
    private string $username;
    private string $password;
    private ?PDO $pdo = null;

    public function __construct($host, $port, $dbname, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbName = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect()
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName};charset=utf8mb4";
            try {
                $this->pdo = new PDO($dsn, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }
        return $this->pdo;
    }
}