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
$upi = mysqli_real_escape_string($connect, $_POST['upi']);
$documentNumber = mysqli_real_escape_string($connect, $_POST['documentNumber']);
$is_family_heading = mysqli_real_escape_string($connect, $_POST['is_family_heading']);
$familyCategory = mysqli_real_escape_string($connect, $_POST['familyCategory']);
$familyId = mysqli_real_escape_string($connect, $_POST['familyId']);
$documentType = mysqli_real_escape_string($connect, $_POST['documentType']);
$movementType = mysqli_real_escape_string($connect, $_POST['movementType']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$status = mysqli_real_escape_string($connect, $_POST['status']);
$key_words = $givenName . '' . $familyName . $familyName . '' . $givenName . '' . $otherName . '' . $dob . '' . $birthplace . '' . $documentNumber;
$key_words = strtolower(preg_replace('/\s+/', '', $key_words));
$gender = ($gender == "GABO" ? "Male" : "Female");
// passport info
$issuedCountry = mysqli_real_escape_string($connect, $_POST['issuedCountry']);
$issuedDate = mysqli_real_escape_string($connect, $_POST['issuedDate']);
$expiryDate = mysqli_real_escape_string($connect, $_POST['expiryDate']);
mysqli_begin_transaction($connect);
try {
  $query = "INSERT INTO citizen (givenName,familyName,otherName,gender,dob,martialstatus,ubudehe,birthplace, documentNumber, 
  birthNationality, otherNationality, email, mobile, isibo, upi, created_at, updated_at, is_family_heading, familyCategory, 
  documentType, currentLocation, location, created_by, status, occupation, level_of_education,familyId,key_words,
  issuedCountry,issuedDate,expiryDate) VALUES ('$givenName','$familyName','$otherName','$gender', '$dob', '$martialstatus', '$ubudehe','$birthplace', '$documentNumber', '$birthNationality', '$otherNationality', '$email', '$mobile', '$isibo', '$upi', '$date', '$date', '$is_family_heading', '$familyCategory', '$documentType', '$currentLocation', '$location', '$created_by', '$status', '$occupation', '$level_of_education','$familyId','$key_words','$issuedCountry','$issuedDate','$expiryDate')";
  $results = mysqli_query($connect, $query);
  $citizen_id = mysqli_insert_id($connect);
  $query = "INSERT INTO history (citizenId,movementType,isibo,upi,previousLocation,created_by,created_at,location) VALUES(
  '$citizen_id','$movementType','$isibo','$upi','$currentLocation','$created_by','$date', '$location')";

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
