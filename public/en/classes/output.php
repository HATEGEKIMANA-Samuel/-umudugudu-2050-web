<?php

/**
 * this class handles all outputs related tasks mainly sanitizing 
 */
class output
{

	// function __construct(argument)
	// {
	// 	# code...
	// }
	public static function integer($string)
	{
		if (is_integer($string)) echo $string;
	}
	public static function plainText($string)
	{
		if (ctype_alnum($string)) echo $string;
	}
	public static function formatDate($dateToformat, $pattern = 'Y/m/d H:i:s')
	{
		$date = date_create($dateToformat);
		return  date_format($date, $pattern);
	}
	public static function print($value, $array = array(), $sep = '-')
	{
		return !isset($array[$value]) || empty($array[$value]) ? $sep : $array[$value];
	}
	public static function log($text, $filePath = "data/logs.txt", $mode = "a+")
	{
		$text = " Logs:" . $text . " Done On :" . date('Y-m-d H:i:s') . " Used IP:" . getenv("REMOTE_ADDR") . PHP_EOL;
		$fh = fopen($filePath, $mode) or die("unable to create " . $filePath);
		fwrite($fh, $text) or die("Could not write to file");
		fclose($fh);
	}
	// to search string in word
	function isInString($search, $string): bool
	{
		$pos = strpos($string, $search);
		if ($pos === false) {
			return false;
		} else {
			return true;
		}
	}
	// loading time

	public static function checkTime($startTime = 00000)
	{
		printf("Total time : %.6fs\n", microtime(true) - $startTime);
	}
}
