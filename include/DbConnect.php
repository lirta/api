<?php 
class DbConnect{
    private $con;
 
    function __construct(){
 
    }
 
    function connect(){
        include_once dirname(__FILE__) . '/Config.php';
        $this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if(mysqli_connect_errno()){
            echo "FAILED TO CONNECT to MYSQL : " . mysqli_connect_error();
        }
        return $this->con;
    }
} ?>