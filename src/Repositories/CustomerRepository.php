<?php
namespace Repositories;

use Core\Repository\Repository;
use PDO;

class CustomerRepository extends Repository {
    private PDO $pdo;
    public string $tableName = 'customers';
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
        $this->setTableName($this->tableName);
    }

    public function createCustomer($name, $surname, $balance): int {
        $stmt = $this->pdo->prepare("INSERT INTO customers (name, surname, balance) VALUES (?, ?, ?)");
        $stmt->execute([$name, $surname, $balance]);
        return $this->pdo->lastInsertId();
    }

    public function updateCustomer($id, $name, $surname, $balance) {
        $stmt = $this->pdo->prepare("UPDATE customers SET name = ?, surname = ?, balance = ? WHERE id = ?");
        return $stmt->execute([$name, $surname, $balance, $id]);
    }

    public function deleteCustomer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}