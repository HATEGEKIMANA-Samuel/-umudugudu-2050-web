    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);
    $citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);
    if (getTotal("visitors", " WHERE village = '$currentLocation' AND resident_id = '$citizenId'") == 0) {
        echo json_encode([]);
        exit(0);
    }
    $sql = "SELECT  c.citizenId, c.givenName, c.familyName, c.otherName, c.documentNumber, c.dob, c.birthplace, c.currentLocation, c.gender, r.givenName AS rgivenName, r.familyName AS rfamilyName, r.otherName AS rotherName, r.documentNumber As rdoc, v.created_at, v.arrival_date, v.departure_date
            FROM citizen c, visitors v, citizen r WHERE c.citizenId = v.visited_by AND village = '$currentLocation' AND resident_id ='$citizenId' AND r.citizenId = v.resident_id AND departure_date >= CURDATE()";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    echo json_encode($db_data);
    $connect->close();
    exit(0);
    ?>