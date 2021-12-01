<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['password'])) {
 
    // menerima parameter POST ( email dan password )
    $email = $_POST['email'];
    $password = $_POST['password'];
 
    // get the user by email and password
    // get user berdasarkan email dan password
    $userm = $db->getUserByEmailAndPassword($email, $password);
 
    if ($userm != false) {
        // userm ditemukan
        $response["error"] = FALSE;
        $response["uid"] = $userm["unique_id"];
        $response["user"]["id"] = $userm["unique_id"];
        $response["user"]["name"] = $userm["name"];
        $response["user"]["username"] = $userm["username"];
        $response["user"]["email"] = $userm["email"];
        $response["user"]["gambar"] = $userm["gambar"];
        echo json_encode($response);
    } else {
        // get user berdasarkan username
        $useru = $db->getUserByUsernameAndPassword($email, $password);
        
        if ($useru != false) {
            // useru ditemukan
            $response["error"] = FALSE;
            $response["uid"] = $useru["unique_id"];
            $response["user"]["id"] = $useru["unique_id"];
            $response["user"]["name"] = $useru["name"];
            $response["user"]["username"] = $useru["username"];
            $response["user"]["email"] = $useru["email"];
            $response["user"]["gambar"] = $useru["gambar"];
            echo json_encode($response);
        } else {
            // get user berdasarkan username
            $useru = $db->getUserByEmailAndPassword($email, $email);
            $response["error"] = TRUE;
            $response["error_msg"] = "Login gagal. Password,Email/email salah";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Parameter (email atau password) ada yang kurang";
    echo json_encode($response);
}
?>