    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $documentNumber = $_POST['documentNumber'];
    $db_data = array();
    $sql = "SELECT documentNumber, dob, gender, birthplace, martialstatus, email, mobile, isibo, upi
            FROM citizen WHERE documentNumber = '$documentNumber' 
            ORDER BY citizenId DESC LIMIT 1";
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