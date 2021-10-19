<?php
include "config.php";
header('Content-Type: application/json');
// CREATE CITIZEN
$date = date("Y-m-d");
$isibo = mysqli_real_escape_string($connect, $_POST['isibo']);
$familyId = mysqli_real_escape_string($connect, $_POST['familyId']);
$movementType = mysqli_real_escape_string($connect, $_POST['movementType']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$status = mysqli_real_escape_string($connect, $_POST['status']);
$arrival_date = mysqli_real_escape_string($connect, $_POST['arrival_date']);
$departure_date = mysqli_real_escape_string($connect, $_POST['departure_date']);
$vistorId = mysqli_real_escape_string($connect, $_POST['vistorId']);
$upi = mysqli_real_escape_string($connect, $_POST['upi']);
mysqli_begin_transaction($connect);
try {
  $query = "INSERT INTO history (citizenId,movementType,isibo,upi,previousLocation,created_by,created_at,location) VALUES('$vistorId','$movementType','$isibo','$upi','$currentLocation','$created_by','$date', '$location')";

  $results = mysqli_query($connect, $query);

  $query = "INSERT INTO visitors (visited_by,resident_id,arrival_date,departure_date,created_at,updated_at,user_id,location,village,status) VALUES('$vistorId','$familyId','$arrival_date','$departure_date','$date','$date','$created_by', '$location', '$currentLocation', '$status')";

  if (mysqli_query($connect, $query)) {
    // On query success it will print below message.
    $MSG = 'Amakuru utanze agiye mu bubiko.';

    // Converting the message into JSON format.
    $json = json_encode($MSG);

    // Echo the message.
    echo $json;
  } else {
    // On query success it will print below message.
    $MSG = 'Amakuru utanze ntabwo agiye mu bubiko.';

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
  echo  json_encode("Amakuru utanze ntabwo agiye mu bubiko.");
  $connect->close();
  // var_dump($exception->getMessage());
  die();
}
