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
// display search resultset
function displayPopulation($d = array(), $keyword = "")
{
    $tr = "";
    $i = 1;
    foreach ($d as $key => $data) {
        $table = $data["tb"] == 0 ? "kids?kd=" : "family?dpl=";
        $data['givenName'] = preg_replace(
            '/(' . $keyword . ')/i',
            "<b class='text-primary fs--16'>$1</b>",
            $data['givenName']
        );
        $data['documentNumber']
            = preg_replace(
                '/(' . $keyword . ')/i',
                "<b class='text-primary fs--16'>$1</b>",
                $data['documentNumber']
            );
        $data['familyName']
            = preg_replace(
                '/(' . $keyword . ')/i',
                "<b class='text-primary fs--16'>$1</b>",
                $data['familyName']
            );

        $other = isset($data['otherName']) ? $data['otherName'] : ' ';
        $href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $data['id']));
        $tr .= "<tr><td scope='row' class='numbering'>$i</td>
                                    <td>{$data['givenName']}  {$data['familyName']} 
                                   {$other} {$data['dob']}</td>
                                    <td>{$data['documentNumber']}</td>
                                    <?php
                                   
                                    ?>
                                    <td><a style href='{$href}'>
                                            <button type='button' class='btn btn-default btn-sm'>
                                                <i class='ti-user'></i> Byinshi
                                            </button>
                                        </a>
                                    </td></tr>";
        $i++;
    }
    return $tr;
}
$opera = input::sanitize("action");
$errors = array();

switch (strtolower($opera)) {
    case 'updatetotal':
        $loc = input::get("office");
        $columnLocation = "location LIKE '$loc%'";
        if ($loc == 0) {
            $columnLocation = "1";
        }
        $loc = rtrim($loc, "#");
        $malePeople = family::getTotalPeople($database, $loc, "male");
        $femalePeople = family::getTotalPeople($database, $loc, "female");
        $allPeople = $malePeople + $femalePeople;
        $today = date('Y-m-d');
        $vc = $loc != 0 ? " AND  diplomats.location LIKE '$loc%' " : "";
        echo json_encode(
            [
                'people' => $allPeople,
                'office' => $loc,
                'ubwicanyi' => family::getTotal($database, 'security', "WHERE issue_id='3' 
                                    AND $columnLocation"),
                'ubujura' => family::getTotal($database, 'security', "WHERE issue_id='2' 
                                    AND $columnLocation"),
                'rent' => family::getTotal($database, 'diplomats', " where status='1' AND rent_house = 'yego' AND $columnLocation"),
                'violance' => family::getTotal($database, 'security', "  WHERE issue_id='10' 
                                    AND $columnLocation"),
                'female' => $femalePeople,
                'male' => $malePeople,
                'conflict' => family::getTotal($database, 'security', "WHERE issue_id='8' AND $columnLocation"),
                'umupaka' => family::getTotal($database, 'security', "WHERE issue_id='9' AND $columnLocation"),
                'drug' => family::getTotal($database, 'security', " WHERE issue_id='6' AND $columnLocation"),
                'covid' => family::getTotal($database, 'security', " where issue_id='12' 
                                    AND $columnLocation"),
                'notrent' =>
                family::getTotal($database, 'diplomats', " where status='1' AND rent_house = 'hoya' AND $columnLocation"),
                'ibiza' => family::getTotal($database, 'security', " WHERE issue_id='4' 
                                    AND $columnLocation"),
                'fraud' => family::getTotal($database, 'security', " where issue_id='7' 
                                    AND $columnLocation"),
                'kwiyahura' => family::getTotal($database, 'security', " where issue_id='5' 
                                    AND $columnLocation"),
                'intwaro' => family::getTotal($database, 'security', " where issue_id='11' 
                                    AND $columnLocation"),
                'urugomo' => family::getTotal($database, 'security', " where issue_id='1' 
                                    AND $columnLocation"),
                'visitors' => family::getTotal(
                    $database,
                    " family_visitors",
                    " inner JOIN diplomats on diplomats.id=family_visitors.head_id AND family_visitors.departure_date>='$today' $vc
                                             "
                )
            ]
        );
        break;
    case 'changefamilyhead':
        $head = input::get("head");
        $new_head = input::get("new_head");

        $head_data = $database->fetch("select * from diplomats where id = " . $head);
        $members = $database->fetch("select * from members where head = " . $head);

        $new_head_data = array_filter($members, function ($item) use ($new_head) {
            return $item["id"] == $new_head;
        });

        $other_members_data = array_filter($members, function ($item) use ($new_head) {
            return $item["id"] != $new_head;
        });

        !empty($new_head_data) ? $new_head_data = array_values($new_head_data)[0] : null;
        !empty($other_members_data) ? $other_members_data = array_values($other_members_data)[0] : null;
        !empty($head_data) ? $head_data = array_values($head_data)[0] : null;

        if (strlen($new_head_data["document_id"]) == 0) {
            echo json_encode([
                "msg" => $new_head_data['given_name'] . " " . $new_head_data['family_name'] . " ntacyangombwa afite. agomba kugira icyangombwa kugirango abe umukuru w'umuryango",
                "error" => "exist", "viewError" => '', "view" => '',
                "id" => 0
            ]);
            exit(0);
        }

        $newHeadFormData  = [
            "given_name" => $new_head_data["given_name"],
            "family_name" => $new_head_data["family_name"],
            "other_name" => $new_head_data["other_name"],
            "gender" => $new_head_data["gender"],
            "dob" => $new_head_data["dob"],
            "birth_place" => $new_head_data["birth_place"],
            "birth_nationality" => $new_head_data["birth_nationality"],
            "other_nationality" => $new_head_data["other_nationality"],
            "email" => $new_head_data["email"],
            "phone" => $new_head_data["phone"],
            "document_id" => $new_head_data["document_id"],
            "issued_country" => $new_head_data["issued_country"],
            "issued_date" => $new_head_data["issued_date"],
            "type" => $new_head_data["type"],
            "expiry_date" => $new_head_data["expiry_date"],
            "occupation" => $new_head_data["occupation"],
            "level_education" => $new_head_data["level_education"],
        ];

        $isHeadAdded = family::editMemberInFamily($database, $head_data["id"], $newHeadFormData, "diplomats");

        $thisMemberTrackings = $database->fetch("select * from track_movement where migrant_id=" . $new_head . " and migrant_type='MEMBER'");
        $thisHeadTrackings = $database->fetch("select * from track_movement where migrant_id=" . $head . " and migrant_type='HEAD'");

        if ($thisMemberTrackings != null) {
            $ids = array_map(function ($track) {
                return $track["id"];
            }, $thisMemberTrackings);

            $database->query("update track_movement set migrant_id=" . $head . ", migrant_type='HEAD' where id IN (" . implode(',', $ids) . ")");
        }

        $new_head_data["given_name"] = $head_data["given_name"];
        $new_head_data["family_name"] = $head_data["family_name"];
        $new_head_data["other_name"] = $head_data["other_name"];
        $new_head_data["gender"] = $head_data["gender"];
        $new_head_data["dob"] = $head_data["dob"];
        $new_head_data["birth_place"] = $head_data["birth_place"];
        $new_head_data["birth_nationality"] = $head_data["birth_nationality"];
        $new_head_data["other_nationality"] = $head_data["other_nationality"];
        $new_head_data["email"] = $head_data["email"];
        $new_head_data["phone"] = $head_data["phone"];
        $new_head_data["document_id"] = $head_data["document_id"];
        $new_head_data["issued_country"] = $head_data["issued_country"];
        $new_head_data["issued_date"] = $head_data["issued_date"];
        $new_head_data["type"] = $head_data["type"];
        $new_head_data["expiry_date"] = $head_data["expiry_date"];
        $new_head_data["occupation"] = $head_data["occupation"];
        $new_head_data["level_education"] = $head_data["level_education"];

        $isMemberAdded = family::editMemberInFamily($database, $new_head_data["id"], $new_head_data);

        if (!empty($thisHeadTrackings)) {
            $ids = array_map(function ($track) {
                return $track["id"];
            }, $thisHeadTrackings);

            $database->query("update track_movement set migrant_id=" . $new_head . ", migrant_type='MEMBER' where id IN (" . implode(',', $ids) . ")");
        }

        echo json_encode([
            "data" => input::enc_dec("e", $new_head), "error" => "none", "viewError" => '', "view" => 'addMemberInFamily',
            "id" => $isMemberAdded
        ]);

        exit(0);
        break;
    case 'transfermember':
        $migrant = json_decode(input::get("migrantInfo"), true);
        // check if any member assigned to migrant
        $id = $migrant["citizenId"];
        if (family::getTotal($database, "citizen", " where familyId= '$id'") > 0) {
            $names = $migrant["familyName"] . ' ' . $migrant['otherName'] . ' ' . $migrant["givenName"];
            echo json_encode([
                "msg" => "Kwimura $names nkugize umuryango ntibyemewe. Arachafite abantu bamwanditseho muwundi muryango",
                "error" => "exist", "viewError" => '', "view" => 'transferMigrant',
                "id" => 0
            ]);
            exit(0);
        }
        echo json_encode([
            "data" => $migrant,
            "error" => "none", "viewError" => '', "view" => 'transferMigrant',
            "id" => 1
        ]);
        exit(0);

        /*  $tracking = array(
            "names" => $names,
            "migrant_id" => $migrant["id"],
            "migrant_type" => isset($migrant["members"]) ? "HEAD" : "MEMBER",
            "pre_location" => $migrant["location"],
            "current_location" => input::get("user_loc"),
            "user_id" => session::get("id")
        );
        if ($tracking["migrant_type"] == "HEAD" && input::get("table") == "members") {
            // Get children from head
            $query = "select * from members where head=" . $migrant["id"];
            if (!empty($database->fetch($query))) {
                echo json_encode([
                    "msg" => "Kwimura $names nkugize umuryango ntibyemewe. Arachafite abantu bamwanditseho muwundi muryango",
                    "error" => "exist", "viewError" => '', "view" => 'transferMigrant',
                    "id" => 0
                ]);
                exit(0);
            }
        }
        $lastId = family::addMigration($database, $tracking, input::get("table"));
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
            "msg" => "Kwimura $names ntabwo yimuwe mwongere mugerageze",
            "error" => "exist", "viewError" => '', "view" => 'transferMigrant',
            "id" => 0
        ]);*/
        //echo json_encode(["msg" => 'Transfer Member is under contruction', "error" => "exist"]);
        break;
    case 'checkmember':
        $type = input::sanitize("doctype");
        $uloc = input::get("user_loc");
        $rwandan_id = trim(input::sanitize('rwandan_id'));
        if ($type == 'PASSPORT') {
            $passport = input::sanitize('passport');
            $issued_country = input::sanitize('issued_country');
            $id_to_edit = input::sanitize('id_to_edit');
            $errors = check(array($passport, $issued_country), array('pasiporo', 'Igihugu cyatanze pasiporo'));
            if (!empty($errors)) {
                echo json_encode([
                    "data" => '', "error" => "none",
                    "view" => 'checkMember', "viewError" => $errors
                ]);
                exit(0);
            }

            if ($id_to_edit)
                $cond = "documentNumber='$passport'  and issuedCountry=$issued_country and citizenId<>$id_to_edit  ORDER BY citizenId DESC limit 1";
            else
                $cond = "documentNumber='$passport'  and issuedCountry=$issued_country  ORDER BY citizenId DESC limit 1";
            // $headInfo = family::checkHeadOfFamily($database, $cond);

            //if ($id_to_edit)
            //  $cond = "members.documentNumber='$passport'  AND members.issued_country=$issued_country  and members.id<>$id_to_edit AND members.status='1' ORDER BY members.id DESC limit 1";
            // else
            //    $cond = "members.documentNumber='$passport'  AND members.issued_country=$issued_country  AND members.status='1' ORDER BY members.id DESC limit 1";

            // check in member of family

            // $memberInfo = family::checkMemberInFamily($database, $cond);
            // current_location

            $cl = '';
            // $Info = chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($headInfo) > 0) {
                //$hloc = explode('#', );
                $cl = user::decodeLocation($database, $headInfo[0]['location']);
                $uloc = user::decodeLocation($database, $uloc);
            }
            echo json_encode([
                "data" => $headInfo, "error" => "none",
                "viewError" => '', "view" => 'checkMember',
                "type" => $type,
                "location" => $cl, "user_loc" => $uloc
            ]);
            exit(0);
        }

        if ($type == 'NONE') {
            // check member if exist or not without passport and ID
            echo json_encode([
                "data" => $_REQUEST,
                "type" => $type,
                "error" => "none",
                "viewError" => '',
                "view" => 'checkMember'
            ]);
            exit(0);
        }

        if (!empty($rwandan_id) && strlen($rwandan_id) == 16) {


            $id_to_edit = input::sanitize('id_to_edit');

            // check in head of family

            if (!empty($id_to_edit))
                $cond = "documentNumber='$rwandan_id' and citizenId<>$id_to_edit ORDER BY citizenId limit 1";
            else
                $cond = "documentNumber='$rwandan_id' ORDER BY citizenId limit 1";

            $headInfo = family::checkHeadOfFamily($database, $cond);
            // check in member of family
            /*$cond = "members.documentNumber='$rwandan_id' and members.id<>$id_to_edit ORDER BY id limit 1";

            if (!empty($id_to_edit))
                $cond = "members.documentNumber='$rwandan_id' and members.id<>$id_to_edit ORDER BY id limit 1";
            else
                $cond = "members.documentNumber='$rwandan_id' ORDER BY id limit 1";

            $memberInfo = family::checkMemberInFamily($database, $cond);
            */
            // current_location
            $cl = '';
            $Info = $headInfo; //chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($Info) > 0) {
                //$hloc = explode('#', );
                $cl = user::decodeLocation($database, $Info[0]['location']);
                //$cl= user::getLocationName($database, $Info[0]['location']);
                $uloc = user::decodeLocation($database, $uloc);
            }
            echo json_encode([
                "data" => $Info,
                "cond" => $cond,
                "type" => $type,
                "error" => "none",
                "viewError" => '',
                "view" => 'checkMember',
                "location" => $cl,
                "user_loc" => $uloc
            ]);
            exit(0);
        }
        echo json_encode(["error" => "none", "viewError" => ['Indangamuntu yanditse nabi' . '(' . $rwandan_id . ')']]);
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
        $upi = input::sanitize("upi");
        $errors = check(
            array(
                $given_name, $family_name, $gender, $dob, $marital_status,
                $birth_place, $birth_nationality, $ubudehe, $isibo,
                $rent_house, $occupation, $level_education, $upi
            ),
            array(
                'Izina rusange', 'Izina ry\'umuryango', 'Igitsina', 'Itariki yamavuko',
                'Irangamimerere', 'Aho yavukiye', 'Ubwenegihugu',
                'Ubudehe', 'isibo', 'Arakodesha ?', 'umwuga akora', 'Urwego rw\'uburezi', 'Nimero y\'ikibanza ntayirimo'
            )
        );
        if ($rent_house == 'hoya') {
            empty($number_house) ? array_push($errors, 'umubare w\'inzu') : '';
        } else if ($rent_house == "yego") {
            empty($house_info) ? array_push($errors, 'Amakuru ya Nyirinzu') : '';
        }
        // check if Document type is ID
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }

        $village = explode("#", $uloc);
        $village = output::print(4, $village, 0);
        //  insert into citzen tb
        $formData2 = array(
            "givenName" => $given_name,
            "familyName" => $family_name,
            "otherName" => $other_name,
            "gender" => $gender,
            "dob" => $dob,
            "martialstatus" => $marital_status,
            "birthplace" => $birth_place,
            "birthNationality" => $birth_nationality,
            "otherNationality" => $other_nationality,
            "email" => $email,
            "mobile" => $phone,
            "documentNumber" => $passporttocheck,
            "issuedCountry" => $issued_country,
            "issuedDate" => $issued_date,
            "expiryDate" => $expiry_date,
            "photoPassport" => "",
            "created_at" => date('Y-m-d h:i:s'),
            "created_by" => session::get("id"),
            "location" => $uloc,
            "upi" => $upi,
            "currentLocation" => $village,
            "documentType" => $type,
            'is_family_heading' => '1',
            'familyCategory' => "head",
            "ubudehe" => $ubudehe,
            "isibo" => $isibo,
            "occupation" => $occupation,
            "number_of_rent_house" => $number_house,
            "landLord" => $house_info,
            "level_of_education" => $level_education,
            'familyId' => null,
            "status" => "1",
            "key_words" => $given_name . '' . $family_name . '' . $other_name . '' . $passporttocheck
        );


        // if it is edit or insert
        $isAdded = 0;
        // check if it is transfer
        if (input::sanitize("transfer") == "yes") {
            $mid = input::sanitize("tid");
            // save movement
            $database->beginTransaction();
            try {
                // update head to family info
                $isAdded = family::editMemberInFamily($database, $mid, $formData2, "citizen");
                $mid = $isAdded;
                // keep history
                $formData3 = array(
                    "citizenId" => $mid,
                    "movementType" => "gutura",
                    "isibo" => $isibo,
                    "landLord" => $house_info,
                    "created_by" => session::get("id"),
                    "upi" => $upi,
                    "location" => $uloc,
                    "previousLocation" => $village,
                    "created_at" => date('Y-m-d h:i:s'),
                );
                // save into table called hostory
                family::addFamilyHead($database, $formData3, "history");
                $database->commit();
            } catch (Exception $exception) {
                $isAdded = 0;
                $database->rollBack();
                var_dump($exception->getMessage());
                die();
            }
        } else {
            // new record
            $database->beginTransaction();
            try {
                // Add head to family
                $isAdded = family::addFamilyHead($database, $formData2);
                $mid = $isAdded;
                $database->commit();
                $msg = "normal execution";
                $formData3 = array(
                    "citizenId" => $mid,
                    "movementType" => "gutura",
                    "isibo" => $isibo,
                    "landLord" => $house_info,
                    "created_by" => session::get("id"),
                    "upi" => $upi,
                    "location" => $uloc,
                    "previousLocation" => $village,
                    "created_at" => date('Y-m-d h:i:s'),
                );
                // save into table called hostory
                family::addFamilyHead($database, $formData3, "history");
            } catch (Exception $exception) {
                $isAdded = 0;
                $database->rollBack();
                $msg = var_dump($exception->getMessage());
                die();
            }
        }

        echo json_encode([
            "data" => input::enc_dec("e", $mid),
            "error" => "none", "viewError" => '',
            "view" => 'addHeadOfFamily',
            "id" => $isAdded,
            "errors" => json_encode($msg)
        ]);
        exit(0);
        //    
        /*

                $formData = array(
            "given_name" => $given_name, "family_name" => $family_name, "other_name" => $other_name,
            "gender" => $gender, "dob" => $dob, "marital_status" => $marital_status, "birth_place" => $birth_place,
            "birth_nationality" => $birth_nationality, "other_nationality" => $other_nationality,
            "email" => $email, "phone" => $phone,
            "document_id" => $passporttocheck, "issued_country" => $issued_country,
            "issued_date" => $issued_date, "expiry_date" => $expiry_date,
            "time" => date('Y-m-d h:i:s'), "user" => session::get("id"),
            "location" => $uloc,
            "upi" => $upi,
            "village" => $village,
            "type" => $type,
            "ubudehe" => $ubudehe,
            "isibo" => $isibo,
            "rent_house" => $rent_house,
            "occupation" => $occupation,
            "number_house" => $number_house,
            "house_info" => $house_info,
            "level_education" => $level_education,
            "status" => "1"
        );
        
        
        if (input::sanitize("table") == "diplomats") {
                // found in head of family
                $migrant = json_decode(input::get("migrantInfo"), true);
                $database->beginTransaction();
                try {
                    $isAdded = family::editMemberInFamily($database, $mid, $formData, "diplomats");
                    $query = "select * from members where head=" . $mid;
                    $members = $database->fetch($query);
                    foreach ($members as $key => $member) {
                        $tracking = array(
                            "names" => $formData["given_name"] . " " . $formData["family_name"],
                            "migrant_id" => $member["id"],
                            "migrant_type" => "MEMBER",
                            "pre_location" => $migrant["location"],
                            "current_location" => input::get("user_loc"),
                            "user_id" => session::get("id")
                        );
                        $database->create("track_movement", $tracking);
                    }

                    $database->commit();
                } catch (Exception $exception) {
                    $isAdded = 0;
                    $database->rollBack();
                    var_dump($exception->getMessage());
                    die();
                }

            } */

        /*else {
                $database->beginTransaction();
                try {
                    // Add head to family
                    $isAdded = family::addHeadOfFamily($database, $formData);
                    // Delete members from member
                    $query = "delete from members where id=" . $mid;
                    $database->query($query);
                    // Update all previous movements of this user
                    $query = "update track_movement set migrant_id=" . $isAdded . ", migrant_type='HEAD' where migrant_id=" . $mid . " and migrant_type='MEMBER'";
                    $database->query($query);
                    // Insert new movement
                    $migrant = json_decode(input::get("migrantInfo"), true);
                    $tracking = array(
                        "names" => $formData["given_name"] . " " . $formData["family_name"],
                        "migrant_id" => $isAdded,
                        "migrant_type" => "HEAD",
                        "pre_location" => $migrant["location"],
                        "current_location" => input::get("user_loc"),
                        "user_id" => session::get("id")
                    );
                    $database->create("track_movement", $tracking);
                    $database->commit();
                } catch (Exception $exception) {
                    $database->rollBack();
                    var_dump($exception->getMessage());
                    die();
                }
                $mid = $isAdded;
            }*/
        // }
        /* else {
            $mid = output::print("id_to_edit", $_REQUEST, "");
            if (empty($mid)) {
                try {
                    $isAdded = family::addHeadOfFamily($database, $formData);
                    $mid = $isAdded;
                } catch (Exception $exception) {
                    var_dump($exception->getMessage());
                    die("Error");
                }
            } else {
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "diplomats");
            }
        }*/
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
        $uloc = input::get("user_loc");
        $village = explode("#", $uloc);
        $village = output::print(4, $village, 0);
        $errors = check(
            array($given_name, $family_name, $gender, $dob, $birth_place, $birth_nationality, $occupation, $level_education),
            array(
                'Izina rusange', 'Izina ry\'umuryango', 'Igitsina', 'Itariki yamavuko', 'Aho yavukiye',
                'Ubwenegihugu', 'umwuga akora ', 'Urwego rw\'uburezi '
            )
        );
        // check visitor
        $relationship = input::sanitize("relationship");
        $what_relationship = input::sanitize("what_relationship");
        /*if ($relationship == "Visitor") {
            // check arrival,check place,check departure date
            $ad = input::get("arrival_date");
            $dd = input::get("departure_date");
            $from = input::get("come_from");
            $pname = input::get("place_name");
            $head_document_id = input::get("head_document_id");
            $head_document_passport = input::get("head_document_passport");
            $head_document_issue_country = input::get("head_document_issue_country");
            $head_document_issue_date = input::get("head_document_issue_date");
            $head_document_expiry_date = input::get("head_document_expiry_date");
            $head_to_member_relationship = input::get("head_to_member_relationship");
            $head_document_type = input::get("head_document_type");
            $errors = array_merge($errors, check(array($ad, $pname, $dd, $head_document_type), array('Itariki yo kuhagera', 'Izina ry\'aho avuye', 'Itariki yo kugenda', 'Icyangobwa cy\'umukuru w\'umuryango')));
            $head_document_type == "ID" && empty($head_document_id) ? $errors = array_merge($errors, check(array($head_document_id, $head_to_member_relationship), array('Indangamuntu y\'umukuru w\'umuryango', 'Isano bafitanye numukuru w\'umuryango'))) : null;
            ($head_document_type == "PASSPORT" &&
                (empty($head_document_passport) || empty($head_document_issue_country) || empty($head_document_issue_date) || empty($head_document_expiry_date))) ?
                $errors = array_merge($errors, check(
                    [$head_document_passport, $head_document_issue_country, $head_document_issue_date, $head_document_expiry_date],
                    ["Passiporo y'umukuru w'umuryango", "Passiporo y'umukuru w'umuryango yatangiwe he?", "Passiporo y'umukuru w'umuryango yatanzwe ryari?", "Passiporo y'umukuru w'umuryango izarangira ryari?"]
                )) : null;
            $visitor_info = $ad . "#" . $from . '#' . $pname . "#" . $dd;
        }
        $visitorFoundInParentFamily = family::validateVisitorOriginFamily($database, $head_document_id, $head_document_passport, $head_document_issue_country, $family_name, $given_name, $relationship, $head_to_member_relationship);
        if ($visitorFoundInParentFamily == -1) {
            $errors = array_merge($errors, ["Uwongewemo ntiyanditswe mumuryango yerekanyeko aturukamo"]);
        }
        if (!family::validateMemberNamesAndRelationship($database, $pid, $family_name, $given_name, $other_name, $relationship, input::get("id_to_edit"))) {
            $errors = array_merge($errors, ["Uwongewemo yisubiyemo"]);
        }
        */
        if ($relationship == "Other" && empty($what_relationship)) {
            $errors = array_merge($errors, ["Shyiramo isano bafitanye"]);
        }
        // check if Document type is ID
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }
        if ($relationship == "Other") $relationship = $what_relationship;
        $formData = array(
            "familyId" => input::get("pid"),
            "givenName" => $given_name,
            "familyName" => $family_name,
            "otherName" => $other_name,
            "gender" => $gender,
            "dob" => $dob,
            "birthplace" => $birth_place,
            "birthNationality" => $birth_nationality,
            "otherNationality" => $other_nationality,
            "email" => $email,
            "mobile" => $phone,
            "is_family_heading" => "0",
            "documentNumber" => $passporttocheck,
            "issuedCountry" => $issued_country,
            "issuedDate" => $issued_date,
            "expiryDate" => $expiry_date,
            "created_at" => date('Y-m-d h:i:s'),
            "created_by" => session::get("id"),
            "familyCategory" => $relationship,
            "documentType" => $type,
            "level_of_education" => $level_education,
            "occupation" => $occupation,
            "location" => $uloc,
            "currentLocation" => $village,
            "status" => "1",
            "key_words" => $given_name . '' . $family_name . '' . $other_name . '' . $passporttocheck
        );
        // here is to insert visitor info
        // "visitor_info" => $visitor_info;
        /*
         "head_document_id" => $head_document_id,
            "head_document_passport" => $head_document_passport,
            "head_document_issue_country" => $head_document_issue_country,
            "head_document_issue_date" => $head_document_issue_date,
            "head_document_expiry_date" => $head_document_expiry_date,
            "head_document_type" => $head_document_type,
            "head_to_member_relationship" => $head_to_member_relationship,
        */
        $isAdded = 0;
        // check if it is transfer
        $mid = "";
        if (input::sanitize("transfer") == "yes") {
            $mid = input::sanitize("tid");
            $isAdded = family::editMemberInFamily($database, $mid, $formData, "citizen");
            $formData3 = array(
                "citizenId" => $mid,
                "movementType" => "gutura",
                "isibo" => $isibo,
                "landLord" => $house_info,
                "created_by" => session::get("id"),
                "upi" => $upi,
                "location" => $uloc,
                "previousLocation" => $village,
                "created_at" => date('Y-m-d h:i:s'),
            );
            // save into table called hostory
            family::addFamilyHead($database, $formData3, "history");

            /* if (input::sanitize("table") == "diplomats") {
                $migrant = json_decode(input::get("migrantInfo"), true);
                //Get number of people in the family
                $query = "select * from members where head=" . $migrant["id"];
                if (empty($database->fetch($query))) {
                    $database->beginTransaction();
                    try {
                        //Add member to family
                        $isAdded = family::addMemberInFamily($database, $formData, $visitorFoundInParentFamily);
                        //Delete current head
                        $query = "delete from diplomats where id=" . $migrant["id"];
                        $database->query($query);
                        // Update all previous movements of this user
                        $query = "update track_movement set migrant_id=" . $isAdded . ", migrant_type='MEMBER' where migrant_id=" . $migrant["id"] . " and migrant_type='HEAD'";
                        $database->query($query);
                        // Insert new movement
                        $migrant = json_decode(input::get("migrantInfo"), true);
                        $tracking = array(
                            "names" => $formData["given_name"] . " " . $formData["family_name"],
                            "migrant_id" => $isAdded,
                            "migrant_type" => "MEMBER",
                            "pre_location" => $migrant["location"],
                            "current_location" => input::get("user_loc"),
                            "user_id" => session::get("id")
                        );
                        $database->create("track_movement", $tracking);
                        $database->commit();
                    } catch (Exception $exception) {
                        $database->commit();
                        var_dump($exception->getMessage());
                        die();
                    }
                } else {
                    $isAdded = 0;
                }
            } else {
                // exist in members;
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "members");
            }*/
        } else {
            $mid = output::print("id_to_edit", $_REQUEST, "");
            if (empty($mid)) {
                // Add member in family
                // $isAdded = family::addMemberInFamily($database, $formData);
                $isAdded = family::addFamilyHead($database, $formData);
                // Increase number of members in family
                // family::incrementNumberOfMembers($database, input::get("pid"), "members");
            } else {
                try {
                    $isAdded = family::editMemberInFamily($database, $mid, $formData, 'citizen');
                } catch (Exception $exception) {
                    var_dump($exception->getMessage());
                    die();
                }
            }
        }
        if ($isAdded == 0) {
            echo json_encode(["msg" => 'kwandika ' . $given_name . "  mumuryango ntabwo bikunze \nmwongere mugerageze", "viewError" => '', "error" => "exist"]);
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
        $errors = check(array($giver, $help), array("Umuterankunga", "ubufasha ahabwa"));
        if ($giver == "ikindi" && empty(trim($other_giver))) {
            $errors = array_merge($errors, ["Izina ry'umuterankunga"]);
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
        echo json_encode(["msg" => "Ntabwo ubufasha bwanditswe mwongere mugerageze", "viewError" => '', "error" => "exist"]);
        break;
    case "get_movements":
        // get movement made by head of family or member of family;
        $id = trim(input::sanitize('id'));
        $type = trim(input::sanitize('type'));
        if (strlen($id) == 0 || strlen($type) == 0) {
            echo json_encode([
                "msg" => "Indangamuntu/pasiporo biracyenewe", "error" => "exist", "viewError" => '', "view" => ''
            ]);
            exit(0);
        }
        $movements = family::getAllMovement($database, $id, $type);
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
        //$cond = ["m" => "(concat(m.family_name,m.given_name,m.document_id)) LIKE '%$search%'", "d" => "(concat(d.family_name,d.given_name,d.document_id)) LIKE '%$search%' AND 1"];
        $cond = " key_words LIKE '%$search%'";
        $result = family::find($database, $cond, $loc);
        echo json_encode([
            "data" => displayPopulation($result, $search),
            "error" => "none",
            "viewError" => '',
            "view" => 'viewSearchResult',
            "count" => count($result)
        ]);
        break;
    default:
        echo json_encode(["msg" => $opera . ' not found', "error" => "exist", "viewError" => '']);
        break;
}
