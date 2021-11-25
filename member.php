<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$member = $db->getMember();
if ($user != null) {
    $response["member"]=$user;
    echo json_encode($response);
}else{
    $response["error"]= true;
    echo json_encode($response);
}
