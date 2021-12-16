<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
// echo("hallo");
 
if (
isset($_POST['userId']) && 
isset($_POST['deviceId'] ) &&
isset($_POST['deviceOsType'] ) &&
isset($_POST['deviceName'] ) &&
isset($_POST['deviceManufactur'] ) &&
isset($_POST['deviceModel'] ) &&
isset($_POST['deviceSDK'] ) &&
isset($_POST['deviceProduct'] ) &&
isset($_POST['deviceOsVersion'] ) &&
isset($_POST['deviceBoard'] ) &&
isset($_POST['deviceBrand'] ) &&
isset($_POST['deviceDisplay'] ) &&
isset($_POST['deviceHardware'] ) &&
isset($_POST['deviceHost'] ) &&
isset($_POST['deviceType']) &&
isset($_POST['deviceImei']) &&
isset($_POST['deviceLat']) &&
isset($_POST['deviceLong'])
) {
    // echo(1);
$userId = $_POST['userId'];
$deviceId = $_POST['deviceId'];
$deviceOsType = $_POST['deviceOsType'];
$deviceName = $_POST['deviceName'];
$deviceManufactur = $_POST['deviceManufactur'];
$deviceModel = $_POST['deviceModel'];
$deviceSDK = $_POST['deviceSDK'];
$deviceProduct = $_POST['deviceProduct'];
$deviceOsVersion = $_POST['deviceOsVersion'];
$deviceBoard = $_POST['deviceBoard'];
$deviceBrand = $_POST['deviceBrand'] ;
$deviceDisplay = $_POST['deviceDisplay'];
$deviceHardware = $_POST['deviceHardware'];
$deviceHost = $_POST['deviceHost'];
$deviceType = $_POST['deviceType'];
$deviceImei = $_POST['deviceImei'];
$deviceLat = $_POST['deviceLat'];
$deviceLong = $_POST['deviceLong'];

//    echo($userId);
   
    if ($db->cekDevice($deviceId)) {
        // echo("device sudah ada");
            $device = $db->updateDevice(
                $userId,
                $deviceId,
                $deviceOsType,
                $deviceName,
                $deviceManufactur,
                $deviceModel,
                $deviceSDK,
                $deviceProduct,
                $deviceOsVersion,
                $deviceBoard,
                $deviceBrand,
                $deviceDisplay,
                $deviceHardware,
                $deviceHost,
                $deviceType,
                $deviceImei,
                $deviceLat,
                $deviceLong 
        );
        
        if ($device == true) {
            $response["error"] = false;
            $response["error_msg1"] = "device ada";
            $response["error_msg"] = "Berhasil";
            $response["device"]["deviceId"] = $deviceId;
            echo json_encode($response);
        }else{
            
            $response["error"] = true;
            $response["error_msg1"] = "device ada";
            $response["device"] = "Gagal";
            echo json_encode($response);
        }
    }else{
        // echo("device blm ada");
        $device = $db->insertDevice(
            $userId,
            $deviceId,
            $deviceOsType,
            $deviceName,
            $deviceManufactur,
            $deviceModel,
            $deviceSDK,
            $deviceProduct,
            $deviceOsVersion,
            $deviceBoard,
            $deviceBrand,
            $deviceDisplay,
            $deviceHardware,
            $deviceHost,
            $deviceType,
            $deviceImei,
            $deviceLat,
            $deviceLong 
        );
        
        if ($device == true) {
            $response["error"] = false;
            $response["error_msg1"] = "device blm ada";
            $response["error_msg"] = "Berhasil";
            $response["device"]["deviceId"] = $deviceId;
            echo json_encode($response);
        }else{
            
            $response["error"] = true;
            $response["error_msg1"] = "device blm ada";
            $response["error_msg"] = "Gagal";
            echo json_encode($response);
        }
    
    }





}else{
    echo(2);
}