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
    if($db->getEmail($email)){
        $response["error"] = TRUE;
        $response["error_ms"] = "email sudah ada " . $email;
        echo json_encode($response);
    }else{
        
        
        $user = $db->editemail($username ,$email);
        
        
        if($user){
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["gambar"] = $user["gambar"];
            echo json_encode($response);
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