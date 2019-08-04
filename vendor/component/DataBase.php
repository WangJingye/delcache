<?php

namespace component;

class DataBase
{
    public static $instance;

    /**
     * @param array $database
     * @return \PDO
     * @throws \Exception
     */
    public static function instance($database = [])
    {
        if (empty($database)) {
            $database = require COMMON_PATH . 'config/db.php';
        }
        if (is_null(self::$instance)) {
            self::$instance = new \PDO("mysql:host={$database['hostname']};dbname={$database['database']};port={$database['port']}", $database['username'], $database['password']);
            self::$instance->query("SET NAMES " . $database['charset']);
        }
        return self::$instance;
    }
}
