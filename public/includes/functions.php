<?php
function rand_string($length)
{
    //$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = '';
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $size = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $size - 1)];
    }
    return $str;
}
/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * 
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */
function encrypt_decrypt($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '@Secrety key PMS';
    $secret_iv = '@Secrety key PMS iv';
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
function lang($txt)
{
    switch ($txt) {
        case 'Wife':
            return "Umufasha";
            break;
        case 'Kid':
            return "Umwana";
            break;
        case 'brother':
            return "Ubuvandimwe";
            break;
        case 'friend':
            return "Ubushuti";
            break;
        case 'House worker':
            return "Umukozi";
            break;
        case 'HEAD':
            return "umukuru w'umuryango";
            break;
        default:
            return $txt;
            break;
    }
}
function getNotif($user = '', $action = '', $location = "", $limit = " limit 10")
{
    global $database;
    if ($action == 'numb') {
        $sql = "SELECT count(*) AS nbr FROM sec_notification sn WHERE sn.id NOT IN(select snu.notification_id FROM sec_notification_user snu WHERE snu.user_id=$user) AND sn.location LIKE '$location%'";
        $res = $database->fetch_array(
            $database->query($sql)
        );
        return $res['nbr'];
    } elseif ($action == 'getreal') {
        $sql = "SELECT sn.* FROM sec_notification sn WHERE sn.id NOT IN(select snu.notification_id FROM sec_notification_user snu WHERE snu.user_id=$user) AND sn.location LIKE '$location%' $limit";
        $res = $database->query($sql);
        $links = "";
        while ($not = $database->fetch_array($res)) {
            $url = explode("#", $not["link"]);
            $hash = isset($url[1]) ? '#' . $url[1] : "";
            $ref = $url[0] . '&nt=' . input::enc_dec('e', $not['id']) . $hash;
            $links .= '<a style="border-bottom: 1px solid #eee" class="dropdown-item notification-notice mt-0 media fs-12" href="' . $ref . '" >
            <p class="fs-14" style="padding: 8px 15px 8px 0 !important; ">
            <img src="./images/bell.png" height="20" class="pr-10">  
            ' . $not["action"] . '</p>
        </a>';
        }
        return $links;
    }
}
function getLocationNameFromCode($db, $hashedLocation = "0#0#0#0#0#")
{
    $arr = explode("#", $hashedLocation);
    $p = isset($arr[0]) ? $arr[0] : 0;
    $d = isset($arr[1]) ? $arr[1] : 0;
    $s = isset($arr[2]) ? $arr[2] : 0;
    $c = isset($arr[3]) ? $arr[3] : 0;
    $v = isset($arr[4]) ? $arr[4] : 0;
    $query = "SELECT name,level FROM(SELECT province as name,'p' as level FROM provinces WHERE id=$p UNION all
                    SELECT district as name ,'d' as level FROM districts WHERE id=$d
                    UNION all SELECT sector as name,'s' as level FROM sectors WHERE id=$s
                    UNION all SELECT name,'c' as level FROM cell WHERE id=$c
                    UNION all SELECT name,'v' as level FROM village WHERE id=$v) as data";
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
function make_password($password, $iterations = 36000, $algorithm = 'sha256')
{
    $salt = base64_encode(openssl_random_pseudo_bytes(9));

    $hash = hash_pbkdf2($algorithm, $password, $salt, $iterations, 32, true);

    return 'pbkdf2_' . $algorithm . '$' . $iterations . '$' . $salt . '$' . base64_encode($hash);
}
function verify_Password($dbString, $password)
{
    try {
        $pieces = explode("$", $dbString);
        $iterations = isset($pieces[1]) ? $pieces[1] : 1;
        $salt = isset($pieces[2]) ? $pieces[2] : 2;
        $old_hash = isset($pieces[3]) ? $pieces[3] : 3;
        $hash = hash_pbkdf2("SHA256", $password, $salt, $iterations, 0, true);
        $hash = base64_encode($hash);

        if ($hash == $old_hash) {
            // login ok.
            return true;
        } else {
            //login fail       
            return false;
        }
    } catch (\Throwable $e) {
        return false;
    }
}
// get level name from id
function getLevelName($level_id)
{
    switch ($level_id) {
        case 1:
            return "admin";
            break;
        case 2:
            return "Umudugudu";
            break;
        case 3:
            return "Akagari";
            break;
        case 4:
            return "Umurenge";
            break;
        case 5:
            return "Akarere";
            break;
        case 6:
            return "Intara";
            break;
        case 7:
            return "Minaloc";
            break;
        default:
            break;
    }
}
