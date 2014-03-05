<?php

class DB {
    static $db;

    static function init($config){
        self::$db = new PDO('mysql:host='.$config['db']['host'].';port='.$config['db']['port'].';dbname='.$config['db']['name'],
                            $config['db']['user'],
                            $config['db']['pass'],
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
