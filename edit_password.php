<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $pass = $db->cekPassword($email, $password);
 
    if (!empty($hash)) {

        $update =$db->updatePassword($)
        // userm ditemukan
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Gagal edit password";
        echo json_encode($response);
    }
}