    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $documentNumber = mysqli_real_escape_string($connect, $_POST['documentNumber']);
    $issuedCountry = "";
    if (isset($_POST['issuedCountry'])) {
        $issuedCountry = mysqli_real_escape_string($connect, $_POST['issuedCountry']);
    }
    $sql = "SELECT citizenId FROM citizen WHERE documentNumber = '$documentNumber' LIMIT 1";
    if (isset($issuedCountry) && !empty($issuedCountry)) {
        $sql = "SELECT citizenId FROM citizen WHERE documentNumber = '$documentNumber' AND issuedCountry='$issuedCountry' LIMIT 1";
    }
    $head = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    $head_id = "";
    if (!isset($head[0]["citizenId"])) {
        echo json_encode([]);
        exit(0);
    }
    $head_id = $head[0]["citizenId"];
    $sql = "SELECT citizenId, givenName, familyName, otherName, documentNumber, dob, birthplace, currentLocation, isibo, upi, is_family_heading, familyCategory, gender
                FROM citizen WHERE familyId ='$head_id'";
    $result = $connect->query($sql);
    if ($result->num_rows > 0) {
        $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
        echo json_encode($db_data);
    } else {
        echo json_encode([]);
    }
    $connect->close();
    return;

    ?>