    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);

    $db_data = array();
    $sql = "SELECT citizenId, givenName, familyName, otherName, documentNumber,familyCategory
                FROM citizen 
                WHERE familyId = '$citizenId' 
                AND (documentNumber IS NOT NULL AND documentNumber != ' ')";
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