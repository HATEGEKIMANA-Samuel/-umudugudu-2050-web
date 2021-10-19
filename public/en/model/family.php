<?php
// model related to family
class family
{
    private $db;

    private function __construct($database = null)
    {
        # code...
        $this->db = $database;
    }
    // function used to add head of family
    public static function addHeadOfFamily($db, $formData = array())
    {
        if ($db->create("diplomats", $formData)) {
            return $db->inset_id();
        }
        return 0;
    }
    public static function addMember()
    {
        # code...
    }
    public static function editMemberInFamily($db, $id = 0, $formData = array(), $table = "members")
    {
        return $db->update($table, "id=" . $id, $formData);
    }


    public static function checkHeadOfFamily($db, $cond = '1')
    {
        $head = array();
        $query = "SELECT diplomats.*,'diplomats' as tb FROM diplomats WHERE $cond ";
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
    public static function getMember($db, $id = 0, $table = "diplomats")
    {
        $query = "SELECT * FROM $table WHERE id = $id  AND status='1' LIMIT 1";
        $resp = $db->query($query);
        return $db->fetch_array($resp);
    }
    public static function addMemberInFamily($db, $formData = array())
    {
        if ($db->create("members", $formData)) {
            return $db->inset_id();
        }
        return 0;
    }
    public static function editNumberOfFamily($db, $id = 0, $formData = array())
    {
        return $db->update("diplomats", "id=" . $id, $formData);
    }
    // add support to family

    public static function addSupportOnFamily($db, $formData = array())
    {
        if ($db->create("help", $formData)) {
            return $db->inset_id();
        }
        return 0;
    }

    // add movement to migrant
    public static function addMigration($db, $formData = array())
    {
        if ($db->create("track_movement", $formData)) {
            return $db->inset_id();
        }
        return 0;
    }
    // get movement to migrant
    public static function getAllMovement($db, $cond = "1")
    {
        $movements = array();
        $query = "SELECT * FROM track_movement WHERE $cond ORDER BY id desc";
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

    // get family single help
    public static function getHelpById($db, $id = 0)
    {
        $query = "SELECT * FROM help WHERE id =$id AND status='1' LIMIT 1";
        return $db->fetch_array($db->query($query));
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
    public static function getAllPeople($db, $loc, $limit = "0")
    {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($limit == "0") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM diplomats d WHERE d.status='1') as data WHERE {$l['value']}
                    UNION ALL
                    SELECT l FROM (SELECT $c from members m  inner JOIN diplomats d on m.head=d.id AND  m.status='1' ) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        } else {
            $query = "SELECT id,given_name,family_name,other_name,
                    document_id,dob,l,tb from (SELECT 
                    d.id,d.given_name,d.family_name,d.other_name,
                    d.document_id,d.dob $column,'d' as tb from diplomats d where d.status='1'
                UNION ALL
                SELECT 
                    m.id,m.given_name,m.family_name,m.other_name,
                    m.document_id,m.dob $column,'m' as tb from members m  inner JOIN diplomats d on m.head=d.id AND  m.status='1'                     
                    ) as data WHERE {$l['value']}  ORDER BY id DESC  $limit";
            //return self::loopData($db, $db->query($query));
            // return result
            return $db->query($query);
            // return $query;
        }
    }
    public static function getHeadOfFamily($db, $loc = "", $limit = "0")
    {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($limit == "0") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM diplomats d WHERE d.status='1')
                         as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        } else {
            $query = "SELECT id,given_name,family_name,other_name,
                        document_id,dob,l,tb from (SELECT 
                        d.id,d.given_name,d.family_name,d.other_name,
                        d.document_id,d.dob $column,'d' as tb from diplomats d where d.status='1'                     
            ) as data WHERE {$l['value']} ORDER BY id DESC  $limit";
            return $db->query($query);
        }
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
    public static function find($db, $cond = ["m" => 1, "d" => 1], $loc = "#", $table = "all", $limit = "LIMIT 30 ")
    {
        $l = self::searchLocation($loc);
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        if ($table == "all") {
            $query = "SELECT id,given_name,family_name,other_name,
                    document_id,dob,l,tb FROM (SELECT 
                    d.id,d.given_name,d.family_name,d.other_name,
                    d.document_id,d.dob $column,'d' as tb from diplomats d where d.status='1' AND {$cond["d"]}
                UNION ALL
                SELECT 
                    m.id,m.given_name,m.family_name,m.other_name,
                    m.document_id,m.dob $column,'m' as tb from members m  
                    inner JOIN diplomats d on m.head=d.id AND  m.status='1' AND {$cond["m"]}                     
                    ) as data WHERE {$l['value']}  ORDER BY id DESC  $limit";
        } else if ($table == "diplomats") {
            $query = "SELECT id,given_name,family_name,other_name,
                    document_id,dob,l,tb FROM (SELECT 
                    d.id,d.given_name,d.family_name,d.other_name,
        d.document_id,d.dob $column,'d' as tb FROM diplomats d WHERE d.status='1' AND {$cond["d"]} ORDER BY id DESC  LIMIT 30";
        } else if ($table == "counting") {
            $c = ltrim($column, ",");
            $query = "SELECT count(*) as total FROM (
                        SELECT l FROM (SELECT $c FROM diplomats d WHERE d.status='1' AND {$cond["d"]}) as data WHERE {$l['value']}
                    UNION ALL
                    SELECT l FROM (SELECT $c from members m  inner JOIN diplomats d on m.head=d.id AND  m.status='1' AND {$cond["m"]}) as data WHERE {$l['value']}) as total";
            return $db->fetch_array($db->query($query))["total"];
        }
        return self::loopData($db, $db->query($query));
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
}
