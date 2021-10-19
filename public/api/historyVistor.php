    <?php
    // database confuguration
    include "config.php";
    header('Content-Type: application/json');
    $citizenId = mysqli_real_escape_string($connect, $_POST['citizenId']);
    if (getTotal("history", " WHERE citizenId = '$citizenId'") == 0) {
        echo json_encode([]);
        exit(0);
    }
    $sql = "SELECT historyId, movementType, created_at
                FROM history WHERE citizenId = '$citizenId'";
    $db_data = mysqli_fetch_all(mysqli_query($connect, $sql), MYSQLI_ASSOC);
    echo json_encode($db_data);
    $connect->close();
    return;
    ?>