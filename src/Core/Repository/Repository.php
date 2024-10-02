<?php

namespace Core\Repository;

use PDO;
use PDOException;

class Repository implements RepositoryInterface
{
    private PDO $pdo;

    private string $tableName;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): ?array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM {$this->getTableName()}");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findById($id): ?array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}