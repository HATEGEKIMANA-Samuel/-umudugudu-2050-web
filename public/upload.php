<?php
include_once "../web-config/grobals.php";
if(!empty($_FILES['file']['name'])){


    //File uplaod configuration
    $result = 0;
    $img=$_FILES['file']['name'];
    $log_username=$_SESSION["username"];
    $fileTmpLoc = $_FILES["file"]["tmp_name"];
    $uploadDir = "uploads/avatar/";
    $kaboom = explode(".", $img);
    $fileExt = end($kaboom);
    $fileName = time().'.'.$fileExt;


    $sql = "SELECT avatar FROM user WHERE username='{$_SESSION["username"]}' LIMIT 1";
    $query = $database->query($sql);
    $row = $database->fetch_array($query);
    $avatar = $row[0];
    if($avatar != ""){
        $picurl = "uploads/avatar/$avatar";
        if (file_exists($picurl)) { unlink($picurl); }
    }
    $moveResult = move_uploaded_file($fileTmpLoc, "uploads/avatar/$fileName");
    if ($moveResult != true) {
        $profile_pic = "images/default_profile.jpg";
        echo $profile_pic;
        exit();
    }
    include_once("includes/image_resize.php");
    $target_file = "uploads/avatar/$fileName";
    $resized_file = "uploads/avatar/$fileName";
    $wmax = 200;
    $hmax = 300;
    img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
    $sql = "UPDATE user SET avatar='$fileName' WHERE username='$log_username' LIMIT 1";
    $update=$database->query($sql);
    if($update){
        echo $target_file;
    }

}
