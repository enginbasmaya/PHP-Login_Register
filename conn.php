<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/config.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $config['db_host'],
                $config['db_name']
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
            } catch (PDOException $e) {
                // Never expose DB error details to the user — log only
                error_log('DB connection error: ' . $e->getMessage());

                // Throw a typed exception so callers can show a translated message via Lang::t('err_db')
                throw new DatabaseException('Database connection failed.');
            }
        }

        return self::$instance;
    }
}

/**
 * Dedicated exception type for database errors.
 * Callers catch this and display Lang::t('err_db') instead of a hardcoded string.
 */
class DatabaseException extends RuntimeException {}
