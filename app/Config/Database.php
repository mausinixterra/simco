<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];
            $port = $_ENV['DB_PORT'];
            $dsn  = "pgsql:host=$host;port=$port;dbname=$db;options='--client_encoding=UTF8'";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                
                // Show database error page
                self::showDatabaseErrorPage($e->getMessage());
            }
        }
        return self::$instance;
    }

    /**
     * Shows the database error page and exits
     * 
     * @param string $errorMessage The error message to display (only in debug mode)
     */
    private static function showDatabaseErrorPage(string $errorMessage): void
    {
        // Set HTTP status code
        http_response_code(503); // Service Unavailable
        
        // Include the error view
        require_once __DIR__ . '/../Views/errors/database.php';
        exit;
    }
}