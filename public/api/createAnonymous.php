<?php
include "config.php";
header('Content-Type: application/json');
// CREATE CITIZEN
$date = date("Y-m-d");
$givenName = mysqli_real_escape_string($connect, $_POST['givenName']);
$familyName = mysqli_real_escape_string($connect, $_POST['familyName']);
$otherName = mysqli_real_escape_string($connect, $_POST['otherName']);
$gender = mysqli_real_escape_string($connect, $_POST['gender']);
$dob = mysqli_real_escape_string($connect, $_POST['dob']);
$martialstatus = mysqli_real_escape_string($connect, $_POST['martialstatus']);
$ubudehe = mysqli_real_escape_string($connect, $_POST['ubudehe']);
$birthplace = mysqli_real_escape_string($connect, $_POST['birthplace']);
$birthNationality = mysqli_real_escape_string($connect, $_POST['birthNationality']);
$otherNationality = mysqli_real_escape_string($connect, $_POST['otherNationality']);
$email = mysqli_real_escape_string($connect, $_POST['email']);
$mobile = mysqli_real_escape_string($connect, $_POST['mobile']);
$isibo = mysqli_real_escape_string($connect, $_POST['isibo']);
$level_of_education = mysqli_real_escape_string($connect, $_POST['level_of_education']);
$occupation = mysqli_real_escape_string($connect, $_POST['occupation']);
$number_of_rent_house = mysqli_real_escape_string($connect, $_POST['number_of_rent_house']);
$landLord = mysqli_real_escape_string($connect, $_POST['landLord']);
$upi = mysqli_real_escape_string($connect, $_POST['upi']);
$documentNumber = mysqli_real_escape_string($connect, $_POST['documentNumber']);
$is_family_heading = mysqli_real_escape_string($connect, $_POST['is_family_heading']);
$familyCategory = mysqli_real_escape_string($connect, $_POST['familyCategory']);
$documentType = mysqli_real_escape_string($connect, $_POST['documentType']);
//$movementType = mysqli_real_escape_string($connect, $_POST['movementType']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$status = mysqli_real_escape_string($connect, $_POST['status']);
$gender = ($gender == "GABO" ? "Male" : "Female");

$query = "INSERT INTO anonymous_citizen (givenName,familyName,otherName,gender,dob,martialstatus,ubudehe,birthplace, documentNumber, birthNationality, otherNationality, email, mobile, isibo, number_of_rent_house, landLord, upi, created_at, updated_at, is_family_heading, familyCategory, documentType, currentLocation, location, created_by, status, occupation, level_of_education) VALUES ('$givenName','$familyName','$otherName','$gender', '$dob', '$martialstatus', '$ubudehe','$birthplace', '$documentNumber', '$birthNationality', '$otherNationality', '$email', '$mobile', '$isibo', '$number_of_rent_house', '$landLord', '$upi', '$date', '$date', '$is_family_heading', '$familyCategory', '$documentType', '$currentLocation', '$location', '$created_by', '$status', '$occupation', '$level_of_education')";

// $query = "INSERT INTO history (citizenId,movementType,isibo,landLord,upi,previousLocation,created_at,created_by) VALUES('$citizenId','$movementType','$isibo','$landLord','$upi','$previousLocation','$date','$created_by')";
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
$connect->close();
return;
