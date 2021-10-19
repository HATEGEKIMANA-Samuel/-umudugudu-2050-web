<?php
class Log
{
    private function __construct()
    {
    }
    // store logs
    public static function create($db, $table = "user_logs", $formData = array())
    {
        return $db->create($table, $formData);
    }
    // display logs
    public static function getLogs($db, $table = "user_logs", $cond = "1"): array
    {
        $lists = array();
        $query = "SELECT * FROM $table WHERE $cond ORDER BY id  DESC";
        $result = $db->query($query);
        while ($row = $db->fetch_array($result)) {
            array_push($lists, $row);
        }
        return $lists;
    }
    public static function updateLog($db, $id = 0, $table = "user_logs", $formData = array())
    {
        return $db->update($table, "id=" . $id, $formData);
    }
    public static function deleteLog($db, $table = "user_logs", $cond = "1")
    {
        $query = "DELETE FROM $table WHERE $cond";
        $db->query($query);
        return $db->affected_rows();
    }
}
