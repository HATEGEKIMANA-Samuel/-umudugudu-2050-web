<?php
class user
{
	private $_db,
		$_data,
		$_session,
		$_cookie,
		$_isloggedin,
		$_phone_session,
		$_nun_loggedin_cookie,
		$_cookie_expiry;

	public function __construct($user = null)
	{
		global $database;
		$this->_db = $database;
		$this->_session = USER_SESSION;
		$this->_phone_session = USER_PHONE_SESSION;
		$this->_cookie = USER_COOKIE;
		$this->_cookie_expiry = USER_COOKIE_EXPIRY;
		if (cookie::exists(NUN_LOGGED_IN_USER_COOKIE)) {
			$this->_nun_loggedin_cookie = cookie::get(NUN_LOGGED_IN_USER_COOKIE);
		} else {
			$this->_nun_loggedin_cookie = null;
		}
		if (!$user) {
			if (session::exists($this->_session)) {
				$user = session::get($this->_session);
				$u = $this->_db->getArray('user_id', 'users_sessions', "hash = '$user'");
				if (!empty($u)) {
					if ($this->find($u['user_id'])) {
						$this->_isloggedin = true;
					} else {
						self::logout();
					}
				}
			}
		} else {
			$this->find($user);
		}
	}
	public function create($table, $fields = array())
	{
		if (!$this->_db->create($table, $fields)) {
			return false;
		}
		return true;
	}

	public function find($user = null)
	{
		if ($user) {
			$data = $this->_db->getArray('*', 'users', " (id = '$user' OR phoneNumber = '$user')  AND status='1'");
			if (!empty($data)) {
				$this->_data = (object) $data;
				return true;
			}
		}
		return false;
	}
	public function login($username = null, $password = null, $rememberMe = null)
	{
		if (!$username && !$password && $this->exists()) {
			$hash = $this->_db->get("hash", "users_sessions", "user_id = '{$this->data()->id}' AND status='1'");
			if (!empty($hash)) {
				session::put($this->_session, $hash->hash);
				return true;
			}
			return false;
		} else {
			$user = $this->find($username);
			if ($user) {
				if (password_verify($password, $this->data()->password)) {
					$hash = randomString(32);
					$hashcheck = $this->_db->getArray("*", "users_sessions", "user_id = '{$this->data()->id}' AND status='1'");
					if (empty($hashcheck)) {
						$this->_db->create('users_sessions', array(
							'user_id' => $this->data()->id,
							'hash' => $hash
						));
					} else {
						$hash = $hashcheck['hash'];
					}
					session::put($this->_session, $hash);
					$this->_isloggedin = true;
					self::log('login attempt');
					if ($rememberMe == '1') {
						cookie::put($this->_cookie, $hash, $this->_cookie_expiry);
					}
					//$this->_db->query("UPDATE cart SET customer_id='{$this->data()->id}' WHERE customer_id='{$this->_nun_loggedin_cookie}'");
					return true;
				}
			}
		}
		self::log('login attempt', '0', "username: $username");
		return false;
	}
	public function phoneLogin($phone, $pin = "12345_system_", $hosp_id = '')
	{

		//$tempPin = SMS::getPin();
		$user = $this->find($phone);
		if ($user) {
			$userName = 'User_' . randomString(5);
			$phoneNumber = sanitizeDatabaseInput($phone);
			if (!empty($hosp_id)) {
				$pin = password_hash($pin, PASSWORD_DEFAULT);
				$this->_db->query("UPDATE users SET password='{$pin}',pin='{$pin}' WHERE phoneNumber='$phoneNumber' LIMIT 1");
			}
			session::put($this->_phone_session, $phoneNumber);
			return true;
		} else {
			if (empty($hosp_id)) {
				return false;
				exit(0);
			}
			$userName = 'User_' . randomString(5);
			$phoneNumber = sanitizeDatabaseInput($phone);
			$pin = password_hash($pin, PASSWORD_DEFAULT);
			self::create('users', array(
				'randomId' => randomString(15),
				'userName' => $userName,
				'phoneNumber' => $phoneNumber,
				'userRole' => 'customer',
				'pharmacy_id' => $hosp_id,
				'password' =>  $pin,
				'pin' =>  $pin
			));
			session::put($this->_phone_session, $phoneNumber);
			return true;
		}
		return false;
	}
	public function update($fields = array(), $id = null)
	{
		if (!$id && $this->isloggedin()) {
			$id = $this->data()->id;
		}
		if (!$this->_db->update('users', 'id=' . $id, $fields)) {
			self::log('personal info update', 0);
			return false;
		}
		self::log('personal info update');
		return true;
	}
	public function haspermission($keys)
	{
		$group = $this->_db->get('groups', '', array('id', '=', $this->data()->group));
		if ($group->count()) {
			$permissions = json_decode($group->first()->permissions, true);
			if ($permissions[$keys] == true) {
				return true;
			}
		}
		return false;
	}

	public function exists()
	{
		return (!empty($this->_data)) ? true : false;
	}
	public function logout()
	{

		self::log('logged out');
		$this->_db->query("DELETE FROM users_sessions WHERE user_id='{$this->data()->id}'");
		session::delete($this->_session);
		cookie::delete($this->_cookie);
	}
	public function data()
	{
		return $this->_data;
	}
	public function isloggedin()
	{
		return $this->_isloggedin;
	}
	public function updateothers($table, $rule, $id, $fields)
	{
		if (!$this->_db->update($table, $rule, $id, $fields)) {
			throw new Exception('there was a problem in inserting your data please consider to try again later');
		}
	}
	public function log($action, $status = '1', $description = null)
	{
		$logged_user = 0;
		if ($this->_isloggedin)
			$logged_user = $this->data()->id;

		if (!$this->_db->create('logs', array(
			'user_id' => $logged_user,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'action' => $action,
			'status' => $status,
			'description' => $description
		))) {
			return false;
		}
		return true;
	}

	// method used to get interval between old date from current date

	public static function dateDiff($parameter, $oldDate)
	{
		$now = new DateTime();
		$interval = $now->diff(new DateTime($oldDate));
		switch ($parameter) {
			case 'y':
				return (int) $interval->format('%y');
				break;
			case 'm':
				return (int) $interval->format('%m');
				break;
			case 'd':
				return (int) $interval->format('%d');
				break;
				//total day
			case 'a':
				return (int) $interval->format('%a');
				break;
			case 'h:i:s':
				return $interval->format('%h:%i:%s');
				break;
			case 'all':
				return $interval->format('%y %m %d %h:%i:%s');
				break;

			default:
				# code...
				break;
		}
		return $interval->format('%a');
	}
}
