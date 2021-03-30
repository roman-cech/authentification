<?php
require_once "config.php";

class Database
{
    public $conn;

    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname" . DB_NAME, DB_USER, DB_PASS);
            $this->conn->exec("set names utf8");
        }catch (PDOException $exception){
            echo "Database could not be connected: " . $exception->getMessage();
        }
        return $this->conn;
    }
}