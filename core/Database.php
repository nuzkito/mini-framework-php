<?php

class Database {
    static $db;

    static function init($config){
        self::$db = new PDO('mysql:host='.$config['host'].';port='.$config['port'].';dbname='.$config['name'],
                            $config['user'],
                            $config['pass'],
                            array(
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)
                            );
    }

    static function query($query, $params = null, $fetch = true){

        $reponse = self::$db->prepare($query);
        $reponse->execute($params);

        if ($fetch) {
            return $reponse->fetchAll(); 
        }
        return $reponse;
    }

    static function lastInsertId($name = null)
    {
        return self::$db->lastInsertId($name);
    }

}
