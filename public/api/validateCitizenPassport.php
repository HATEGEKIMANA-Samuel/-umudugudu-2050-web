    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $documentNumber = $_POST['documentNumber'];
    $country = $_POST['issuedCountry'];
    if (getTotal("citizen", " WHERE documentNumber = '$documentNumber' AND issuedCountry='$country' LIMIT 1") == 0) {
        echo json_encode([]);
        exit(0);
    }
    $sql = "SELECT citizenId, CONCAT(givenName,' ',familyName) AS names,location,otherName,documentNumber, dob, birthplace, currentLocation,issuedCountry,
            CASE
            WHEN (SELECT COUNT(familyId) FROM citizen WHERE familyId = (SELECT citizenId FROM citizen WHERE documentNumber = '$documentNumber' AND issuedCountry='$country' ORDER BY citizenId DESC LIMIT 1)) = 0 THEN '0'
            ELSE '1'
            END as dependent 
            FROM citizen
            WHERE documentNumber = '$documentNumber' AND issuedCountry='$country' ORDER BY citizenId DESC LIMIT 1";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    $hash = $db_data[0]["location"];
    $db_data[0]["locationNames"] = getLocationName($hash);
    echo json_encode($db_data);
    $connect->close();
    ?>