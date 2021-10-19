    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $documentNumber = $_POST['documentNumber'];
    if (getTotal("citizen", " WHERE documentNumber = '$documentNumber' LIMIT 1") == 0) {
        echo json_encode("NOTFOUND");
        exit(0);
    }
    $sql = "SELECT citizenId, CONCAT(givenName,' ',familyName) AS names, otherName,documentNumber, dob, birthplace, currentLocation 
                FROM citizen
                WHERE documentNumber = '$documentNumber'  LIMIT 1";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    echo json_encode($db_data);
    $connect->close();
    return;
    ?>