<?php
include "config.php";
header('Content-Type: application/json');
// CREATE CITIZEN
$date = date("Y-m-d h:i:s");
$replacer = mysqli_real_escape_string($connect, $_POST['replacer']);
$citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);

$exheader = mysqli_query($connect, "SELECT number_of_rent_house,landLord FROM citizen WHERE citizenId = '$citizenId' limit 1");
$rh = mysqli_fetch_array($exheader);
mysqli_begin_transaction($connect);
try {
  $query = "UPDATE citizen SET number_of_rent_house ='{$rh["number_of_rent_house"]}', landLord ='{$rh["landLord"]}', updated_at = '$date', is_family_heading ='1', familyCategory='head', familyId='0' WHERE citizenId = '$replacer'";
  $results = mysqli_query($connect, $query);
  $query = "UPDATE citizen SET familyId = '$replacer' WHERE familyId = '$citizenId'";
  $results = mysqli_query($connect, $query);
  $query = "UPDATE citizen SET number_of_rent_house ='', landLord ='', updated_at = '$date', is_family_heading ='0', familyCategory='xhead', familyId='$replacer' WHERE citizenId = '$citizenId'";
  if (mysqli_query($connect, $query)) {
    // On query success it will print below message.
    $MSG = 'Igikorwa cyo guhindura umukuru wu muryango cyagenze neza';
    // Converting the message into JSON format.
    $json = json_encode($MSG);
    // Echo the message.
    echo $json;
  } else {
    // On query success it will print below message.
    $MSG = 'Igikorwa guhindura umukuru wu muryango ntabwo cyakozwe mwongere mugerageze.';

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
  echo  json_encode("Igikorwa guhindura umukuru wu muryango ntabwo cyakozwe mwongere mugerageze.");
  $connect->close();
  // var_dump($exception->getMessage());
  die();
}
