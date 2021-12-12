<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['name'] ) && isset($_POST['gambar'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $gambar = $_POST['gambar'];

    $user = $db->cekEmailGoogle($email, $gambar, $name);
    if ($user != false) {
        echo("imail sudah ada");
            $response["error"] = TRUE;
            // $response["user"]["id"]=$user["uniqui_id"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["gambar"] = $user["gambar"];
            echo json_encode($response);
    }else{
        echo("email blm ada");
        $user= $db->save($email, $gambar, $name);
            if ($user != false) {
                $response["error"] = true;
                // $response["user"]["id"]=$user["uniqui_id"];
                $response["user"]["name"] = $user["name"];
                $response["user"]["username"] = $user["username"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["gambar"] = $user["gambar"];
                echo json_encode($response);
                # code...
            }else{
                echo("simpandata error");
            }
    }
}