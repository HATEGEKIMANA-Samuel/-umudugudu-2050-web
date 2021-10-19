<?php
class Statistic
{
    private $db;

    private function __construct($db = null)
    {
        $this->db = $db;
    }
    // to count
    public static function getNumbering($db, $cond = "1", $table = "diplomats")
    {
        $query = " SELECT COUNT(id) as total FROM  $table WHERE $cond LIMIT 1";
        $result = $db->query($query);
        return $db->fetch_array($result)["total"];
    }
    // get summary 
    public static function getSampleStatisticInfo($db, $cond = array(), $loc = "#")
    {
        $l = self::searchLocation($loc);
        $cond2 = $cond["d"];
        $cond3 = $cond["m"];
        $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
        $query = "SELECT id,names,tb,level_education,dob,l FROM (
   select d.id,concat(d.given_name,' ',d.family_name,'/',d.document_id) as names,'d' as tb,
   d.level_education,d.dob $column FROM diplomats d WHERE $cond2
  UNION all
  select m.id,concat(m.given_name,' ',m.family_name,'/',m.document_id) as names,'m' as tb,
  m.level_education,m.dob $column FROM members m  inner JOIN diplomats d on m.head=d.id WHERE $cond3 AND m.status='1'
)as data where {$l['value']}";
        $res = $db->query($query);
        $data = array();
        while ($row = $db->fetch_array($res)) {
            array_push($data, $row);
        }
        return $data;
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
}
