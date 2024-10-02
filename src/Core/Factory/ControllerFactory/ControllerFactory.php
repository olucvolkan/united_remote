<?php

namespace Core\Factory\ControllerFactory;

use Core\BaseController\BaseController;
use DI\Container;

class ControllerFactory
{

    /**
     * Load all controllers using the DI container.
     *
     * @param Container $container The DI container.
     * @param string $controllerDirectory The directory where controllers are located.
     * @param string $namespace The base namespace of the controllers.
     * @return array List of instantiated controllers.
     */
    public static function loadControllersFromContainer(Container $container, string $controllerDirectory, string $namespace): array
    {
        $controllers = [];

        foreach (glob($controllerDirectory . '/*.php') as $file) {
            $className = $namespace . '\\' . basename($file, '.php');

            // If the class exists, get it from the DI container
            if (class_exists($className)) {
                $controllers[] = $container->get($className);
            }
        }

        return $controllers;
    }
}