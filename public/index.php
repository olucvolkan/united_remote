<?php

use Core\Database\Database;
use Core\Factory\ControllerFactory\ControllerFactory;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use Core\Router\Router;
use Utils\Env;

require_once __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');
$host = Env::get('DB_HOST', '127.0.0.1');
$port = Env::get('DB_PORT', '3306');
$dbname = Env::get('DB_DATABASE', 'customer_api');
$username = Env::get('DB_USERNAME', 'root');
$password = Env::get('DB_PASSWORD', '');

$database = new Database($host, $port, $dbname, $username, $password);
$pdo = $database->connect();
$router = new Router();
$request = new Request();
$response = new Response();
$dependencies = [$request, $response, $database->connect()];
$controllers = ControllerFactory::loadControllers(__DIR__ . '/../src/Controller', 'Controller', $dependencies);

$router->loadRoutesFromFile(__DIR__ . '/../src/Routes/web.php');
$router->run();

