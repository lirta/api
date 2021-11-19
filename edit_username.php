<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);

if (isset($_POST['username']) && isset($_POST['email'])){
    //menerima data
    $username = $_POST['username'];
    $email = $_POST['email'];
    //cek username
    if($useru = $db->getUsername($username)){
        $response["error"] = TRUE;
        $response["error"] = "Username sudah ada" . $username;
        echo json_decode($response);
    }else{
        $user = $db->editusername($username ,$email);
        if($user){
            $response["error"] = FALSE;
            $response["user"] = $user["name"];
            $response["user"] = $user["username"];
            $response["user"] = $user["email"];
            echo json_decode($response);
        }else{
            $response["error"] =TRUE;
            $response["error_mgs"] ="terjadi kesalahan";
        }
    }

}else{
    $response["error"] = TRUE;
    $response["error_msg"] = "Parameter ada yang kurang";
    echo json_encode($response);
}