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
        
        
        $response["error"] = true;
        $response["error_msg"] = "update password succes";
        echo json_encode($response);
        // userm ditemukan
    } else {
        $response["error"] = FALSE;
        $response["error_msg"] = "Password lama anda salah";
        echo json_encode($response);
    }

}