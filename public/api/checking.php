<?php
include "./../../web-config/config.php";
include "./../../web-config/database.php";
include_once "./../classes/input.php";
include_once './../includes/functions.php';
header('Content-Type: application/json');
function getLocationName($db, $hashedLocation = "0#0#0#0#0#")
{
    $arr = explode("#", $hashedLocation);
    $query = "SELECT name,level FROM(
                    SELECT province as name,'p' as level FROM provinces WHERE id=$arr[0]
                    UNION all
                    SELECT district as name ,'d' as level FROM districts WHERE id=$arr[1]
                    UNION all
                    SELECT sector as name,'s' as level FROM sectors WHERE id=$arr[2]
                    UNION all
                    SELECT name,'c' as level FROM cell WHERE id=$arr[3]
                    UNION all
                    SELECT name,'v' as level FROM village WHERE id=$arr[4]
                    ) as data";
    $locs = ["province" => 0, "district" => 0, "sector" => 0, "cell" => 0, "village" => 0];
    $resp = $db->query($query);
    while ($row = $db->fetch_array($resp)) {
        if ($row["level"] == "p") {
            $locs["province"] = $row["name"];
        } else if ($row["level"] == "d") {
            $locs["district"] = $row["name"];
        } else if ($row["level"] == "s") {
            $locs["sector"] = $row["name"];
        } elseif ($row["level"] == "c") {
            $locs["cell"] = $row["name"];
        } elseif ($row["level"] == "v") {
            $locs["village"] = $row["name"];
        }
    }
    return $locs;
}
if (input::required(array('username', 'password'))) {
    $uname = input::sanitize("username");
    $user_password = input::sanitize("password");
    $sql = "SELECT * FROM user where username='$uname' AND level='2' limit 1";
    $data = $database->fetch_array($database->query($sql));
    if (!isset($data)) {
        echo json_encode([]);
        exit(0);
    }
    // check login
    if (!verify_Password($data["password"], $user_password)) {
        echo json_encode([]);
        exit(0);
    }
    $hash = $data["location"];
    $location = getLocationName($database, $hash);
    echo json_encode(array([
        "locationCode" => $hash,
        "province" => $location["province"],
        "district" => $location["district"],
        "sector" => $location["sector"],
        "cell" => $location["cell"],
        "village" => $location["village"],
        "username" => $data['username'],
        "user_id" => $data["id"],
        "villageCode" => $data["village"],
        "names" => $data["fname"] . ' ' . $data['lname']
    ]));
} else {
    echo json_encode([]);
    exit(0);
}
