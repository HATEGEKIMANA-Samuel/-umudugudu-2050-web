<?php

class input
{

	public static function exists($type = 'REQUEST')
	{

		switch ($type) {

			case 'POST':

				return (!empty($_POST)) ? true : false;

				break;

			case 'GET':

				return (!empty($_GET)) ? true : false;

				break;

			case 'REQUEST':

				return (!empty($_REQUEST)) ? true : false;

				break;

			default:

				return false;

				break;
		}
	}
	public static function get($item)
	{
		if (isset($_POST[$item])) {
			return $_POST[$item];
		} else if (isset($_GET[$item])) {

			return $_GET[$item];
		} else if (isset($_REQUEST[$item])) {

			return $_REQUEST[$item];
		}
	}
	public static function sanitize($item, $type = 'alphabet')
	{
		switch ($type) {
			case 'alphabet':
				return filter_var(self::get($item), FILTER_SANITIZE_STRING);
				break;

			default:
				return filter_var(self::get($item), FILTER_SANITIZE_STRING);
				break;
		}
	}
	public static function getFileName($item)
	{
		if (isset($_FILES[$item]['name'])) {
			return $_FILES[$item]['name'];
		}
		return null;
	}
	public static function getFileTemporaryName($item)
	{
		if (isset($_FILES[$item]['tmp_name'])) {

			return $_FILES[$item]['tmp_name'];
		}

		return null;
	}
	public static function enc_dec($action, $string)
	{
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = '@Secrety key PMS';
		$secret_iv = '@Secrety key PMS iv';
		// hash
		$key = hash('sha256', $secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'e') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'd') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	public static function getDbDateTimeFormat($inputName, $format = 'Y-m-d h:i:s')
	{
		return date("$format", strtotime(self::sanitize($inputName)));
	}
	public static function required($params = array())
	{
		foreach ($params as $key => $value) {

			if (!isset($_REQUEST[$value]) || empty(trim($_REQUEST[$value]))) {

				return false;
			}
		}
		return true;
	}
}
