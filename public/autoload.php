<?php
// error_reporting(0);
if (file_exists('../web-config/grobals.php')) {
	require_once('../web-config/grobals.php');
} elseif (file_exists('../../web-config/grobals.php')) {
	require_once('../../web-config/grobals.php');
} elseif (file_exists('../../../web-config/grobals.php')) {
	require_once('../../../web-config/grobals.php');
}
spl_autoload_register(function ($class) {
	//$class = strtolower($class);
	require_once 'classes/' . $class . '.php';
});

include("includes/functions.php");
