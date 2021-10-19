    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $currentLocation = mysqli_real_escape_string($connect, $_POST['currentLocation']);

    $db_data = array();
    $sql = "SELECT c.citizenId, c.givenName, c.familyName, c.otherName, 
        c.documentNumber, c.dob, c.birthplace, c.currentLocation, c.gender, r.givenName AS rgivenName,
         r.familyName AS rfamilyName, r.otherName AS rotherName, r.documentNumber As rdoc, 
         v.created_at, v.arrival_date, v.departure_date
            FROM citizen c, visitors v, citizen r WHERE c.citizenId = v.visited_by AND village = '$currentLocation' AND r.citizenId = v.resident_id AND departure_date >= CURDATE() AND v.status='1'";
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