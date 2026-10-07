<?php

declare(strict_types=1);

namespace App\Core;

class Lang
{
    private static array $strings = [];
    private static array $catalogs = [];

    public static function load(string $langCode): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $langCode = in_array($langCode, $config['supported_langs']) ? $langCode : $config['default_lang'];
        $langFile = __DIR__ . "/../../resources/lang/{$langCode}.php";
        $catalogFile = __DIR__ . "/../../resources/lang/catalogs/{$langCode}.php";

        if (file_exists($langFile)) {
            self::$strings = require $langFile;
        }

        // Sin archivo de catálogos (p. ej. 'es'), se usa el texto de la base de datos
        self::$catalogs = file_exists($catalogFile) ? require $catalogFile : [];
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$strings[$key] ?? $default;
    }

    /**
     * Translates a database catalog value by its ID
     *
     * @param string $catalog Catalog table name (e.g. 'formulario_pqr_tipo_fuente')
     * @param string $id Catalog record ID
     * @param string $default Text from the database, used when there is no translation
     * @return string
     */
    public static function catalog(string $catalog, string $id, string $default): string
    {
        return self::$catalogs[$catalog][$id] ?? $default;
    }

    /**
     * Indicates whether the loaded language has a translation dictionary for the catalog
     *
     * @param string $catalog Catalog table name
     * @return bool
     */
    public static function hasCatalog(string $catalog): bool
    {
        return !empty(self::$catalogs[$catalog]);
    }
}
