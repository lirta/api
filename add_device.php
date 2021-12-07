<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['androidId']) && 
isset($_POST['device'] ) &&
isset($_POST['deviceId'] ) &&
isset($_POST['deviceType'] ) &&
isset($_POST['deviceModel'] ) &&
isset($_POST['deviceManufactur'] ) &&
isset($_POST['deviceVersionSDK'] ) &&
isset($_POST['deviceProduct'] ) &&
isset($_POST['deviceHost']) &&
isset($_POST['imei']) &&
isset($_POST['lat']) &&
isset($_POST['long'])
) {
    $androidId = $_POST['androidId'];
    $device = $_POST['device'];
    $deviceId = $_POST['deviceId'];
    $deviceType = $_POST['deviceType'];
    $deviceModel = $_POST['deviceModel'];
    $deviceManufactur = $_POST['deviceManufactur'];
    $deviceVersionSDK = $_POST['deviceVersionSDK'];
    $deviceProduct = $_POST['deviceProduct'];
    $deviceHost = $_POST['deviceHost'];
    $imei = $_POST['imei'];
    $lat = $_POST['lat'];
    $long = $_POST['long'];

    // echo($imei);
    // echo($lat);
    echo($long);

$device = $db->insertDevice(
    $androidId,
    $device,
    $deviceId,
    $deviceType,
    $deviceModel,
    $deviceManufactur,
    $deviceVersionSDK,
    $deviceProduct,
    $deviceHost,
    $imei,
    $lat,
    $long
);

if ($device == true) {
    $response["error"] = false;
    $response["error_msg"] = "Berhasil";
    echo json_encode($response);
}else{
    
    $response["error"] = true;
    $response["error_msg"] = "Gagal";
    echo json_encode($response);
}

}