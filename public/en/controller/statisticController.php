<?php
require_once "../autoload.php";
require_once "../model/statistic.php";
$action = input::sanitize("action");

switch (strtolower($action)) {
    case "view_statistic_numbering":
        if (session::exists("CL")) {
            $location = session::get("CL")["numeric"] . "#";
        } else {
            $level = session::get("level");
            if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
            $location = "#";
        }
        $view = input::get("view");
        $search = input::get("counting");
        $date = '1';
        $mcolmn = '1';
        if ($view == "range") {
            if (!input::required(array("start_year"))) {
                echo json_encode(["msg" => "Start year is required", "error" => "exist", "viewError" => '']);
                exit(0);
            }
            $start_year = (int) date('Y') - (int) input::get("start_year");
            $end_year = (int) date('Y') - (int) input::get("end_year");
            $date = ($end_year <= $start_year) ? "BETWEEN '$end_year-01-01' AND '$start_year-12-12'" : " BETWEEN '$start_year-01-01' AND '$end_year-12-12'";
        }
        if ($date != '1') {
            $mcolmn = " m.dob " . $date;
            $date = " dob " . $date;
        }
        if ($search == "help") {
            $table = "help";
        } else if ($search == "education") {
            $edu = trim(input::sanitize("level_education"));
            $date .= " AND level_education='$edu'";
            $mcolmn .= " AND m.level_education='$edu'";
        }
        echo json_encode(["data" => Statistic::getSampleStatisticInfo($database, ["d" => $date, "m" => $mcolmn], $location), "error" => "none", "viewError" => '', "view" => "viewStatistic"]);
        //echo json_encode(["msg" => print_r($nd) . ' not found' . $date, "error" => "exist", "viewError" => '']);
        exit(0);
        break;
    default:
        echo json_encode(["msg" => $action . ' not found', "error" => "exist", "viewError" => '']);
        exit(0);
        break;
}
