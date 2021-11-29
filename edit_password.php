<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['newpassword'])) {

    $email = $_POST['email'];
    $oldpassword = $_POST['password'];
    $passwordd = $_POST['newpassword'];
    
    
    if ($pass = $db->cekPassword($email, $oldpassword,$passwordd)) {
        
        if($user=$user = $db->getUser($email,)){
            
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["gambar"] = $user["gambar"];
            echo json_encode($response);
        }
        // userm ditemukan
    } else {
        $response["error"] = true;
        $response["error_msg"] = "Password lama anda salah";
        echo json_encode($response);
    }

}