<?php
class token{
	public static function generate(){
		return session::put(USER_TOKEN,randomString(32));
	}
	public static function check($token){
		if(session::exists(USER_TOKEN) && $token === session:: get(USER_TOKEN)){
			session::delete(USER_TOKEN);
			return true;
		}
		return false;
	}
}
?>