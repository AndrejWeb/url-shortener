<?php

namespace App\Database;

class DB
{

    private static $_DB_HOST = 'localhost';
    private static $_DB_NAME = 'url_shortener';
    private static $_DB_USER = 'root';
    private static $_DB_PASS = '';
    private static $_DB;

    public static function getInstance() {
        try {
            self::$_DB = new \PDO('mysql:host='.self::$_DB_HOST.';dbname='.self::$_DB_NAME, self::$_DB_USER, self::$_DB_PASS);
        } catch (\PDOException $e) {
            exit();
        }

        return self::$_DB;
    }

    public static function closeDBConnection()
    {
        self::$_DB = null;
        return self::$_DB;
    }

}
