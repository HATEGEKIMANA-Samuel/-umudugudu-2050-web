    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
    $citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);
    $sql = "SELECT issuedCountry,issuedDate,expiryDate,documentType,citizenId, givenName, familyName, otherName, gender, dob, martialstatus, birthplace, documentNumber, is_family_heading, familyCategory, currentLocation, birthNationality, otherNationality, email, mobile, ubudehe, isibo, occupation, 
        level_of_education, upi,familyId  FROM citizen WHERE currentLocation = '$currentLocation' AND (familyId = '$citizenId' OR citizenId='$citizenId') ORDER BY is_family_heading DESC";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    echo json_encode($db_data);
    $connect->close();
    return;

    ?>