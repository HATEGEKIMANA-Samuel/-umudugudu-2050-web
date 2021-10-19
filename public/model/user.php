<?php
class user
{

    private function __construct($database = null)
    {
        $this->db = $database;
    }
    public static function getUserLocation($db, $id, $columns = " id,province", $table = "provinces")
    {
        $query = "SELECT $columns FROM $table where id=$id limit 1";
        return $db->fetch_array($db->query($query));
    }
    public static function decodeLocation($db, $hashedLocation = "0#0#0#0#0#")
    {
        $arr = explode("#", $hashedLocation);
        $arr[0] = isset($arr[0]) ? $arr[0] : 0;
        $arr[1] = isset($arr[1]) ? $arr[1] : 0;
        $arr[2] = isset($arr[2]) ? $arr[2] : 0;
        $arr[3] = isset($arr[3]) ? $arr[3] : 0;
        $arr[4] = isset($arr[4]) ? $arr[4] : 0;
        $query = "SELECT id,name,level FROM(
                            SELECT id,province as name,'p' as level FROM provinces WHERE id=$arr[0]
                            UNION all
                            SELECT id,district as name ,'d' as level FROM districts WHERE id=$arr[1]
                            UNION all
                            SELECT id,sector as name,'s' as level FROM sectors WHERE id=$arr[2]
                            UNION all
                            SELECT id,name,'c' as level FROM cell WHERE id=$arr[3]
                            UNION all
                            SELECT id,name,'v' as level FROM village WHERE id=$arr[4]
            ) as data";
        $locs = array();
        $resp = $db->query($query);
        while ($row = $db->fetch_array($resp)) {
            array_push($locs, $row);
        }
        return $locs;
    }
    public static function isSet($arr)
    {
        return isset($arr) ? $arr : 0;
    }
    public static function getLocationName($db, $hashedLocation = "0#0#0#0#0#")
    {
        $arr = explode("#", $hashedLocation);
        $query = "SELECT name,level FROM(
                    SELECT province as name,'p' as level FROM provinces WHERE id=$arr[0]
                    UNION all
                    SELECT district as name ,'d' as level FROM districts WHERE id=$arr[1]
                    UNION all
                    SELECT sector as name,'s' as level FROM sectors WHERE id=$arr[2]
                    UNION all
                    SELECT name,'c' as level FROM cell WHERE id=$arr[3]
                    UNION all
                    SELECT name,'v' as level FROM village WHERE id=$arr[4]
                    ) as data";
        $locs = ["province" => 0, "district" => 0, "sector" => 0, "cell" => 0, "village" => 0];
        $resp = $db->query($query);
        while ($row = $db->fetch_array($resp)) {
            if ($row["level"] == "p") {
                $locs["province"] = $row["name"];
            } else if ($row["level"] == "d") {
                $locs["district"] = $row["name"];
            } else if ($row["level"] == "s") {
                $locs["sector"] = $row["name"];
            } elseif ($row["level"] == "c") {
                $locs["cell"] = $row["name"];
            } elseif ($row["level"] == "v") {
                $locs["village"] = $row["name"];
            }
        }
        return $locs;
    }
}
