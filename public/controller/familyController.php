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
                'rent' => family::getTotal($database, 'citizen', " where is_family_heading='1' AND  landLord  is not  null AND $columnLocation"),
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
                family::getTotal($database, 'citizen', "  where is_family_heading='1' AND  landLord  is  null AND $columnLocation"),
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
        // update in citizen
        $head_data = $database->fetch_array($database->query("select number_of_rent_house,upi,landLord,location,currentLocation from citizen where citizenId = " . $head));
        $database->beginTransaction();
        try {
            // change head of family
            if (!input::required(array("migration"))) {
                $database->update(
                    "citizen",
                    "citizenId='$head'",
                    array(
                        "familyId" => $new_head,
                        'is_family_heading' => '0',
                        'updated_at' => date('Y-m-d h:i:s'),
                        'created_by' => session::get("id")
                    )
                );
            }
            $database->update(
                "citizen",
                "citizenId='$new_head'",
                array(
                    'is_family_heading' => '1',
                    'number_of_rent_house' => $head_data["number_of_rent_house"],
                    'landLord' => $head_data["landLord"],
                    'upi' => $head_data["upi"],
                    'currentLocation' => $head_data["currentLocation"],
                    'location' => $head_data["location"],
                    "familyId" => 0,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'created_by' => session::get("id")
                )
            );
            $database->update(
                "citizen",
                "familyId='$head'",
                array(
                    "familyId" => $new_head,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'created_by' => session::get("id")
                )
            );
            // update in visitors
            $database->update(
                "visitors",
                "resident_id='$head'",
                array(
                    "resident_id" => $new_head,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'user_id' => session::get("id")
                )
            );
            // update history
            $database->update(
                "history",
                "landLord='$head'",
                array(
                    "landLord" => $new_head,
                    'created_by' => session::get("id")
                )
            );
            $database->commit();
            echo json_encode(["status" => true, "head" => input::enc_dec("e", $new_head)]);
        } catch (Exception $exception) {
            $database->rollBack();
            var_dump($exception->getMessage());
            die();
        }
        exit(0);
        break;
    case 'transfermember':
        $migrant = json_decode(input::get("migrantInfo"), true);
        // check if any member assigned to migrant
        $id = $migrant["citizenId"];
        $migrant["migration"] = "no";
        if ($migrant["relation"] != "Umushyitsi") {
            if (family::getTotal($database, "citizen", " where familyId= '$id'") > 0) {
                // $names = $migrant["familyName"] . ' ' . $migrant['otherName'] . ' ' . $migrant["givenName"];
                // echo json_encode([
                //     "msg" => "$names Ntabwo yemerewe kwimurwa afite abamwanditsweho",
                //     "error" => "exist",
                //     "viewError" => '',
                //     "view" => 'transferMigrant',
                //     "id" => 0
                // ]);
                // exit(0);
                // keep info 
                $results = family::getMembersInFamily($database, $id);
                $members = array();
                while ($member = $database->fetch_array($results)) {
                    $members[] = $member;
                }
                $migrant["migration"] = $members;
            }
        }
        echo json_encode([
            "data" => $migrant,
            "error" => "none", "viewError" => '', "view" => 'transferMigrant',
            "id" => 1
        ]);
        exit(0);

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

            $cl = '';
            $headInfo = family::checkHeadOfFamily($database, $cond);
            // $Info = chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($headInfo) > 0) {
                if (!empty($Info[0]['location']))
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
            // current_location
            $cl = '';
            $Info = $headInfo; //chooseInfoToDisplay($headInfo, $memberInfo);
            if (count($Info) > 0) {
                //$hloc = explode('#', );
                if (!empty($Info[0]['location']))
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
        $msg = "";
        $uloc = input::get("user_loc");
        $given_name = input::sanitize("given_name");
        $family_name = input::sanitize('family_name');
        $other_name = input::sanitize('other_name');
        $gender = input::sanitize('gender');
        $dob = date('Y-m-d', strtotime(input::get('dob')));
        $marital_status = input::sanitize('marital_status');
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
        // $upi
        $errors = check(
            array(
                $given_name, $family_name, $gender, $dob, $marital_status,
                $birth_place, $birth_nationality, $ubudehe, $isibo,
                $rent_house, $occupation, $level_education
            ),
            array(
                'Izina rusange', 'Izina ry\'umuryango', 'Igitsina', 'Itariki yamavuko',
                'Irangamimerere', 'Aho yavukiye', 'Ubwenegihugu',
                'Ubudehe', 'isibo', 'Arakodesha ?', 'umwuga akora', 'Urwego rw\'uburezi'
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
        $keywords = $given_name . '' . $family_name . $family_name . '' . $given_name . '' . $other_name . '' . $dob . '' . $birth_place . '' . $passporttocheck;
        $keywords = strtolower(preg_replace('/\s+/', '', $keywords));
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
            'familyId' => 0,
            "status" => "1",
            "key_words" => $keywords
        );
        // if it is edit or insert
        $isAdded = 0;
        // check if it is transfer
        if (input::sanitize("transfer") == "yes") {
            $mid = input::get("tid");
            // update last visits
            $today = date('Y-m-d h:i:s');
            $database->query("update visitors set status='0',updated_at='$today' WHERE visited_by='$mid' order by id desc limit 1");
            //  update location of member registered remotely
            if (input::sanitize("added_remotely") == "yes") {
                $sql = "update citizen set location='$uloc',currentLocation='$village' where familyId='$mid'";
                $database->query($sql);
            }
            // save movement
            $database->beginTransaction();
            try {
                // update head to family info
                $isAdded = family::editMemberInFamily($database, $mid, $formData2, "citizen");
                // $mid = $isAdded;
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
                $database->commit();
            } catch (Exception $exception) {
                $isAdded = 0;
                $database->rollBack();
                $msg = var_dump($exception->getMessage() . json_encode($formData3));
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
        break;
    case 'check_unknown_family_member':
        echo json_encode(["msg" => ' to check Unknown Member is under contruction', "error" => "exist"]);
        break;
    case 'member_to_family':
        $pid = input::get("pid");
        $params = "%members";
        $given_name = input::sanitize("given_name");
        $family_name = input::sanitize('family_name');
        $other_name = input::sanitize('other_name');
        $gender = input::sanitize('gender');
        $dob = date('Y-m-d', strtotime(input::get('dob')));
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
        $ubudehe = input::sanitize('ubudehe');
        $village = output::print(4, $village, 0);
        $errors = check(
            array($given_name, $family_name, $gender, $dob, $birth_place, $birth_nationality, $occupation, $level_education, $ubudehe),
            array(
                'Izina rusange', 'Izina ry\'umuryango', 'Igitsina', 'Itariki yamavuko', 'Aho yavukiye',
                'Ubwenegihugu', 'umwuga akora ', 'Urwego rw\'uburezi ', 'ubudehe'
            )
        );
        // check visitor
        $relationship = input::get("relationship");
        $what_relationship = input::sanitize("what_relationship");
        if ($relationship == "Umushyitsi") {
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
            // 'Icyangobwa cy\'umukuru w\'umuryango'
            $errors = array_merge($errors, check(
                array($ad, $pname, $dd),
                array('Itariki yo kuhagera', 'Izina ry\'aho avuye', 'Itariki yo kugenda',)
            ));
        }

        if ($relationship == "Other" && empty($what_relationship)) {
            $errors = array_merge($errors, ["Shyiramo isano bafitanye"]);
        }
        // check if Document type is ID
        if (!empty($errors)) {
            echo json_encode(["data" => '', "error" => "none", "viewError" => $errors]);
            exit(0);
        }
        if ($relationship == "Other") $relationship = $what_relationship;
        $keywords = $given_name . '' . $family_name . $family_name . '' . $given_name . '' . $other_name . '' . $dob . '' . $birth_place . '' . $passporttocheck;
        $keywords = strtolower(preg_replace('/\s+/', '', $keywords));
        $formData = array(
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
            "documentNumber" => $passporttocheck,
            "issuedCountry" => $issued_country,
            "issuedDate" => $issued_date,
            "expiryDate" => $expiry_date,
            "created_at" => date('Y-m-d h:i:s'),
            "created_by" => session::get("id"),
            "documentType" => $type,
            "level_of_education" => $level_education,
            "occupation" => $occupation,
            "ubudehe" => $ubudehe,
            "status" => "1",
            "key_words" => $keywords

        );
        // here is to insert visitor info
        // "visitor_info" => $visitor_info;
        $isAdded = 0;
        // check if it is transfer
        $mid = "";
        if (input::sanitize("transfer") == "yes") {
            $mid = input::sanitize("tid");
            // update last visit in visitor
            // update last visits
            $today = date('Y-m-d h:i:s');
            $database->query("update visitors set status='0',updated_at='$today' WHERE visited_by='$mid' order by id desc limit 1");
            //  if(family::getTotal($database,"visitors"," where visited_by=''"))
            if ($relationship != "Umushyitsi") {
                $formData["is_family_heading"] = "0";
                $formData["familyId"] = input::get("pid");
            }
            $database->beginTransaction();
            try {
                if (input::sanitize("added_remotely") == "yes") {
                    $sql = "update citizen set location='$uloc',currentLocation='$village' where familyId='$mid'";
                    $database->query($sql);
                }
                $formData["familyCategory"] = $relationship;
                $isAdded = family::editMemberInFamily($database, $mid, $formData, "citizen");
                $formData3 = array(
                    "citizenId" => $mid,
                    "movementType" => $relationship,
                    "isibo" => "",
                    "landLord" => $mid,
                    "created_by" => session::get("id"),
                    "upi" => '',
                    "location" => $uloc,
                    "previousLocation" => $village,
                    "created_at" => date('Y-m-d h:i:s'),
                );
                // save in visitors table
                if ($relationship == "Umushyitsi") {
                    // edit visitor location
                    family::addFamilyHead(
                        $database,
                        array(
                            "location" => $uloc,
                            "village" => $village,
                            "visited_by" => $mid,
                            "arrival_date" => $ad,
                            "departure_date" => $dd,
                            "come_from" => $from,
                            "place_name" => $pname,
                            "user_id" => session::get("id"),
                            "resident_id" => input::get("pid")
                        ),
                        "visitors"
                    );
                    $params = "%help";
                }
                // save into table called hostory
                family::addFamilyHead($database, $formData3, "history");
                $database->commit();
            } catch (\Exception $exception) {
                $isAdded = 0;
                $database->rollBack();
                $msg = var_dump($exception->getMessage());
                die();
            }
        } else {
            $mid = output::print("id_to_edit", $_REQUEST, "");
            $formData["familyId"] = input::get("pid");
            $formData["familyCategory"] = $relationship;
            $formData["is_family_heading"] = "0";
            if (input::sanitize("added_remotely") == "no") {
                $formData["location"] = $uloc;
                $formData["currentLocation"] = $village;
                $formData["familyCategory"] = $relationship;
            }
            if (empty($mid)) {
                // Add member in family
                if (input::sanitize("added_remotely") == "yes") {
                    $mid = input::sanitize("tid");
                    // add unregistered head of family
                    $pid = input::get("pid");
                    if (input::get("members") != "add") {
                        $pid  = family::addFamilyHead($database, array(
                            "is_family_heading" => '1',
                            "created_at" => date('Y-m-d h:i:s'),
                            "created_by" => session::get("id"),
                            "documentNumber" => input::get('pid'),
                            "familyCategory" => "head"
                        ));
                    }
                    $formData["familyId"] =  $pid;
                }
                $database->beginTransaction();
                try {
                    // add new citizen in db
                    $isAdded = family::addFamilyHead($database, $formData);
                    $formData3 = array(
                        "citizenId" => $isAdded,
                        "movementType" => $relationship,
                        "isibo" => "",
                        "landLord" => $mid,
                        "created_by" => session::get("id"),
                        "upi" => '',
                        "location" => $uloc,
                        "previousLocation" => $village,
                        "created_at" => date('Y-m-d h:i:s'),
                    );
                    // save into table called hostory
                    family::addFamilyHead($database, $formData3, "history");
                    // save in visitors table
                    if ($relationship == "Umushyitsi") {
                        // edit visitor location
                        family::addFamilyHead(
                            $database,
                            array(
                                "location" => $uloc,
                                "village" => $village,
                                "visited_by" => $isAdded,
                                "arrival_date" => $ad,
                                "departure_date" => $dd,
                                "come_from" => $from,
                                "place_name" => $pname,
                                "resident_id" => input::get("tid"),
                                "user_id" => session::get("id"),
                                "created_at" => date('Y-m-d h:i:s')
                            ),
                            "visitors"
                        );
                        $pid = input::get("tid");
                        $params = "%help";
                    }
                    # code...
                    $database->commit();
                } catch (\Exception $exception) {
                    $isAdded = 0;
                    $database->rollBack();
                    $msg = var_dump($exception->getMessage());
                    die();
                }
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
            "data" =>  rawurlencode(input::enc_dec("e", $pid)) . rawurlencode("$params"), "error" => "none", "viewError" => '', "view" => 'addMemberInFamily',
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
            "count" => 1
        ]);
        break;
    case 'get_head_with_members':
        $document = input::sanitize("familyid");
        // check document if exists
        if (family::getTotal($database, "citizen", " where documentNumber='$document' limit 1") == 0) {
            echo json_encode([
                "data" => [],
                "msg" => "Umwishingizi ntabwo abonetse",
                "document" => $document,
                "status" => "NOT_FOUND"
            ]);
            exit(0);
        }
        // family head exitis
        $head = $database->fetch_array(family::getHeadOfFamilyByDocument($database, $document));
        // check if has all required informations
        $head["locationByName"] = "";
        if (isset($head["location"]) && !empty($head["location"])) {
            $head["locationByName"] = user::decodeLocation($database, $head["location"]);
        }
        // get members
        $results = family::getMembersInFamily($database, $head["citizenId"]);
        $li = "<ul class='list-group'>";
        $c = 1;
        while ($member = $database->fetch_array($results)) {
            $li .= "<li class='list-group-item'>" . $c . '. ' . $member['familyName'] . ' ' . $member['otherName'] . ' ' . $member['givenName'] . ' ' . $member['dob'] . " &nbsp;<button class='btn btn-success' type='button' 
            onclick='confirmVisitor(" . json_encode($member) . ")' >
            Emeza</button></li>";
            $c++;
        }
        $li .= "</ul>";
        // remote registrastion
        $head["members"] = $li;
        $head["member_count"] = $c;
        echo json_encode(["data" => $head, "msg" => "Amakuru abonetse"]);

        break;
    case 'visitor_leave':
        $id = input::sanitize("id");
        echo json_encode(["status" => $database->update(
            "visitors",
            "id=$id",
            array("status" => "yes", "updated_at" => date('Y-m-d h:i:s'))
        )]);
        break;
    case "check_new_document_in_system":
        $keywords = strtolower(input::sanitize("key_words"));
        $keywords = preg_replace('/\s+/', '', $keywords);
        $results = family::getSuggestions($database, $keywords);
        $doc = input::sanitize("documentNumber");
        // loop result
        $tr = "";
        $c = 0;
        foreach ($results as $key => $member) {
            $c += 1;
            $location = user::getLocationName($database, $member["location"]);
            $head = family::getCitizenById($database, $member["familyId"]);
            $names = $head['givenName'] . ' ' . $head['otherName'] . ' ' . $head['familyName'];
            $member["documentNumber"] = $doc;
            $tr .= "<tr><td>" . $c . "</td><td>" .  $member['givenName'] . ' ' . $member['otherName'] . ' ' . $member['familyName'] . "</td> <td>" . $names . "</td>
            <td>" . $location['province'] . '/' . $location['district'] . '/' . $location['sector'] . '/' . $location['cell'] . '/' . $location['village'] . "</td>
            <td><button class='btn btn-primary fs-13' type='button' onclick='confirmSuggestion(" . json_encode($member) . ")'>Emeza &rarr;</button></td></tr>";
        }
        echo json_encode(["data" => $tr, "total" => $c, "keywords" => $keywords]);
        break;
    case 'add_unregistered_head_of_family':
        $doc = input::sanitize("document");
        $formData = array(
            "is_family_heading" => '1',
            "created_at" => date('Y-m-d h:i:s'),
            "created_by" => session::get("id"),
            "documentNumber" => $doc,
            "familyCategory" => "head"
        );
        $isAdded = family::addFamilyHead($database, $formData);
        echo json_encode(["data" => $formData, "status" => $isAdded]);
        break;
    case 'find_head_of_family':
        echo json_encode([
            "data" => '', "error" => "none",
            "view" => 'find_head_of_family', "viewError" => ""
        ]);
        exit(0);
        break;
    default:
        echo json_encode(["msg" => $opera . ' not found', "error" => "exist", "viewError" => '']);
        break;
}
