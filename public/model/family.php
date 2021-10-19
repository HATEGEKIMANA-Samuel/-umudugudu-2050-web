<?php

// model related to family
class family
{
    private $db;

    const RELATIONSHIP_VISITOR = "Visitor";
    const RELATIONSHIP_KID = "Kid";
    const RELATIONSHIP_WIFE = "Wife";
    const DOCUMENT_TYPE_NONE = "NONE";
    private function __construct($database = null)
    {
        # code...
        $this->db = $database;
    }

    /**
     * function used to add head of family
     * Checks whether there temporal assignments to a newly created family head
     * @param $db
     * @param array $formData
     * @return int
     */
    public static function addHeadOfFamily($db, $formData = array())
    {
        // Default for national id(s)
        $formData["type"] == "ID" ?  $formData["issued_country"] = 178 : null;

        // check if current the current head of family has some temp records
        $query = "select * from diplomats_temp 
        where document_id='" . $formData["document_id"] . "' and 
        issued_country=" . $formData["issued_country"] . " and type='" . $formData["type"] . "'";
        $rows = $db->fetch($query);
        $found_temp_dep = isset($rows[0]) ? $rows[0] : null;
        // If member is found, then fetch associated temporarily member details
        $temporal_member_details = [];
        $member_ids = [];
        if (!empty($found_temp_dep)) {
            // fetch member ids
            $registered_dep_members = $db->fetch("select * from diplomats_temp_members where diplomats_temp_id='" . $found_temp_dep["id"] . "'");
            $member_ids = array_map(function ($item) {
                return $item["members_id"];
            }, $registered_dep_members);

            foreach ($registered_dep_members as $registered_dep_member) {
                $member_detail = $db->fetch("select * from members where id='" . $registered_dep_member["members_id"] . "'");
                $member_detail = isset($member_detail[0]) ? $member_detail[0] : null;
                (!empty($member_detail) && is_array($member_detail)) ? $member_detail["relationship"] = $registered_dep_member["relationship"] : null;
                (!empty($member_detail) && is_array($member_detail)) ? array_push($temporal_member_details, $member_detail) : null;
            }
        }



        // Clear form_data  to preevent query failure
        foreach ($formData as $key => $value) {
            if (strlen($value) == 0) {
                unset($formData[$key]);
            }
        }

        // Begin all processes in one db transaction
        $db->beginTransaction();
        try {

            $formData["members"] = 0;
            // Save new diplomats
            if ($db->create("diplomats", $formData)) {
                $inserted_diplomat = $db->inset_id();
            }

            //Assign new members
            foreach ($temporal_member_details as $initial_member) {
                // Clear form_data  to prevent query failure
                // O-1
                foreach ($initial_member as $key => $value) {
                    if (empty($value)) {
                        unset($initial_member[$key]);
                    }
                }
                // Clear id to prevent update
                unset($initial_member["id"]);

                // Update head of the family to the new head
                $initial_member["head"] = $inserted_diplomat;

                // Database transaction
                $db->create("members", $initial_member);
                self::incrementNumberOfMembers($db, $inserted_diplomat, "members");
            }
            if (!empty($member_ids)) {
                // Clean temporal tables
                $db->query("delete FROM diplomats_temp_members WHERE members_id IN (" . implode(',', $member_ids) . ")");
            }

            if (!empty($found_temp_dep)) {
                $db->query("delete FROM diplomats_temp where id='" . $found_temp_dep["id"] . "'");
            }

            // Commit transaction
            $db->commit();
            return $inserted_diplomat;
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            var_dump($exception->getMessage() . json_encode($formData));
            die();
            $db->rollback();
        }
        return 0;
    }

    public static function addMember()
    {
        # code...
    }

    public static function editMemberInFamily($db, $id = 0, $formData = array(), $table = "members")
    {
        // Clear form_data  to preevent query failure
        foreach ($formData as $key => $value) {
            if (strlen($value) == 0) {
                unset($formData[$key]);
            }
        }
        // $formData["head_document_id"] = $formData["head_document_type"] == "ID" ?  $formData["head_document_id"] : $formData["head_document_passport"];
        // unset($formData["head_document_passport"]);
        return $db->update($table, "citizenId=" . $id, $formData);
    }
    /**
     * Validate from which head the visitor is coming from
     * @param $db
     * @param $pid
     * @param $family_name
     * @param $given_name
     * @param $other_name
     * @param $relationship
     * @param $member_id
     * @return bool
     */
    public  static  function validateVisitorOriginFamily($db, $head_document_id, $head_document_passport, $head_document_issue_country, $family_name, $given_name, $relationship, $header_to_member_relationship)
    {

        if ($relationship != self::RELATIONSHIP_VISITOR)
            return 0;

        if (!$head_document_id && !$head_document_passport)
            return 0;

        if ($head_document_id) {
            $head = $db->fetch("SELECT * FROM citizen where documentNumber='" . $head_document_id . "' limit 1");
        }
        if ($head_document_passport) {
            $head = $db->fetch("SELECT * FROM citizen where documentNumber=='" . $head_document_passport . "' and issuedCountry=" . $head_document_issue_country . " limit 1");
        }

        if (empty($head))
            return 0;

        $query = "SELECT * FROM citizen where familyId=" . $head[0]['citizenId'] . " and familyName='" . $family_name . "' and givenName='" . $given_name . "' and familyCategory='" . $header_to_member_relationship . "'";

        if (!empty($db->fetch($query)))
            return 1;

        // Try query flipped
        $query = "SELECT * FROM citizen  where familyId=" . $head[0]['citizenId'] . " and familyName='" . $given_name . "' and givenName='" . $family_name . "' and familyCategory='" . $header_to_member_relationship . "'";

        return empty($db->fetch($query)) ? -1 : 1;
    }

    /**
     * Function used to validate every member entry (Only duplicate identity)
     * @param $family_name
     * @param $given_name
     * @param $other_name
     * @param $relationship
     */
    public  static  function validateMemberNamesAndRelationship($db, $pid, $family_name, $given_name, $other_name, $relationship, $member_id)
    {

        if ($relationship != self::RELATIONSHIP_KID && $relationship != self::RELATIONSHIP_WIFE)
            return true;

        //Get their details
        if (!empty($member_id))
            $query = "SELECT * FROM citizen where familyId=" . $pid . " and familyName='" . $family_name . "' and givenName='" . $given_name . "' and familyCategory='" . $relationship . "' and citizenId<>" . $member_id . " limit 1";
        else
            $query = "SELECT * FROM citizen where familyId=" . $pid . " and familyName='" . $family_name . "' and givenName='" . $given_name . "' and familyCategory='" . $relationship . "' limit 1";

        if (!empty($db->fetch($query)))
            return false;
        // Try with names flipped
        //Get their details
        if (!empty($member_id))
            $query = "SELECT * FROM citizen where familyId=" . $pid . " and familyName='" . $given_name . "' and givenName='" . $family_name . "' and familyCategory='" . $relationship . "' and id<>" . $member_id . " limit 1";
        else
            $query = "SELECT * citizen where familyId=" . $pid . " and familyName='" . $given_name . "' and givenName='" . $family_name . "' and familyCategory='" . $relationship . "' limit 1";
        return empty($db->fetch($query));
    }

    public static function checkHeadOfFamily($db, $cond = '1')
    {
        $head = array();
        $query = "SELECT citizen.*,'citizen' as tb FROM citizen WHERE $cond ";
        $result = $db->query($query);
        while ($row = $db->fetch_array($result)) {
            array_push($head, $row);
        }
        return $head;
    }

    public static function checkMemberInFamily($db, $cond = '1')
    {
        $members = array();
        $query = "SELECT members.*,diplomats.location,'members' as tb
                  FROM members inner join diplomats on head=diplomats.id 
                  AND members.document_id AND $cond ";
        $result = $db->query($query);
        while ($row = $db->fetch_array($result)) {
            array_push($members, $row);
        }
        return $members;
    }

    public static function getMemberMovement($db, $cond = '1')
    {
        $query = "";
    }

    public static function getMember($db, $id = 0, $cond = "head")
    {
        $column = "citizenId='$id' ";
        $query = "SELECT * FROM citizen WHERE $column LIMIT 1";
        $resp = $db->query($query);
        return $db->fetch_array($resp);
    }

    /**
     * Add new member to a family
     * Adds a new member in the database
     * If member is a visitor and no id document is set, a temporarily family head is created
     * @param $db
     * @param array $formData
     * @return int
     */
    public static function addMemberInFamily($db, $formData = array(), $visitorFoundInParentFamily = false)
    {
        $temp_dep = [];
        $temp_relationship = null;
        // Check if there is a need to initiate head of family (temp)
        if ($formData["relationship"] == self::RELATIONSHIP_VISITOR && $formData["type"] == self::DOCUMENT_TYPE_NONE && !$visitorFoundInParentFamily) {
            //build form-data for
            $temp_relationship = $formData["head_to_member_relationship"];
            $temp_dep = array_merge($temp_dep, [
                "document_id" => $formData["head_document_type"] == "ID" ?  $formData["head_document_id"] : $formData["head_document_passport"],
                "issued_country" => $formData["head_document_issue_country"],
                "issued_date" => $formData["head_document_issue_date"],
                "expiry_date" => $formData["head_document_expiry_date"],
                "type" => $formData["head_document_type"],
                "user" => $formData["user"]
            ]);
        }

        $formData["head_document_id"] = $formData["head_document_type"] == "ID" ?  $formData["head_document_id"] : $formData["head_document_passport"];
        unset($formData["head_document_passport"]);

        // Begin all processes in one db transaction
        $db->beginTransaction();

        $inserted_member_id = null;

        try {

            foreach ($formData as $key => $value) {
                if (strlen($value) == 0) {
                    unset($formData[$key]);
                }
            }


            if ($db->create("members", $formData)) {
                $inserted_member_id = $db->inset_id();
            }


            if (!empty($temp_dep) && !$visitorFoundInParentFamily) {

                foreach ($temp_dep as $key => $value) {
                    if (empty($value)) {
                        unset($temp_dep[$key]);
                    }
                }

                // check if current the current head of family has some temp records
                if ($temp_dep["type"] == "ID") {
                    $query = "select * from diplomats_temp where document_id='" . $temp_dep["document_id"] . "' and type='" . $temp_dep["type"] . "'";
                } else {
                    $query = "select * from diplomats_temp where document_id='" . $temp_dep["document_id"] . "' and type='" . $temp_dep["type"] . "' and issued_country=" . $temp_dep["issued_country"];
                }

                $rows = $db->fetch($query);
                $found_temp_dep = isset($rows[0]) ? $rows[0] : null;
                $found_temp_dep_id = (!empty($found_temp_dep) && isset($found_temp_dep["id"])) ? $found_temp_dep["id"] : null;

                if (is_null($found_temp_dep_id) && $db->create("diplomats_temp", $temp_dep)) {
                    $found_temp_dep_id = $db->inset_id();
                }

                $member_already_exists = null;
                if (!is_null($found_temp_dep_id)) {
                    // fetch member ids
                    $registered_dep_members = $db->fetch("select * from diplomats_temp_members where diplomats_temp_id='" . $found_temp_dep_id . "'");
                    $member_ids = array_map(function ($item) {
                        return $item["members_id"];
                    }, $registered_dep_members);

                    if (!empty($member_ids)) {

                        //Get their details
                        $query = "SELECT * FROM members WHERE id IN (" . implode(',', $member_ids) . ") and given_name='"
                            . $formData["given_name"] . "' and family_name='" . $formData["family_name"] . "'";

                        !empty($formData["other_name"]) ? $query .= " and other_name='" . $formData["other_name"] . "'" : null;

                        $member_already_exists = $db->fetch($query);
                    }
                }

                if (empty($member_already_exists) && $db->create("diplomats_temp_members", [
                    "diplomats_temp_id" => $found_temp_dep_id,
                    "members_id" => $inserted_member_id,
                    "user" => $formData["user"],
                    "relationship" => $temp_relationship,
                ])) {
                    $inserted_temp_dep = $db->inset_id();
                }
            }

            $db->commit();
            return $inserted_member_id;
        } catch (Exception $exception) { // An error occurred in one of our db queries. We cancel the whole transaction
            error_log($exception->getMessage());
            var_dump($exception->getMessage());
            die();
            $db->rollback();
            return 0;
        }
    }

    public static function editNumberOfFamily($db, $id = 0, $formData = array())
    {
        return $db->update("diplomats", "id=" . $id, $formData);
    }


    public static function incrementNumberOfMembers($db, $id = 0, $formData = array())
    {
        return $db->incrementColumn("diplomats", "id=" . $id, "members");
    }

    // add support to family
    public static function addSupportOnFamily($db, $formData = array())
    {
        try {

            if ($db->create("help", $formData)) {
                return $db->inset_id();
            }
            return 0;
        } catch (Exception $exception) {
            var_dump($exception->getMessage());
            die();
        }
    }
    // add movement to migrant
    public static function addMigration($db, $formData = array(), $destination)
    {

        if ($destination == "diplomats" && $formData["migrant_type"] == "HEAD") {
            try {
                if ($db->create("track_movement", $formData)) {
                    return $db->inset_id();
                }
                return 0;
            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
            }
        }

        if ($destination == "members" && $formData["migrant_type"] == "MEMBER") {
            try {
                if ($db->create("track_movement", $formData)) {
                    return $db->inset_id();
                }
                return 0;
            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
            }
        }

        return 1;
    }

    // get movement to migrant
    public static function getAllMovement($db, $id, $type)
    {
        $movements = array();
        $query = "SELECT * FROM track_movement WHERE migrant_id=" . $id . " and migrant_type='" . $type . "'";
        $result = $db->query($query);
        while ($row = $db->fetch_array($result)) {
            array_push($movements, $row);
        }
        return $movements;
    }
    // get help given to family
    public static function getFamilyHelp($db, $family_id = 0)
    {
        $query = "SELECT * FROM help WHERE family =$family_id AND status='1'";
        return self::loopData($db, $db->query($query));
    }

    // get all support
    public static function getAllSupport($db, $loc, $limit = "0")
    {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($limit == "0") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                    SELECT l FROM (SELECT $c from help h 
                     INNER JOIN diplomats d on h.family=d.id AND  d.status='1'  GROUP BY h.family ) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        } else {
            $query = "SELECT id,head,help,giver,dob,family,names,document_id,l FROM(
                    SELECT h.id , d.id as head,h.help,h.giver,d.dob,h.family, concat(d.given_name,' ',d.family_name) as names,d.document_id $column from help h 
                    INNER JOIN diplomats d ON h.family=d.id AND d.status='1'  GROUP BY h.family) as data WHERE {$l['value']} $limit ";
            return self::loopData($db, $db->query($query));
        }
    }
    // get all population
    public static function getAllPeople($db, $loc, $limit = "0", $cond = "")
    {
        $is_village = self::checkIfIsVillage($loc);
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($limit == "0") {
            if ($is_village) {
                $cond = ltrim($cond, "WHERE");
                $cond = " WHERE d.currentLocation='$is_village' AND " . $cond;
                return $db->fetch_array($db->query("SELECT count(*) as total FROM citizen d $cond "))["total"];
                exit(0);
            }
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM citizen d $cond ) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        } else {
            $query = "SELECT  id,givenName,familyName,otherName,
                    documentNumber,dob,l,tb from (SELECT 
                    d.citizenId as id,d.givenName,d.familyName,d.otherName,
                    d.documentNumber,d.dob $column,is_family_heading as tb from citizen d $cond  
                    ) as data WHERE {$l['value']}  ORDER BY id DESC  $limit";
            if ($is_village) {
                $cond = ltrim($cond, "WHERE ");
                $cond = " WHERE d.currentLocation='$is_village' AND " . $cond;
                $query = "SELECT 
                    d.citizenId as id,d.givenName,d.familyName,d.otherName,
                    d.documentNumber,d.dob,is_family_heading as tb from citizen d $cond ORDER BY id DESC  $limit";
            }
            return $db->query($query);
        }
    }

    public static function getHeadOfFamily($db, $loc = "", $limit = "0", $cond = "")
    {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        $is_village = self::checkIfIsVillage($loc);
        if ($limit == "0") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM citizen d WHERE d.is_family_heading='1' $cond)
                         as data WHERE {$l['value']}) as total";
            if ($is_village) {
                $query = "SELECT count(*) as total FROM citizen d WHERE d.currentLocation='$is_village' AND  d.is_family_heading='1' $cond";
            }
            return $db->fetch_array($db->query($query))["total"];
        } else {
            $query = "SELECT id,givenName,familyName,otherName,
                        documentNumber,dob,l,tb from (SELECT 
                        d.citizenId as id,d.givenName,d.familyName,d.otherName,
                        d.documentNumber,d.dob $column,d.is_family_heading as tb from citizen d 
                        where d.is_family_heading='1' $cond                     
            ) as data WHERE {$l['value']} ORDER BY id DESC  $limit";
            if ($is_village) {
                $query = "SELECT 
                        d.citizenId as id,d.givenName,d.familyName,d.otherName,
                        d.documentNumber,d.dob,d.is_family_heading as tb from citizen d 
                        WHERE d.currentLocation='$is_village' AND  d.is_family_heading='1' $cond  ORDER BY id DESC  $limit";
            }
            return $db->query($query);
        }
    }

    // get family single help
    public static function getHelpById($db, $id = 0)
    {
        $query = "SELECT * FROM help WHERE id =$id AND status='1' LIMIT 1";
        return $db->fetch_array($db->query($query));
    }

    public static function getFamilyMembers($db, $head_id = 0, $limit = "0"): array
    {
        $query = "SELECT 
                    m.id,m.given_name,m.family_name,m.other_name,
                    M.document_id,m.dob,from members m  inner JOIN diplomats d 
                    on m.head=d.id AND  m.status='1'                     
                    ORDER BY id DESC  $limit";
        return self::loopData($db, $db->query($query));
    }

    public static function loopData($db, $result = array())
    {
        $output = array();
        while ($row = $db->fetch_array($result)) {
            array_push($output, $row);
        }
        return $output;
    }

    // get exactly location
    public static function searchLocation($loc = "0#0#0#0#0#")
    {
        $arr = explode("#", $loc);
        $c = 0;
        $v = 0;
        foreach ($arr as $key => $val) {
            if (!empty($val)) {
                $c++;
                $v = $val;
            }
        }
        switch ($c) {
            case 1:
                return ["column" => "SUBSTRING_INDEX(d.location, '#', 1) AS l", "value" => "l='$v'"];
                break;
            case 2:
                return ["column" => "SUBSTRING_INDEX(SUBSTRING_INDEX(d.location, '#', 2), '#', -1) AS l", "value" => "l='$v'"];
                break;
            case 3:
                return ["column" => "SUBSTRING_INDEX(SUBSTRING_INDEX(d.location, '#', 3), '#', -1) AS l", "value" => "l='$v'"];
                break;
            case 4:
                return ["column" => "SUBSTRING_INDEX(SUBSTRING_INDEX(d.location, '#', 4), '#', -1) AS l", "value" => "l='$v'"];
                break;
            case 5:
                return ["column" => "SUBSTRING_INDEX(SUBSTRING_INDEX(d.location, '#', 5), '#', -1) AS l", "value" => "l='$v'"];
                break;
            default:
                return ["column" => "", "value" => "1"];
                break;
        }
    }
    // find people
    public static function find(
        $db,
        $cond = ["m" => 1, "d" => 1],
        $loc = "#",
        $table = "all",
        $limit = "LIMIT 30 "
    ) {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($table == "all") {
            $query = "SELECT id,givenName,familyName,otherName,
                    documentNumber,dob,l,tb FROM (SELECT 
                    d.citizenId as id,d.givenName,d.familyName,d.otherName,
                    d.documentNumber,d.dob $column,is_family_heading as tb from citizen d where 1
                    AND $cond                     
                    ) as data WHERE {$l['value']}  ORDER BY id DESC  $limit";
        } else if ($table == "diplomats") {
            $query = "SELECT 
                    d.id,d.givenName,d.familyName,d.otherName,
        d.documentNumber,d.dob $column,is_family_heading as tb FROM citizen d WHERE 1 AND $cond ORDER BY id DESC  LIMIT 30";
        } else if ($table == "counting") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM citizen d WHERE 1 AND $cond) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        }
        return $db->query($query);
        //  return self::loopData($db, $db->query($query));
    }
    // calculate number on dashboard
    public static function getTotal($db, $table, $cond = '')
    {
        $query = "SELECT count(*) as total FROM $table $cond ";
        return $db->fetch_array($db->query($query))["total"];
    }
    // total related to the people
    public static function getTotalPeople($db, $loc, $gender = "male", $join = false)
    {
        $is_village = self::checkIfIsVillage($loc);
        if ($is_village) {
            $query = "SELECT count(*) as total FROM citizen d WHERE currentLocation='$is_village' AND d.gender like '$gender%' ";
            return $db->fetch_array($db->query($query))["total"];
            exit(0);
        }
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        $c = ltrim($column, ",");
        $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM citizen d WHERE  
                           d.gender like '$gender%' )
                         as data WHERE {$l['value']}) as total";
        return $db->fetch_array($db->query($query))["total"];
    }

    /**
     * Get the value of db
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set the value of db
     *
     * @return  self
     */
    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }
    //  add head of family in new table called citizen

    public static function addFamilyHead($db, $formData, $table = "citizen")
    {
        if ($db->create($table, $formData)) {
            return  $db->inset_id();
        }
        return 0;
    }
    // SAVE VISITOR
    public static function addVisitorInFamily($db, $formData, $table = "visitors")
    {
        if ($db->create($table, $formData)) {
            return  $db->inset_id();
        }
        return 0;
    }
    // get visitor located in location
    public static function getVisitors($db, $loc, $limit = 0, $searchInCitzen = "", $cond = "WHERE d.status='1' AND d.departure_date>=curdate()")
    {
        $l = self::searchLocation($loc);
        $is_village = self::checkIfIsVillage($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($limit == "0") {
            if ($is_village) {
                $cond = ltrim(strtolower($cond), "where");
                $cond = " WHERE d.village='$is_village' AND " . $cond;
                return $db->fetch_array($db->query("SELECT count(*) as total FROM visitors d $cond "))["total"];
                exit(0);
            }
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM visitors d $cond ) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        } else {
            // get visitors ids
            $query = "SELECT  * from (SELECT 
                    d.visited_by,d.resident_id,d.arrival_date,d.departure_date,
                    d.id,d.updated_at
                    $column from visitors d  $cond    
                    ) as data WHERE {$l['value']}    $limit";
            if ($is_village) {
                $cond = ltrim(strtolower($cond), "where");
                $cond = " WHERE d.village='$is_village' AND " . $cond;
                $query = "SELECT d.visited_by,d.resident_id,d.arrival_date,d.departure_date,d.id,d.updated_at from visitors d  $cond  $limit";
            }
            $vistors = array();
            $result = $db->query($query);
            while ($value = $db->fetch_array($result)) {
                $visted_by = $db->fetch("select familyName,givenName,documentNumber,citizenId,dob,otherName,is_family_heading as tb
               FROM citizen  where citizenId='{$value["visited_by"]}' $searchInCitzen ");
                // get who visited
                $resident = $db->fetch("
                select familyName,givenName,documentNumber,citizenId,dob,otherName,is_family_heading as tb
               FROM citizen  where citizenId='{$value["resident_id"]}' $searchInCitzen ");
                $value["visitor"] = isset($visted_by[0]) ? $visted_by[0] : '';
                $value["resident"] = isset($resident[0]) ? $resident[0] : '';
                array_push($vistors, $value);
            }
            return $vistors;
        }
    }
    // get current visitors in family
    public static function getCurrentVisitorsInFamily($db, $family_id = 0)
    {
        $today = date("Y-m-d");
        $query = "SELECT c.documentNumber as document_id,c.otherName,c.is_family_heading as tb, c.citizenId as id,v.id as visitor_id,
                    c.familyCategory,c.familyName,c.givenName FROM visitors v INNER JOIN citizen c on v.visited_by=c.citizenId AND v.resident_id='$family_id' 
                    AND v.status='1' AND v.departure_date >='$today'";
        // check if exist
        // return $query;
        return $db->query($query);
        // return self::loopData($db, );
    }
    public static function familyHasVisitor($db, $family_id = 0)
    {
        $today = date("Y-m-d");
        $cond = "where resident_id='$family_id' AND status='1' AND departure_date >='$today'";
        return self::getTotal($db, "visitors", $cond);
    }
    public static function getHeadOfFamilyByDocument($db, $document = 0)
    {
        $query = "SELECT * FROM citizen where documentNumber='$document' limit 1";
        // return result
        return $db->query($query);
    }
    public static function getMembersInFamily($db, $family_id = 0)
    {
        return $db->query("SELECT * FROM citizen WHERE  familyId='$family_id'");
    }
    // get movement was made
    public static function getMovementById($db, $id = 0, $limit = "")
    {
        // get names /id,location,reason
        $query = "SELECT movementType as reason ,location,created_at as time from history where citizenId='$id' $limit";
        return $db->query($query);
    }
    // get suggestion in order to minimize duplicate
    public static function getSuggestions($db, $keywords = "", $limit = " limit  30")
    {
        $query = "SELECT * from citizen WHERE key_words like '$keywords%' $limit";
        return $db->query($query);
    }
    // get citizen by id
    public static function getCitizenById($db, $id = 0)
    {
        return $db->fetch_array($db->query("SELECT * FROM citizen WHERE citizenId='$id'"));
    }
    //  get villages located in region
    public static function getVillagesInLocation($db, $location, $column = "currentLocation")
    {
        $codes = explode("#", $location);
        //check if is array
        if (!is_array($codes)) return `0`;
        $len = count($codes);
        $sql = "";
        switch ($len) {
            case 6:
                return $column . "='$codes[4]'";
                break;
            case 5:
                $sql = " SELECT GROUP_CONCAT(v.id) as ids FROM village v  INNER JOIN cell c on v.cell_id=c.id   WHERE c.id=$codes[3]";
                break;
            case 4:
                $sql = " SELECT GROUP_CONCAT(v.id) as ids FROM village v  INNER JOIN cell c on v.cell_id=c.id INNER JOIN  sectors s on c.sector_id=s.id  WHERE s.id=$codes[2]";
                break;
            case 3:
                $sql = " SELECT GROUP_CONCAT(v.id) as ids FROM village v  INNER JOIN cell c on v.cell_id=c.id INNER JOIN  sectors s on c.sector_id=s.id INNER JOIN districts d on s.district_id=d.id WHERE d.id=$codes[1]";
                break;
            case 2:
                $sql = " SELECT GROUP_CONCAT(v.id) as ids FROM village v  INNER JOIN cell c on v.cell_id=c.id INNER JOIN  sectors s on c.sector_id=s.id INNER JOIN districts d on s.district_id=d.id 
                INNER JOIN provinces p on d.province_id=p.id WHERE p.id=$codes[0]";
                if ($codes[0] == 0)  return " 1";
                break;
            default:
                return " 1";
                break;
        }
        $data = $db->fetch($sql);
        $ids = $data[0]['ids'];
        $fields = explode(",", $ids);
        asort($fields);
        return $column . " IN   ('" . implode('\',\'', $fields) . "')";
    }
    // optimized query
    public static function checkIfIsVillage($location = "0")
    {
        $codes = explode("#", $location);
        if (count($codes) == 6) return $codes[4];
        return 0;
    }
}
