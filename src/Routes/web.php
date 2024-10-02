<?php

global $router, $controllers;

$controllerMap = [];
foreach ($controllers as $controller) {
    $controllerMap[get_class($controller)] = $controller;
}

$customerController = $controllerMap['Controller\CustomerController'] ?? null;

if ($customerController) {
    $router->get('/api/customers/{id}', [$customerController, 'get']);
    $router->post('/api/customers', [$customerController, 'post']);
    $router->put('/api/customers/{id}', [$customerController, 'put']);
    $router->delete('/api/customers/{id}', [$customerController, 'delete']);
}

$accountController = $controllerMap['Controller\AccountController'] ?? null;

if ($accountController) {
    $router->post('/api/accounts/{id}/deposit', [$accountController, 'deposit']);
    $router->post('/api/accounts/{id}/withdraw', [$accountController, 'withdraw']);
    $router->post('/api/accounts/transfer', [$accountController, 'transfer']);
    $router->get('/api/accounts/{id}', [$accountController, 'getAccountBalance']);
}

