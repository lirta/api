<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['id'] ) && isset($_POST['deviceId'] )) 
{
    $idUser = $_POST['id'];
    $deviceId= $_POST['deviceId'];
}