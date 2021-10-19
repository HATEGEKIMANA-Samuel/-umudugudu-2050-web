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
$movementType = mysqli_real_escape_string($connect, $_POST['movementType']);
$currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$location = mysqli_real_escape_string($connect, $_POST['location']);
$created_by = mysqli_real_escape_string($connect, $_POST['created_by']);
$status = mysqli_real_escape_string($connect, $_POST['status']);
$familyId = mysqli_real_escape_string($connect, $_POST['familyId']);
$gender = ($gender == "GABO" ? "Male" : "Female");
$key_words = $givenName . '' . $familyName . $familyName . '' . $givenName . '' . $otherName . '' . $dob . '' . $birthplace . '' . $documentNumber;
$key_words = strtolower(preg_replace('/\s+/', '', $key_words));
// passport info
$issuedCountry = mysqli_real_escape_string($connect, $_POST['issuedCountry']);
$issuedDate = mysqli_real_escape_string($connect, $_POST['issuedDate']);
$expiryDate = mysqli_real_escape_string($connect, $_POST['expiryDate']);
mysqli_begin_transaction($connect);
try {
  $query = "UPDATE citizen SET issuedCountry='$issuedCountry',expiryDate='$expiryDate',issuedDate='$issuedDate', givenName='$givenName',key_words='$key_words',familyName='$familyName',otherName='$otherName',gender='$gender',dob='$dob',martialstatus='$martialstatus',ubudehe='$ubudehe',birthplace='$birthplace', documentNumber='$documentNumber', birthNationality='$birthNationality', otherNationality='$otherNationality', email='$email', mobile='$mobile', isibo='$isibo', number_of_rent_house='$number_of_rent_house', landLord='$landLord', upi='$upi', created_at='$date', updated_at='$date', is_family_heading='$is_family_heading', familyCategory='$familyCategory', documentType='$documentType', currentLocation='$currentLocation', location='$location', created_by='$created_by', status='$status', occupation='$occupation', level_of_education='$level_of_education' WHERE citizenId = '$familyId'";
  $results = mysqli_query($connect, $query);
  mysqli_query($connect, "UPDATE citizen SET currentLocation='$currentLocation',location='$location',isibo='$isibo',upi='$upi' WHERE familyId='$familyId'");
  $query = "INSERT INTO history (citizenId,movementType,isibo,landLord,upi,previousLocation,created_by,created_at,location) VALUES('$familyId','$movementType','$isibo','$landLord','$upi','$currentLocation','$created_by','$date', '$location')";
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
