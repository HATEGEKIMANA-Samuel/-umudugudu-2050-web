<?php
class umutekano
{
    private function  __construct()
    {
    }
    public static function getTotal($db, $cond = "1", $table = "security")
    {
        $query = "SELECT count(*) as total FROM $table WHERE $cond";
        return $db->fetch_array($db->query($query))["total"];
    }
    public static function getCases($db, $cond = "1", $table = "security")
    {
        $query = "SELECT * FROM $table WHERE $cond";
        return $db->query($query);
    }
    public static function getCaseById($db, $id, $table = "security")
    {
        return $db->query("SELECT * FROM $table WHERE id=$id");
    }
    public static function createFeedBack($db, $formData = array(), $table = "security_feedback")
    {

        if ($db->create($table, $formData)) {
            // get user names
            $user_id = $formData["created_by"];
            $formData["last_id"] = $db->inset_id();
            $formData["user_names"] = $db->fetch_array(
                $db->query("SELECT CONCAT(lname,' ',fname) as names  from user WHERE id=$user_id")
            )["names"];

            return $formData;
        } else {
            return 0;
        }
    }
    public static function getFeedBack($db, $cond = "WHERE 1", $limit = " limit 20")
    {
        $query = " SELECT feedback,created_at,created_by,id,
        (SELECT CONCAT(lname,' ',fname) as names  FROM user u where u.id=f.created_by) as author FROM security_feedback f $cond  ORDER BY f.id desc $limit";
        return $db->query($query);
    }
    public static  function getLocationName($db, $hashedLocation = "0#0#0#0#0#", $sep = ",")
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
        $places = "";
        $resp = $db->query($query);
        while ($row = $db->fetch_array($resp)) {
            if ($row["level"] == "p") {
                $places = $row["name"] . "$sep";
            } else if ($row["level"] == "d") {
                $places .= $row["name"] . "$sep";
            } else if ($row["level"] == "s") {
                $places .= $row["name"] . "$sep";
            } elseif ($row["level"] == "c") {
                $places .= $row["name"] . "$sep";
            } elseif ($row["level"] == "v") {
                $places .= $row["name"] . "$sep";
            }
        }
        return rtrim($places, "$sep");
    }
}
