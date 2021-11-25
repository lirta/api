<?php
require_once 'include/DB_Functions.php';
$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);

$image = $_FILE['image']['name'];
$email=$_POST['email'];
$imagePath ='uploads/'.$image;
$tmp_name = $_FILES['image']['tmp_name'];

move_uploaded_file($tmp_name, $imagePath);

echo $image;
echo $email;
echo $image;

