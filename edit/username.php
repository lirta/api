<?php
    include 'coneksi.php';

    $response = array("error" => FALSE);

if (isset($_POST['username']) && isset($_POST['email'])){
    //menerima data
    $username = $_POST['username'];
    $email = $_POST['email'];

    $query = $db->prepare("SELECT * FROM `tbl_user` WHERE username = :username");
    $query->bindParam(":username", $username);
    $query->execute();
    $result=$query->fetchALL();
    $response["hasil"]=$result;
    echo json_encode($response);
    if($query->rowCount() == 0){
        $query = $db->prepare("UPDATE `tbl_user` SET `username`=:nama WHERE email=:email");
        $query->bindParam(":nama", $username);
        $query->bindParam(":email", $email);
        $query->execute();
        // $query->close();

        

        $response["error"] =$query;
        $response["username"] =$username;
        $response["email"] =$email;
        // echo json_encode($response);
    }
}