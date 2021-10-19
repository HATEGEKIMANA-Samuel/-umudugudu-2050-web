<?php
include "config.php";
header('Content-Type: application/json');
$village = mysqli_real_escape_string($connect, $_POST['currentLocation']);
$rent = getTotal("citizen", "WHERE currentLocation='$village' AND is_family_heading ='1' AND landLord IS NOT NULL");
$family = getTotal("citizen", "WHERE currentLocation='$village'  AND is_family_heading ='1'");
$formData = array([
    "citizen" => getTotal("citizen", "WHERE currentLocation='$village'"),
    "family" => $family,
    "visitor" => getTotal("visitors", " WHERE village='$village' AND departure_date >= CURDATE() AND status='1'"),
    "unkown" => 0,
    "norent" => ($family - $rent),
    "rent" => $rent,
]);
echo json_encode($formData);
