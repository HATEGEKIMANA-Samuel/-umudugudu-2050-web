<?php
require_once("autoload.php");
session::delete("id");
session::delete("username");
session::delete("level");
session::delete("userLocation");
session::delete("CL");
session_regenerate_id();
header("location: index");
exit(0);
