
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
$gender = ($gender == "GABO" ? "Male" : "Female");
$key_words = $givenName . '' . $familyName . $familyName . '' . $givenName . '' . $otherName . '' . $dob . '' . $birthplace . '' . $documentNumber;
$key_words = strtolower(preg_replace('/\s+/', '', $key_words));
$mid = "";
// passport info
$issuedCountry = mysqli_real_escape_string($connect, $_POST['issuedCountry']);
$issuedDate = mysqli_real_escape_string($connect, $_POST['issuedDate']);
$expiryDate = mysqli_real_escape_string($connect, $_POST['expiryDate']);
$query = "INSERT INTO citizen (givenName,familyName,otherName,gender,dob,martialstatus,ubudehe,birthplace, documentNumber, birthNationality, otherNationality, email, mobile, isibo, number_of_rent_house, landLord, upi, created_at, updated_at, is_family_heading, familyCategory, documentType, currentLocation, location, created_by, status, occupation, level_of_education,key_words,issuedCountry,issuedDate,expiryDate) VALUES ('$givenName','$familyName','$otherName','$gender', '$dob', '$martialstatus', '$ubudehe','$birthplace', '$documentNumber', '$birthNationality', '$otherNationality', '$email', '$mobile', '$isibo', '$number_of_rent_house', '$landLord', '$upi', '$date', '$date', '$is_family_heading', '$familyCategory', '$documentType', '$currentLocation', '$location', '$created_by', '$status', '$occupation', '$level_of_education','$key_words','$issuedCountry','$issuedDate','$expiryDate')";
mysqli_begin_transaction($connect);
try {
  $results = mysqli_query($connect, $query);
  $citizen_id = mysqli_insert_id($connect);
  $historyData = array(
    "citizenId" => $citizen_id,
    "movementType" => $movementType,
    "isibo" => $isibo,
    "landLord" => $landLord,
    "created_by" => $created_by,
    "upi" => $upi,
    "location" => $location,
    "previousLocation" => $currentLocation,
    "created_at" => date('Y-m-d h:i:s'),
  );
  $keys = array_keys($historyData);
  $sql = "INSERT INTO history (`" . implode('`,`', $keys) . "`) VALUES 
       ('" . implode('\',\'', $historyData) . "')";
  if (mysqli_query($connect, $sql)) {
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
  echo  json_encode("Amakuru utanze ntabwo agiye mu bubiko." . $exception->getMessage());
  $connect->close();
  // var_dump($exception->getMessage());
  die();
}
