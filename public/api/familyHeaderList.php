    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
    if (getTotal("citizen", "WHERE currentLocation = '$currentLocation' AND is_family_heading ='1'") == 0) {
        echo json_encode([]);
        exit(0);
    }
    $sql = "SELECT issuedCountry,issuedDate,expiryDate,documentType,citizenId, givenName, familyName, otherName, documentNumber, dob, birthplace, currentLocation, isibo, upi, is_family_heading, familyCategory, gender
                FROM citizen WHERE currentLocation = '$currentLocation' AND is_family_heading ='1'";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    echo json_encode($db_data);
    $connect->close();
    ?>