<?php

use Core\Http\Request\Request;
use Core\Http\Response\Response;
use Core\Database\Database;
use Repositories\CustomerRepository;
use Service\TransferService;

return [
    Request::class => DI\create(Request::class),
    Response::class => DI\create(Response::class),

    PDO::class => function () {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_DATABASE') ?: 'customer_api';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
        return new PDO($dsn, $username, $password);
    },

    // Database class
    Database::class => DI\autowire(Database::class)
        ->constructor(
            DI\get('DB_HOST'),
            DI\get('DB_PORT'),
            DI\get('DB_DATABASE'),
            DI\get('DB_USERNAME'),
            DI\get('DB_PASSWORD')
        ),

    // Repositories
    CustomerRepository::class => DI\autowire(CustomerRepository::class)
        ->constructor(DI\get(PDO::class)),

    // Services
    TransferService::class => DI\autowire(TransferService::class)
        ->constructor(DI\get(CustomerRepository::class)),
];