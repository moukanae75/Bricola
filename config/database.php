<?php

class Database {

    private static $instance;
    
    public static function getConnection() {

        if (self::$instance === null) {

            self::$instance = new PDO(
                'mysql:host=localhost;dbname=bricola;charset=utf8mb4',
                'root',
                ''
            );
        }

        return self::$instance;
    }
}
?>