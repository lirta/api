<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['userId'] ) && isset($_POST['deviceId'] ) && isset($_POST['deviceLat'] )&& isset($_POST['deviceLong'] )) 
{
    $userId = $_POST['userId'];
    $deviceId = $_POST['deviceId'];
    $deviceLat = $_POST['deviceLat'];
    $deviceLong = $_POST['deviceLong'];

    $device= $db->updateDeviceUser($userId, $deviceId, $deviceLat, $deviceLong);
    if ($device == true) {
        $response["error"] = false;
        $response["error_msg"] = "berhasil update device, id user, lat dan long";
        echo json_encode($response);
    }else{
        $response["error"] = true;
        $response["error_msg"] = "gagal update device, id user, lat dan long";
        echo json_encode($response);
    }
    
}