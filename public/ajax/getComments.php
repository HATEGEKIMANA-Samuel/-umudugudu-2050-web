<?php
require_once '../autoload.php';
if (input::required(array("owner", "type")) && input::get("action") == "loaddata") {
    // citizen_id
    $owner = input::sanitize("owner");
    // kid or head of family
    $type = input::sanitize("type");
    $comment_query = $database->query("SELECT * FROM comments WHERE owner ='$owner' AND status='1' AND owner_type='$type' ORDER BY time ASC ");
    while ($comments  = $database->fetch_array($comment_query)) {
        echo "<div class='comment p-15' style='background-color: #fafafa' >";
        $comment = nl2br($comments['comment']);
        $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$comments['user']}' LIMIT 1 "));
        echo "<p class='comment_u_t'><b>{$user['fname']} {$user['lname']}</b> <b style='float : right; font-size: 11px'>{$comments['time']}</b><hr> <p>";
        echo "<p class='comment_data mb-0 fs-14'>$comment </p>";
        echo "</div>";
    }
} elseif (input::required(array("action")) && input::get("action") == "savecomment") {
    $comment = input::sanitize('comment_field');
    $owner = input::sanitize("owner");
    $type = input::sanitize("type");
    $sql = "INSERT INTO comments(owner,comment,owner_type,time,user) VALUES('$owner','$comment','$type',NOW(),{$_SESSION["id"]})";
    $database->query($sql);
    //$dpl = rawurlencode(encrypt_decrypt('encrypt', $owner));
    echo json_encode([
        "status" => $database->affected_rows(),
        "id" => $owner,
        "comment" => nl2br(
            $comment
        ),
        "time" => date("Y-m-dh:i:s"),
        "user" => input::sanitize("user")
    ]);
}
