<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    // echo($email);
    $user = $db->getUserId($id);
    if ($user) {
        // user ditemukan
        $response["error"] = FALSE;
        $response["uid"] = $user["unique_id"];
        $response["user"]["id"] = $user["unique_id"];
        $response["user"]["name"] = $user["name"];
        $response["user"]["username"] = $user["username"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["gambar"] = $user["gambar"];
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
            $response["error_msg"] = "gagal get user";
            echo json_encode($response);
    }
}