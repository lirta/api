<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['newpassword'])) {

    $email = $_POST['email'];
    $oldpassword = $_POST['password'];
    $password = $_POST['newpassword'];
    // $pass = $db->cekPassword($email, $password);

    
    // $response["email"]=$email;
    // $response["newpassword"]=$newpassword;
    // $response["password"]=$password;
    // echo json_encode($response);
    
    if ($pass = $db->cekPassword($email, $oldpassword)) {
        
        $user =$db->updatePassword($password, $email);
        if ($user) {
            # code...
            $response["error"] = true;
            $response["error_msg"] = "update password succes";
            echo json_encode($response);
        }else{
            $response["error"] = false;
            $response["error_msg"] = "update password gagal";
            // $response["error_msk"] = $encrypted_password;
            echo json_encode($response);

        }
        // userm ditemukan
    } else {
        $response["error"] = FALSE;
        $response["error_msg"] = "Password lama anda salah";
        echo json_encode($response);
    }

}