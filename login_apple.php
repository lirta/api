<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
echo("masuk");
 
if (isset($_POST['email']) && isset($_POST['name'] )) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $gambar = "default.jpg";


    $user = $db->cekEmailApple($email, $name, $gambar);
    if ($user != false) {
        $response["error"] = TRUE;
        $response["error_msg"] = "user sudah ada";
        $response["user"]["id"]=$user["unique_id"];
        $response["user"]["name"] = $user["name"];
        $response["user"]["username"] = $user["username"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["gambar"] = $user["gambar"];
        echo json_encode($response);
    }else{
        $user = $db->apple($email, $name, $gambar);
        if ($user != false) {
        $response["error"] = TRUE;
        $response["error_msg"] = "berhasil di simpan";
        $response["user"]["id"]=$user["unique_id"];
        $response["user"]["name"] = $user["name"];
        $response["user"]["username"] = $user["username"];
        $response["user"]["email"] = $user["email"];
        $response["user"]["gambar"] = $user["gambar"];
        echo json_encode($response);
        }else{
        $response["error"] = "gagal menyimpan user";
        echo json_encode($response);
        }
    }
}