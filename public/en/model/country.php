<?php

/**
 * undocumented class
 * this class deal with country table
 */
class Country
{
    private  function __contruct()
    {
    }
    // get countries
    public static function getAllCountry($db, $cond = '1'): array
    {
        $lists = array();
        $query = "SELECT id,name FROM countries ORDER BY id ASC";
        $res = $db->query($query);
        while ($row = $db->fetch_array($res)) {
            array_push($lists, $row);
        }
        return $lists;
    }
}
