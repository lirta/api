<?php
 
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
if (isset($_POST['name']) && isset($_POST) && isset($_POST['email']) && isset($_POST['password'])&& isset($_POST['gambar'])) {
 
    // menerima parameter POST ( name, email, password )
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gambar = $_POST['gambar'];
 
    // Cek jika user ada dengan email dan username yang sama
    if ($db->isUserExisted($email)) {
        // user telah ada
        $response["error"] = TRUE;
        $response["error_msg"] = "User telah ada dengan email " . $email;
        echo json_encode($response);
    }else if($db->isUsername($username)){
        $response["error"] = TRUE;
        $response["error_msg"] = "User telah ada dengan username " . $username;
        echo json_encode($response);
    } else {
        // buat user baru
        $user = $db->simpanUser($name, $username,  $email, $gambar, $password );
        if ($user) {
            // simpan user berhasil
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["id"] = $user["unique_id"];
            $response["user"]["name"] = $user["name"];
            $response["user"]["username"] = $user["username"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["gambar"] = $user["gambar"];
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
    $response["error_msg"] = "Parameter (name, email, username, atau password) ada yang kurang";
    $response["data"]=$_POST['name'];
    echo json_encode($response);
}
?>