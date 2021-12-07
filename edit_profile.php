<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);

if (isset($_POST['name']) && isset($_POST['email'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $user = $db->editprofile($name ,$email);
    
    // echo($name);
    // echo($email);
        
    if($user){
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["id"] = $user["unique_id"];
        $response["user"]["name"] = $user["name"];
        $response["user"]["username"] = $user["username"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["gambar"] = $user["gambar"];
        echo json_encode($response);
    }else{
        $response["error"] =TRUE;
        $response["error_mgs"] ="Gagal edit profile";
        echo json_encode($response);
    }
}