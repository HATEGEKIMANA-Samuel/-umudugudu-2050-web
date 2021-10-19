<?php
class redirect{
	public static function to($link){
		header('location:'.$link);
	}
	
}
?>