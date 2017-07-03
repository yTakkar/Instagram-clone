<?php

  class N{

    public static $e;
    public static $database;
    public static $DIR = "/faiyaz/Instagram";

    public static function _DB(){
      try {
        self::$database = new PDO('mysql:host=HOST;dbname=DB;charset=utf8mb4', 'USER', 'PASSWORD');
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $e = self::$e;
      } catch (PDOException $e) {
        echo $e->getMessage();
      }
      return self::$database;
    }

  }

?>
