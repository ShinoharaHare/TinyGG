<?php

class DAO {
    private static $connection;
    private static $status = array("error" => "no connection",
                                   "result" => false);
    private function __construct(){
        echo "__construct";
        // self::connect();
    }

    function __destruct(){
        echo "__destruct";
        // self::disconnect();
    }

    public static function connect($server, $dbuser, $dbpassword, $dbname){
        // echo "connect"."<br />";
        self::$connection = new mysqli($server, $dbuser, $dbpassword, $dbname);
        if(self::$connection->connect_error){
            self::$status["error"] = self::$connection->connect_error;
            echo "connect error!";
            return false;
        }else{
            self::$status["error"] = false;
            return true;
        }
    }

    public static function disconnect(){
        // echo "disconnect"."<br />";
        if(self::$connection){
            self::$connection->close();
            self::$status["error"] = "no connection";
        }
    }

    public static function getStatus(){
        return self::$status;
    }
    
    public static function getResult(){
        return self::$status["sqlResult"];
    }

    public static function getError(){
        return self::$status["error"];
    }

    public static function setError($errorMsg){
        self::$status["error"] = $errorMsg;
    }

    public static function query($sqlQuery){
        if(self::$connection){
            if($result = self::$connection->query($sqlQuery)){
                // print_r($result);
                if($result !== true){
                    $rows = [];
                    while($row = $result->fetch_assoc()){
                        $rows[] = $row;
                    }
                    $result->close();
                    $result = $rows;
                }
                self::$status["sqlResult"] = $result;
                self::setError(false);
            }else{
                self::setError("SQL execution fail!!");
            }
        }else{
            self::setError("no connection");
        }
    }
}

?>