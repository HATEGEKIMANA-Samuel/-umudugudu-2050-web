<?php
include "config.php";
header('Content-Type: application/json');
// CREATE CITIZEN
$date = date("Y-m-d h:i:s");
$isibo = mysqli_real_escape_string($connect, $_POST['isibo']);
$upi = mysqli_real_escape_string($connect, $_POST['upi']);
$is_family_heading = mysqli_real_escape_string($connect, $_POST['is_family_heading']);
$familyCategory = mysqli_real_escape_string($connect, $_POST['familyCategory']);
$movementType = mysqli_real_escape_string($connect, $_POST['movementType']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$familyId = mysqli_real_escape_string($connect, $_POST['familyId']);
$documentNumber = mysqli_real_escape_string($connect, $_POST['documentNumber']);
mysqli_begin_transaction($connect);
try {
  $query = "UPDATE citizen SET isibo = '$isibo', upi = '$upi', updated_at = '$date', is_family_heading = '$is_family_heading', familyCategory = '$familyCategory', currentLocation = '$currentLocation', location = '$location', created_by = '$created_by', familyId = '$familyId' WHERE documentNumber = '$documentNumber' ";
  $results = mysqli_query($connect, $query);

  $query = "INSERT INTO history (citizenId,movementType,isibo,upi,previousLocation,created_by,created_at,location) VALUES('$familyId','$movementType','$isibo','$upi','$currentLocation','$created_by','$date', '$location')";
  if (mysqli_query($connect, $query)) {
    // On query success it will print below message.
    $MSG = 'Igikorwa cyo kwimura cyagenze neza';

    // Converting the message into JSON format.
    $json = json_encode($MSG);

    // Echo the message.
    echo $json;
  } else {
    // On query success it will print below message.
    $MSG = 'Igikorwa cyo kwimura ntabwo cyakozwe mwongere mugerageze.';

    // Converting the message into JSON format.
    $json = json_encode($MSG);

    // Echo the message.
    echo $json;
  }
  mysqli_commit($connect);
  $connect->close();
} catch (Exception $exception) {
  // $database->rollBack();
  mysqli_rollback($connect);
  echo  json_encode("Igikorwa cyo kwimura ntabwo cyakozwe mwongere mugerageze.");
  $connect->close();
  // var_dump($exception->getMessage());
  die();
}
