<?php
include "config.php";
header('Content-Type: application/json');
$date = date("Y-m-d h:i:s");
$isibo = mysqli_real_escape_string($connect, $_POST['isibo']);
$number_of_rent_house = mysqli_real_escape_string($connect, $_POST['number_of_rent_house']);
$landLord = mysqli_real_escape_string($connect, $_POST['landLord']);
$upi = mysqli_real_escape_string($connect, $_POST['upi']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);
mysqli_begin_transaction($connect);
try {
  $query = "UPDATE citizen SET isibo = '$isibo', number_of_rent_house = '$number_of_rent_house', landLord = '$landLord', upi = '$upi', updated_at = '$date', currentLocation = '$currentLocation', location = '$location', created_by = '$created_by' WHERE citizenId = '$citizenId'";
  $results = mysqli_query($connect, $query);
  $query = "UPDATE citizen SET isibo = '$isibo', upi = '$upi', updated_at = '$date', currentLocation = '$currentLocation', location = '$location', created_by = '$created_by' WHERE familyId = '$citizenId'";
  $results = mysqli_query($connect, $query);
  $query = "INSERT INTO history (citizenId,isibo,landLord,upi,previousLocation,created_by,location) 
      SELECT citizenId,isibo,landLord,upi,currentLocation,created_by,location 
      FROM citizen WHERE citizenId = '$citizenId' OR familyId ='$citizenId'";
  if (mysqli_query($connect, $query)) {
    // On query success it will print below message.
    $MSG = 'Igikorwa cyo kwimura umuryango cyagenze neza';

    // Converting the message into JSON format.
    $json = json_encode($MSG);

    // Echo the message.
    echo $json;
  } else {
    // On query success it will print below message.
    $MSG = 'Igikorwa cyo kwimura umuryango ntabwo cyakozwe mwongere mugerageze.';

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
