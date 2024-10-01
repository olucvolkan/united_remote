<?php

namespace Utils;

use Dotenv\Dotenv;

class Env {
    public static function load(string $filePath): void {
        $dotenv = Dotenv::createImmutable(dirname($filePath));
        $dotenv->load();
    }

    public static function get(string $key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}