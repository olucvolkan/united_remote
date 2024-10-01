<?php

namespace Core\Repository;

use PDO;

interface RepositoryInterface
{
    public function __construct(PDO $pdo);
    public function findAll();
    public function findById($id);
    public function getTableName(): string;
}