<?php
 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
//  echo json_encode(isset($_POST['name']) ); 
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
 
    // menerima parameter POST ( name, email, password )
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
// //  echo json_encode($name ); 
// //  echo json_encode($email ); 
//  echo json_encode($name ); 
//  echo json_encode($email ); 
//  echo json_encode($password ); 
 
    // Cek jika user ada dengan email yang sama
    if ($db->isUserExisted($email)) {
        // user telah ada
        $response["error"] = TRUE;
        $response["error_msg"] = "User telah ada dengan email " . $email;
        echo json_encode($response);
    } else {
        // buat user baru
        $data = $db->simpanUser($name, $email, $password);
        if ($data) {
            // simpan data berhasil
            $response["error"] = FALSE;
            $response["uid"] = $data["unique_id"];
            $response["data"]["name"] = $data["name"];
            $response["data"]["email"] = $data["email"];
            echo json_encode($response);
        } else {
            // gagal menyimpan data
            $response["error"] = TRUE;
            $response["error_msg"] = "Terjadi kesalahan saat melakukan registrasi";
            echo json_encode($response);
        }
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Parameter (name, email, atau password) ada yang kurang";
    echo json_encode($response);
}
?>