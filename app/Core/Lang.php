<?php

declare(strict_types=1);

namespace App\Core;

class Lang
{
    private static array $strings = [];

    public static function load(string $langCode): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $langCode = in_array($langCode, $config['supported_langs']) ? $langCode : $config['default_lang'];
        $langFile = __DIR__ . "/../../resources/lang/{$langCode}.php";

        if (file_exists($langFile)) {
            self::$strings = require $langFile;
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$strings[$key] ?? $default;
    }
}