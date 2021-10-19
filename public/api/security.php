<?php
include "./../../web-config/config.php";
include "./../../web-config/database.php";
include_once "./../classes/input.php";
require_once("./../model/umutekano.php");
$action = trim(strtoupper(input::sanitize("action")));
header('Content-Type: application/json');
switch ($action) {
    case 'ADD_REPORT':
        $inzego = input::get("inzego");
        $formData = array(
            "issue_id" => input::sanitize('issueId'),
            "icyabaye_id" => input::sanitize('icyabayeId'),
            "uruhare_gabo" => input::sanitize('uruhareGabo'),
            "uruhare_gore" => input::sanitize('uruhareGore'),
            "abahohotewe_gabo" => input::sanitize('aGabo'),
            "abahohotewe_gore" => input::sanitize('aGore'),
            "location" => input::get('location'),
            "comments" => input::get('comment'),
            "security_date" => date('Y-m-d h:i:s'),
            "user" => input::get('user_id'),
            "village" => input::get('village'),
            "security_org" => $inzego
        );
        $database->beginTransaction();
        try {
            if ($database->create("security", $formData)) {
                $id = $database->inset_id();
                $formData["id"] = $id;
                $id = input::enc_dec('e', $id);
                // push notification
                $loc =  umutekano::getLocationName($database, input::get('location'), '/');
                $database->create(
                    "sec_notification",
                    array(
                        "location" => input::get('location'),
                        "action" => input::sanitize("issueName") . '->'
                            . $loc,
                        'link' => "reports?notify=$id"
                    )
                );
                $nt = $database->inset_id();
                if ($nt) {
                    $database->create(
                        "sec_notification_user",
                        array(
                            "notification_id" => $nt,
                            "user_id" => input::sanitize("user_id")
                        )
                    );
                }
                echo json_encode("Amakuru utanze agiye mu bubiko.");
                $database->commit();
            } else {
                echo json_encode("Amakuru utanze ntabwo agiye mu bubiko.");
            }
        } catch (\Exception $exception) {
            $database->rollBack();
            echo json_encode("Amakuru utanze ntabwo agiye mu bubiko.");
            // $msg = var_dump($exception->getMessage());
            die();
        }
        break;
    case 'GET_CASES':
        $issue = input::sanitize("issueId");
        $village = input::sanitize("village");
        $sql = "SELECT s.*,(SELECT count(*) FROM security_feedback sf WHERE sf.issue_id= s.security_id LIMIT 1) as comment,(SELECT icyabaye.icyabaye_name FROM icyabaye  WHERE icyabaye.icyabaye_id=s.icyabaye_id LIMIT 1) as icyabaye FROM security s WHERE s.village='$village' AND s.issue_id='$issue' ORDER BY s.security_id DESC";
        $db_data = $database->fetch($sql);
        echo json_encode($db_data);
        break;
    case 'REPORT_TOTAL':
        $village = input::sanitize("village");
        $formData = array([
            "urugomo" => umutekano::getTotal($database, "issue_id='1' AND village='$village'"),
            "ubujura" => umutekano::getTotal($database, "issue_id='2' AND village='$village'"),
            "ubwicanyi" => umutekano::getTotal($database, "issue_id='3' AND village='$village'"),
            "ibiza" => umutekano::getTotal($database, "issue_id='4' AND village='$village'"),
            "kwiyahura" => umutekano::getTotal($database, "issue_id='5' AND village='$village'"),
            "ibiyobyabwenge" => umutekano::getTotal($database, "issue_id='6' AND village='$village'"),
            "magendu" => umutekano::getTotal($database, "issue_id='7' AND village='$village'"),
            "amakimbirane" => umutekano::getTotal($database, "issue_id='8' AND village='$village'"),
            "umupaka" => umutekano::getTotal($database, "issue_id='9' AND village='$village'"),
            "igitsina" => umutekano::getTotal($database, "issue_id='10' AND village='$village'"),
            "intwaro" => umutekano::getTotal($database, "issue_id='11' AND village='$village'"),
            "covid" => umutekano::getTotal($database, "issue_id='12' AND village='$village'"),
        ]);
        echo json_encode($formData);
        break;
    case 'ADD_COMMENT':
        $issue = input::enc_dec('d', input::get("issueID"));
        $formData = array(
            "feedback" => input::sanitize("comment"),
            "created_by" => input::sanitize("user_id"),
            "created_at" => date('Y-m-d h:i:s'),
            "issue_id" => input::get("issueID")
        );
        $database->beginTransaction();
        try {
            $data = umutekano::createFeedBack($database, $formData);
            //  create notification
            if ($data == 0) {
                echo json_encode("Igitekerezo nticyakiriwe.");
                exit(0);
            }
            $notify = input::enc_dec("e", $data["last_id"]);
            $action = input::sanitize("issueName");
            $comment_id = $data["last_id"];
            $database->create(
                "sec_notification",
                array(
                    "notification_type" => "FEEDBACK",
                    "location" => input::get("location"),
                    "action" => "umudugudu Haricyo yatangaje : $action",
                    'link' => "report-details?issue=" . $issue . "&notify=$notify#c$comment_id"
                )
            );
            $nt = $database->inset_id();
            if ($nt) {
                $database->create(
                    "sec_notification_user",
                    array(
                        "notification_id" => $nt,
                        "user_id" => input::sanitize("user_id")
                    )
                );
            }
            $database->commit();
            echo json_encode("Igitekerezo cyakiriwe");
            exit(0);
        } catch (\Exception $exception) {
            $database->rollBack();
            $msg = var_dump($exception->getMessage());
            echo json_encode("Igitekerezo nticyakiriwe. " . $msg);
            die();
        }
        break;
    case 'GET_COMMENT':
        $issue = input::sanitize("issueId");
        $feed_total = umutekano::getTotal($database, "  issue_id=$issue", " security_feedback");
        if ($feed_total == 0) {
            echo json_encode("Ntabwo Amakuru Abonetse");
            exit(0);
        }
        $sql = " SELECT feedback,created_at,created_by,id,
        (SELECT CONCAT(lname,' ',fname) as names  FROM user u where u.id=f.created_by) as author FROM security_feedback f WHERE f.issue_id=$issue  ORDER BY f.id desc";
        $db_data = $database->fetch($sql);
        echo json_encode($db_data);
        break;
    case 'GET_ICYABAYE':
        $issue = input::sanitize("issueId");
        $sql = "SELECT icyabaye_id as icyabayeId,icyabaye_name as icyabayeName FROM icyabaye WHERE issue_id=$issue";
        $db_data = $database->fetch($sql);
        echo json_encode($db_data);
        break;
    case 'GET_COUNTRY':
        $sql = "SELECT name FROM countries";
        $db_data = $database->fetch($sql);
        echo json_encode($db_data);
        break;
    default:
        return json_encode("NOTFOUND");
        break;
}
