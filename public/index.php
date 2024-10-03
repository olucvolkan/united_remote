<?php

use Core\Factory\ControllerFactory\ControllerFactory;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use Core\Router\Router;
use DI\ContainerBuilder;
use Utils\Env;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
Env::load(__DIR__ . '/../.env');

// Build the DI container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/Config/di.php');
$container = $containerBuilder->build();

// Resolve core components from the DI container
$router = $container->get(Router::class);

// Load all controllers from the container
$controllers = ControllerFactory::loadControllersFromContainer($container, __DIR__ . '/../src/Controller', 'Controller');

// Load routes from the routes file
$router->loadRoutesFromFile(__DIR__ . '/../src/Routes/web.php');

// Run the router to handle requests
$router->run();