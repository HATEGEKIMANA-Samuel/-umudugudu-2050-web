<?php
function rand_string($length) {
	//$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$str ='';
	$chars = "abcdefghijklmnopqrstuvwxyz0123456789";	
	$size = strlen($chars);
	for( $i = 0; $i < $length; $i++ ) {
		$str .= $chars[ rand( 0, $size - 1 ) ];
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
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '@Secrety key PMS';
    $secret_iv = '@Secrety key PMS iv';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
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
        default:
            return $txt;
            break;
    }
}
function getNotif($user='',$action='')
{
    global $database;
    if ($action=='numb') {
        $res = $database->fetch_array($database->query("SELECT count(id) AS nbr FROM notification"));
        return $res['nbr'];
    }elseif ($action=='getreal') {
        $res = $database->query("SELECT * FROM notification");
        while ($not = $database->fetch_array($res)) {
            if ($not['action_type']=='jpc') {
                $ref = 'displayjpcc?jpc='.rawurlencode(encrypt_decrypt('encrypt', $not['action_id'])).'%info';
            }elseif ($not['action_type'] == 'jpc-decision') {
                $res1 = $database->fetch_array($database->query("SELECT jpc_meet FROM decisions WHERE id='{$not['action_id']}' LIMIT 1"));
                $ref = 'displayjpcc?jpc='.rawurlencode(encrypt_decrypt('encrypt', $res1['jpc_meet'])).'%25decisions';
            }elseif ($not['action_type'] == 'jpc-decision-action') {
                $ref = 'jpc-d-actions?decs='.rawurlencode(encrypt_decrypt('encrypt',$not['action_id']));
            }
            if ($not['action_type'] != 'jpc-decision-action') {
                
                echo '<a class="dropdown-item notification-notice mt-0 media fs-12" href="'.$ref.'" style="padding: 11px 13px;">
                         <p><img src="./images/bell.png" height="20" class="pr-10">  '.$not["action_name"].'</p>
                      </a>';
            }
        }
    }
}
