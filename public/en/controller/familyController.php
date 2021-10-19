<?php
require_once "../autoload.php";
require_once "../model/family.php";
require_once "../model/user.php";
function check($items = array(), $lang = array())
{
    $errors = array();
    foreach ($items as $key => $value) {
        if (empty(trim($value))) {
            array_push($errors, $lang[$key]);
        }
    }
    return $errors;
}
// display search resultset
function displayPopulation($d = array())
{
    $tr = "";
    $i = 1;
    foreach ($d as $key => $data) {
        $table = $data["tb"] == "m" ? "kids?kd=" : "family?dpl=";
        $href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $data['id']));
        $tr .= "<tr><td scope='row' class='numbering'>$i</td>
                                    <td>{$data['given_name']}  {$data['family_name']} 
                                   {$data['other_name']} {$data['dob']}</td>
                                    <td>{$data['document_id']}</td>
                                    <?php
                                   
                                    ?>
                                    <td><a style href='{$href}'>
                                            <button type='button' class='btn btn-default btn-sm'>
                                                <i class='ti-user'></i> More
                                            </button>
                                        </a>
                                    </td></tr>";
        $i++;
    }
    return $tr;
}
function chooseInfoToDisplay($heads, $members)
{
    $ht = count($heads) == 0 ? true : false;
    $mt = count($members) == 0 ? true : false;
    if (!$ht && !$mt) {
        $mdate = $heads[0]["time"];
        $hdate = $heads[0]["time"];
        if ($mdate >= $hdate) return $heads;
        return $members;
    } elseif (!$ht && $mt) {
        return $heads;
    } elseif ($ht && !$mt) {
        return $members;
    } elseif ($ht && $mt) {
        return $heads;
    }
}
$opera = input::sanitize("action");
$errors = array();
switch (strtolower($opera)) {
    case 'updatetotal':
        echo json_encode(["numbers" => 0]);
        break;
    case 'transfermember':
        $migrant = json_decode(input::get("migrantInfo"), true);
        // add movement to migrant
        $names = $migrant["family_name"] . ' ' . $migrant['other_name'] . ' ' . $migrant["given_name"];
        $tracking = array(
            "names" => $names,
            "document_id" => $migrant["document_id"],
            "pre_location" => $migrant["location"],
            "current_location" => input::get("user_loc"),
            "user_id" => session::get("id")
        );
        $lastId = family::addMigration($database, $tracking);
        if ($lastId > 0) {
            // update location on memeber and diplomats
            // add Person 
            echo json_encode([
                "data" => $migrant,
                "error" => "none", "viewError" => '', "view" => 'transferMigrant',
                "id" => $lastId
            ]);
            exit(0);
        }
        // rawurlencode(input::enc_dec("e", $head)) . rawurlencode("%help")
        echo json_encode([
            "msg" => "$names\'s transfer has not been moved and try again",
            "error" => "exist", "viewError" => '', "view" => 'transferMigrant',
            "id" => 0
        ]);
        //echo json_encode(["msg" => 'Transfer Member is under contruction', "error" => "exist"]);
        break;
    case 'checkmember':
        $type = input::sanitize("doctype");
        $uloc = input::get("user_loc");
        $rwandan_id = trim(input::sanitize('rwandan_id'));
        if ($type == 'PASSPORT') {
            $passport = input::sanitize('passport');
            $issued_country = input::sanitize('issued_country');
            $errors = check(array($passport, $issued_country), array('pasport', 'issued country'));
            if (!empty($errors)) {
                echo json_encode([
                    "data" => '', "error" => "none",
                    "view" => 'checkMember', "viewError" => $errors
                ]);
                exit(0);
            }
            $cond = "document_id='$passport'  AND issued_country=$issued_country ORDER BY id DESC limit 1";
            $headInfo = family::checkHeadOfFamily($database, $cond);
            // check in member of family
            $cond = "members.document_id='$passport'  AND members.issued_country=$issued_country ORDER BY members.id DESC limit 1";
            $memberInfo = family::checkMemberInFamily($database, $cond);
            // current_location
            $cl = '';
            $Info = chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($Info) > 0) {
                //$hloc = explode('#', );
                $cl = user::decodeLocation($database, $Info[0]['location']);
                $uloc = user::decodeLocation($database, $uloc);
            }
            echo json_encode([
                "data" => $Info, "error" => "none", "viewError" => '', "view" => 'checkMember',
                "location" => $cl, "user_loc" => $uloc
            ]);
            exit(0);
        }
        if ($type == 'NONE') {
            // check member if exist or not without passport and ID
            echo json_encode([
                "data" => $_REQUEST, "error" => "none", "viewError" => '', "view" => 'checkMember'
            ]);
            exit(0);
        }
        if (!empty($rwandan_id) && strlen($rwandan_id) == 16) {
            // check in head of family
            $cond = "document_id='$rwandan_id' ORDER BY id limit 1";
            $headInfo = family::checkHeadOfFamily($database, $cond);
            // check in member of family
            $cond = "members.document_id='$rwandan_id' ORDER BY id limit 1";
            $memberInfo = family::checkMemberInFamily($database, $cond);
            // current_location
            $cl = '';
            $Info = chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($Info) > 0) {
                //$hloc = explode('#', );
                $cl = user::decodeLocation($database, $Info[0]['location']);
                $uloc = user::decodeLocation($database, $uloc);
            }
            echo json_encode([
                "data" => $Info, "error" => "none", "viewError" => '', "view" => 'checkMember',
                "location" => $cl, "user_loc" => $uloc
            ]);
            exit(0);
        }
        echo json_encode(["error" => "none", "viewError" => ['Bad identification' . '(' . $rwandan_id . ')']]);
        break;
    case 'head_of_family':
        $uloc = input::get("user_loc");
        $given_name = input::sanitize("given_name");
        $family_name = input::sanitize('family_name');
        $other_name = input::sanitize('other_name');
        $gender = input::sanitize('gender');
        $dob = input::get('dob');
        $marital_status = input::sanitize("table") == "diplomats" ? input::sanitize('marital_status') : 'Single';
        $birth_place = input::sanitize('birth_place');
        $birth_nationality = input::sanitize('birth_nationality');
        $other_nationality = input::sanitize('other_nationality');
        $passport = input::sanitize('passport');
        $issued_country = input::sanitize('issued_country');
        $members = input::sanitize('members');
        $issued_country = empty($issued_country) ? 0 : $issued_country;
        $issued_date = input::get('issued_date');
        $expiry_date = input::get('expiry_date');
        $rwandan_id = input::sanitize('rwandan_id');
        $type = input::get("doctype");
        $passporttocheck = $type == "ID" ? $rwandan_id : $passport;
        $email = input::sanitize('email');
        $phone = input::sanitize('phone');
        $ubudehe = input::sanitize('ubudehe');
        $isibo = input::sanitize("isibo");
        $rent_house = input::get("rent_house");
        $occupation = input::sanitize("occupation");
        $level_education = input::get("level_education");
        $number_house = input::sanitize("number_house");
        $house_info = input::sanitize("house_info");
        $errors = check(
            array(
                $given_name, $family_name, $gender, $dob, $marital_status,
                $birth_place, $birth_nationality, $members, $ubudehe, $isibo,
                $rent_house, $occupation, $level_education
            ),
            array(
                'Given name', 'Family Name', 'Gender', 'Date of birth',
                'Status', 'Place of birth', 'Nationality', 'Number of family members',
                'Ubudehe', 'isibo', 'Is he/she renting?', 'the profession he/she works for', 'Level of education'
            )
        );
        if ($rent_house == 'hoya') {
            empty($number_house) ? array_push($errors, 'number of houses') : '';
        } else if ($rent_house == "yego") {
            empty($house_info) ? array_push($errors, 'Home Information') : '';
        }
        // check if Document type is ID
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }

        $village = explode("#", $uloc);
        $village = output::print(4, $village, 0);
        $formData = array(
            "given_name" => $given_name, "family_name" => $family_name, "other_name" => $other_name,
            "gender" => $gender, "dob" => $dob, "marital_status" => $marital_status, "birth_place" => $birth_place,
            "birth_nationality" => $birth_nationality, "other_nationality" => $other_nationality,
            "email" => $email, "phone" => $phone,
            "document_id" => $passporttocheck, "issued_country" => $issued_country,
            "issued_date" => $issued_date, "expiry_date" => $expiry_date,
            "time" => date('Y-m-d h:i:s'), "user" => session::get("id"),
            "location" => $uloc,
            "village" => $village,
            "type" => $type,
            "members" => $members,
            "ubudehe" => $ubudehe,
            "isibo" => $isibo,
            "rent_house" => $rent_house,
            "occupation" => $occupation,
            "number_house" => $number_house,
            "house_info" => $house_info,
            "level_education" => $level_education,
            "status" => "1"
        );
        // if it is edit or insert
        $isAdded = 0;
        // check if it is transfer
        $isAdded = 0;
        // check if it is transfer
        if (input::sanitize("transfer") == "yes") {
            $mid = input::sanitize("tid");
            if (input::sanitize("table") == "diplomats") {
                // found in head of family
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "diplomats");
            } else {
                // exist in members;
                $isAdded = family::editMemberInFamily($database, $mid, array("status" => "0"), "members");
                $isAdded = family::addHeadOfFamily($database, $formData);
                $mid = $isAdded;
            }
        } else {
            $mid = output::print("id_to_edit", $_REQUEST, "");
            if (empty($mid)) {
                $isAdded = family::addHeadOfFamily($database, $formData);
                $mid = $isAdded;
            } else {
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "diplomats");
            }
        }
        echo json_encode([
            "data" => input::enc_dec("e", $mid), "error" => "none", "viewError" => '', "view" => 'addHeadOfFamily',
            "id" => $isAdded
        ]);
        break;
    case 'check_unknown_family_member':
        echo json_encode(["msg" => ' to check Unknown Member is under contruction', "error" => "exist"]);
        break;
    case 'member_to_family':
        $pid = input::get("pid");
        $given_name = input::sanitize("given_name");
        $family_name = input::sanitize('family_name');
        $other_name = input::sanitize('other_name');
        $gender = input::sanitize('gender');
        $dob = input::get('dob');
        $birth_place = input::sanitize('birth_place');
        $birth_nationality = input::sanitize('birth_nationality');
        $other_nationality = input::sanitize('other_nationality');
        $passport = input::sanitize('passport');
        $issued_country = input::sanitize('issued_country');
        $members = input::sanitize('members');
        $issued_country = empty($issued_country) ? 0 : $issued_country;
        $issued_date = input::get('issued_date');
        $expiry_date = input::get('expiry_date');
        $rwandan_id = input::sanitize('rwandan_id');
        $type = input::get("doctype");
        $passporttocheck = $type == "ID" ? $rwandan_id : $passport;
        $email = input::sanitize('email');
        $phone = input::sanitize('phone');
        $visitor_info = '';
        $occupation = input::sanitize("occupation");
        $level_education = input::sanitize("level_education");
        $errors = check(
            array($given_name, $family_name, $gender, $dob, $birth_place, $birth_nationality, $occupation, $level_education),
            array(
                'Given name', 'Family name', 'Gender', 'Date of birth', 'Place of birth',
                'Nationality', 'The profession he/she  works for ', 'level of education '
            )
        );

        // check visitor
        $relationship = input::sanitize("relationship");
        if ($relationship == "Visitor") {
            // check arrival,check place,check departure date
            $ad = input::get("arrival_date");
            $dd = input::get("departure_date");
            $from = input::get("come_from");
            $pname = input::get("place_name");
            $errorr2 = check(
                array($ad, $pname, $dd),
                array('Arrival date', 'Name of origin', 'Date of departure')
            );
            $errors = array_merge($errors, $errorr2);
            $visitor_info = $ad . "#" . $from . '#' . $pname . "#" . $dd;
        }
        // check if Document type is ID
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }
        // store information about member who did not take any identification
        if (input::sanitize("doctype") == "NONE") {
            $passporttocheck = "T-" . date('Ymdhis') . '-' . input::get("pid");
        }
        $formData = array(
            "head" => input::get("pid"),
            "given_name" => $given_name, "family_name" => $family_name, "other_name" => $other_name,
            "gender" => $gender, "dob" => $dob, "birth_place" => $birth_place,
            "birth_nationality" => $birth_nationality, "other_nationality" => $other_nationality,
            "email" => $email, "phone" => $phone,
            "document_id" => $passporttocheck, "issued_country" => $issued_country,
            "issued_date" => $issued_date, "expiry_date" => $expiry_date,
            "time" => date('Y-m-d h:i:s'), "user" => session::get("id"),
            "relationship" => $relationship,
            "what_relationship" => input::sanitize("what_relationship"),
            "type" => $type,
            "visitor_info" => $visitor_info,
            "level_education" => $level_education,
            "occupation" => $occupation
        );
        $isAdded = 0;
        // check if it is transfer
        // check if it is transfer
        $mid = "";
        if (input::sanitize("transfer") == "yes") {
            $mid = input::sanitize("tid");
            if (input::sanitize("table") == "diplomats") {
                // found in head of family
                $isAdded = family::editMemberInFamily(
                    $database,
                    $mid,
                    array("status" => "0"),
                    "diplomats"
                );
                // add to members
                $isAdded = family::addMemberInFamily($database, $formData);
            } else {
                // exist in members; 
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "members");
            }
        } else {
            $mid = output::print("id_to_edit", $_REQUEST, "");
            if (empty($mid)) {
                $isAdded = family::addMemberInFamily($database, $formData);
                family::editNumberOfFamily($database, input::get("pid"), array("members" => $members + 1));
            } else {
                $isAdded = family::editMemberInFamily($database, $mid, $formData);
            }
        }
        if ($isAdded == 0) {
            echo json_encode(["msg" => "Writing $given_name  in the family refused,try again", "viewError" => '', "error" => "exist"]);
            exit(0);
        }
        echo json_encode([
            "data" =>  rawurlencode(input::enc_dec("e", $pid)) . rawurlencode("%members"), "error" => "none", "viewError" => '', "view" => 'addMemberInFamily',
            "id" => $isAdded
        ]);
        break;
    case 'help':
        $head = input::sanitize("diplomat");
        $giver = input::get("giver");
        $help = input::sanitize("what_help");
        $count_help = input::sanitize("count");
        $comment = input::sanitize("comment");
        $other_giver = input::sanitize("other_sponsor");
        $errors = check(array($giver, $help), array("Sponsor", "the help he/she receives"));
        if ($giver == "ikindi" && empty(trim($other_giver))) {
            $errors = array_merge($errors, ["Name of donor"]);
        }
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }
        $formData = array(
            "family" => $head, "help" => $help, "giver" => $giver,
            "time" => date('Y-m-d h:i:s'), "user" => session::get("id"),
            "other_giver" => $other_giver, "count_help" => $count_help, "comment" => $comment
        );
        // check if it is edit
        if (empty(output::print("id_to_edit", $_POST, ""))) {
            $lastid = family::addSupportOnFamily($database, $formData);
        } else {
            $lastid = family::editMemberInFamily($database, input::get("id_to_edit"), $formData, "help");
        }
        if ($lastid != 0) {
            echo json_encode([
                "data" =>  rawurlencode(input::enc_dec("e", $head)) . rawurlencode("%help"), "error" => "none", "viewError" => '', "view" => 'helpInFamily',
                "id" => $lastid
            ]);
            exit(0);
        }
        echo json_encode(["msg" => "No written help and try again", "viewError" => '', "error" => "exist"]);
        break;
    case "get_movements":
        // get movement made by head of family or member of family
        $passport = input::sanitize('passport');
        $type = input::sanitize("doctype");
        $rwandan_id = trim(input::sanitize('rwandan_id'));
        $doc_id = $type == 'PASSPORT' ? $passport : $rwandan_id;
        if (empty($doc_id)) {
            echo json_encode([
                "msg" => "Documents are still needed", "error" => "exist", "viewError" => '', "view" => ''
            ]);
            exit(0);
        }
        $movements = family::getAllMovement($database, "document_id='$doc_id'");
        echo json_encode([
            "data" => $movements, "error" => "none", "viewError" => '', "view" => 'viewMovements'
        ]);
        break;
    case "get_location_by_name":
        $locations = user::getLocationName($database, input::get("location_code"));
        echo json_encode([
            "data" => $locations, "error" => "none", "viewError" => '',
            "view" => "viewLocationFromCode"
        ]);
        break;
    case 'find_people':
        $search = input::sanitize("search");
        $table = input::get("table");
        $loc = input::get("location");
        // find in diplomant and members
        $cond = ["m" => "(concat(m.family_name,m.given_name,m.document_id)) LIKE '%$search%'", "d" => "(concat(d.family_name,d.given_name,d.document_id)) LIKE '%$search%' AND 1"];
        $result = family::find($database, $cond, $loc);
        echo json_encode([
            "data" => displayPopulation($result), "error" => "none", "viewError" => '',
            "view" => 'viewSearchResult', "count" => count($result)
        ]);
        break;
    default:
        echo json_encode([
            "msg" => $opera . ' not found',
            "error" => "exist", "viewError" => '', 'request' => $_REQUEST
        ]);
        break;
}
