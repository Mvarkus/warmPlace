<?php

namespace App\Component;

use \PDO;

/**
 * Is responsible for returning database connections
 */
class DBConnection
{
    /**
     * Creates and returns PDO connection
     * 
     * @return PDO - pdo connection
     */
    public static function getConnection(): PDO
    {
        $database_details = require CONFIG_PATH.'/database.php';

        $host    = $database_details['host'];
        $db      = $database_details['dbname'];
        $user    = $database_details['user'];
        $pass    = $database_details['password'];
        $charset = $database_details['charset'];

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dns = "{$database_details['driver']}:host={$database_details['host']};dbname={$database_details['dbname']}:charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $pdo;
    }
}
