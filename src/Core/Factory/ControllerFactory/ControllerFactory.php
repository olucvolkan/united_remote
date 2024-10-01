<?php

namespace Core\Factory\ControllerFactory;

use Core\BaseController\BaseController;

class ControllerFactory
{

    public static function loadControllers(string $controllerDirectory, string $namespace, array $dependencies = []): array
    {
        $controllers = [];

        foreach (glob($controllerDirectory . '/*.php') as $file) {
            $className = $namespace . '\\' . basename($file, '.php');

            if (class_exists($className) && is_subclass_of($className, BaseController::class)) {
                $controllers[] = self::createControllerInstance($className, $dependencies);
            }
        }

        return $controllers;
    }

    private static function createControllerInstance(string $className, array $dependencies)
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("Class can not initialize! $className");
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}