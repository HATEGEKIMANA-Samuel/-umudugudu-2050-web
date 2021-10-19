    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);

    $db_data = array();
    $sql = "SELECT citizenId, CONCAT(givenName,' ',familyName, ' ',otherName) AS names, documentNumber, dob, birthplace, currentLocation, isibo, upi, is_family_heading
                FROM anonymous_citizen WHERE currentLocation = '$currentLocation'";
    $result = $connect->query($sql);
    if ($result->num_rows > 0) {
        $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
        echo json_encode($db_data);
    } else {
        echo json_encode("NOTFOUND");
    }
    $connect->close();
    return;
    ?>