<?php
require_once "../autoload.php";
require_once "../model/umutekano.php";
require_once "../model/user.php";
$opera = input::sanitize("action");
$errors = array();
switch (strtolower($opera)) {
    case 'make_feedback':
        $issue = input::enc_dec('d', input::get("issue"));
        $formData = array(
            "feedback" => input::sanitize("comment"),
            "created_by" => session::get("id"),
            "created_at" => date('Y-m-d h:i:s'),
            "issue_id" => $issue
        );
        $database->beginTransaction();
        try {
            $data = umutekano::createFeedBack($database, $formData);
            //  create notification
            if ($data == 0) {
                echo json_encode(["data" => $data, "status" => "fail"]);
                exit(0);
            }
            $notify = input::enc_dec("e", $data["last_id"]);
            $level_name = getLevelName(session::get("level"));
            $action = input::sanitize("action_name");
            $comment_id = $data["last_id"];
            $database->create(
                "sec_notification",
                array(
                    "notification_type" => "FEEDBACK",
                    "location" => input::get("location"),
                    "action" => $level_name . " Haricyo yatangaje : $action",
                    'link' => "report-details?issue=" . input::get("issue") . "&notify=$notify#c$comment_id"
                )
            );
            $nt = $database->inset_id();
            if ($nt) {
                $database->create(
                    "sec_notification_user",
                    array(
                        "notification_id" => $nt,
                        "user_id" => session::get("id")
                    )
                );
            }
            // get user names
            $database->commit();
            echo json_encode(["data" => $data, "status" => "success"]);
            exit(0);
        } catch (\Exception $exception) {
            $database->rollBack();
            $msg = var_dump($exception->getMessage());
            echo json_encode(["data" => $msg, "status" => "fail"]);
            die();
        }
        break;
    default:
        break;
}
