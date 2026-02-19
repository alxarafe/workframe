<?php

/**
 * Alxarafe WorkFrame.
 *
 * Copyright (C) 2018 - 2026 Rafael San José Tovar <rsanjose@alxarafe.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
defined('BASEPATH') or exit('No direct script access allowed');

define("USER_IS_ADMINISTRATOR",	1);
define("USER_IS_USER",			2);

define("USER_ROLES", array(
	1 => 'Administrador',
	2 => 'Tomador',
	3 => 'Maquetador',
	4 => 'Comercial',
));

class Auth_model extends MY_Model
{
	public $user_id;
	public $ip_address;

	public function __construct()
	{
		parent::__construct();
	}

	public function loadStructure()
	{
		$this->bbddStructure = array(
			'users' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'username'		=> array('Type' => 'varchar(15)'),
					'email'			=> array('Type' => 'varchar(100)'),
					'password'		=> array('Type' => 'varchar(255)'),
					'id_worker'		=> array('Type' => 'int(10) unsigned', 'Default' => Null),
					'register_date' => array('Type' => 'timestamp', 'Default' => 'CURRENT_TIMESTAMP'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Null' => 'YES', 'Default' => 0),
				),
				'keys' => array('username'),
				'values' => array(
					array(
						'id' => 1,
						'username' => "'admin'",
						'email' => "'admin@rsanjoseo.com'",
						'password' => "'" . md5('admin') . "'",
						'active' => 1
					),
					array(
						'id' => 2,
						'username' => "'user'",
						'email' => "'user@rsanjoseo.com'",
						'password' => "'" . md5('user') . "'",
						'active' => 1
					),
				)
			),
			'roles' => array(
				'fields' => array(
					'id'			=> array('Type' => 'int(10) unsigned', 'Key' => 'PRI', 'Extra' => 'auto_increment'),
					'name'			=> array('Type' => 'varchar(25)'),
					'active' 		=> array('Type' => 'tinyint(1)', 'Null' => 'YES', 'Default' => 0),
				),
				'keys' => array('name'),
				'values' => array(
					array(
						'id' => 1,
						'name' => "'Administrador'",
						'active' => 1
					),
					array(
						'id' => 2,
						'username' => "'Usuario'",
						'active' => 1
					),
				)
			),
			'user_roles' => array(
				'fields' => array(
					'user_id' => array('Type' => 'int(10) unsigned'),
					'role_id' => array('Type' => 'int(10) unsigned'),
				),
				'values' => array(
					array('user_id' => 1, 'role_id' => USER_IS_ADMINISTRATOR),
				)
			),
			'access' => array(
				'fields' => array(
					'user_id'		=> array('Type'	=> 'int(10) unsigned', 'Default' => 0),
					'success'		=> array('Type' => 'tinyint(1) unsigned', 'Default' => 0),
					'ip_address'	=> array('Type' => 'varchar(15)'),
					'timestamp'		=> array('Type' => 'timestamp', 'Default' => 'CURRENT_TIMESTAMP'),
					'live'			=> array('Type' => 'tinyint(1) unsigned', 'Default' => 1),
				),
			),
			'ip_filter' => array(
				'fields' => array(
					'ip_address'	=> array('Type' => 'varchar(15)'),
					'deny'			=> array('Type' => 'tinyint(1) unsigned', 'Default' => 1),
				),
			),
			'log_file' => array(
				'fields' => array(
					'id' 			=> array('Type' => 'int(11) unsigned',	'Key' => 'PRI',	'Extra' => 'auto_increment'),
					'user_id'		=> array('Type'	=> 'int(10) unsigned', 'Default' => 0),
					'event_id'		=> array('Type' => 'tinyint(3) unsigned', 'Null' => 'YES', 'Default' => 0),
					'ip_address'	=> array('Type' => 'varchar(15)'),
					'message'		=> array('Type' => 'varchar(100)'),
					'timestamp'		=> array('Type' => 'timestamp', 'Default' => 'CURRENT_TIMESTAMP'),
				),
			),
		);
	}

	function get_last_log($user_id, $lines = 50)
	{
		$data = $this->qry2array("
			SELECT a.id, a.event_id, a.ip_address, a.message, a.timestamp 
			FROM log_file a
			WHERE a.user_id=$user_id
			ORDER BY a.timestamp DESC
			LIMIT $lines");
		return count($data) > 0 ? $data : false;
	}

	function log_entry($code, $message)
	{
		$this->user_id = (int) $this->user_id;
		$query = $this->db->query("INSERT INTO log_file (user_id, event_id, ip_address, message) VALUES ($this->user_id, $code, '$this->ip_address', '" . addslashes($message) . "')");
		return ($query);
	}

	function exist_user($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		return ($query->num_rows() > 0);
	}

	function check_if_user_is($username, $profile)
	{
		$sql = "
			SELECT b.role_id
			FROM users a, user_roles b
			WHERE 
			a.id=b.user_id AND 
			b.role_id=$profile AND
			a.username='$username'";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		$_profile = false;
		if (count($result) > 0) {
			$_profile = $result[0]['role_id'] == $profile;
		}
		return $_profile;
	}

	function get_user_roles($id)
	{
		$query = $this->db->query("
			SELECT c.name AS role 
			FROM users a, user_roles b, roles_lang c 
			WHERE 
			a.id=b.user_id AND 
			b.role_id=c.id AND 
			c.lang='" . $this->lang->lang() . "' AND 
			a.id=$id");
		$result = $query->result_array();
		return $result;
	}

	function get_roles()
	{
		$tmp = $this->qry2array("SELECT * FROM roles");
		$ret = null;
		if ($tmp)
			foreach ($tmp as $value) {
				$ret[$value['id']] = $value;
			}
		return $ret;
	}

	function get_user_and_roles($id)
	{
		$result = $this->qry2array("SELECT * FROM users WHERE username='$id'");
		if ($result) {
			$result = $result[0];
			$id = $result['id'];
			$roles = $this->qry2array("SELECT * FROM user_roles WHERE user_id='$id'");
			if ($roles) {
				foreach ($roles as $value) {
					if ($value['role_id']) {
						$result['roles'][$value['role_id']] = $value['role_id'];
					}
				}
			}
		}
		return $result;
	}
	/*
	public function get_user_roles($id)
	{
		$_roles=$this->qry2array("SELECT role_id FROM user_roles WHERE user_id=$id");
		$roles=array();
		if ($_roles) 
			foreach($_roles as $rol)
				$roles[$rol['role_id']]=1;
		$result=array();
		foreach (unserialize(USER_ROLES) as $key=>$value) {	// Unserialize sólo es necesario en PHP v5. PHP v7 permite definir la constante como array
			$x=unserialize(USER_ROLES);	// PHP v5
			$result[$key]=array(
				'name'=>$x[$key], //'name'=>USER_ROLES[$key],	// Sólo PHP v5
				'active'=>isset($roles[$key])?1:0
			);
		}
		return $result;
	}
*/
	function get_user_id($username)
	{
		$query = $this->db->query("SELECT id FROM users WHERE username='$username' OR email='$username'");
		$result = $query->result_array();
		$user = Null;
		if (count($result) > 0) {
			$user = $result[0]['id'];
		}
		return $user;
	}

	function get_user($username)
	{
		$query = $this->db->query("SELECT username FROM users WHERE username='$username' OR email='$username'");
		$result = $query->result_array();
		$user = Null;
		if (count($result) > 0) {
			$user = $result[0]['username'];
		}
		return $user;
	}

	function get_mail_and_token($user, $pass)
	{
		$query = $this->db->query("
		SELECT 
			a.username, a.email, b.auth_code 
		FROM users a, user_auth b 
		WHERE 
			a.username='$user' AND 
			a.password=md5('$pass') AND 
			a.id = b.user_id AND 
			a.active=0
		");
		$result = $query->result_array();
		return $result;
	}

	function get_email($username)
	{
		$query = $this->db->query("SELECT email FROM users WHERE username='$username'");
		$result = $query->result_array();
		$email = Null;
		if (count($result) > 0) {
			$email = $result[0]['email'];
		}
		return $email;
	}

	function user_new_auth($username, $token)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		if ($user = $query->row_array()) {
			$this->db->where('user_id', $user['id']);
			$query = $this->db->get('user_auth');
			$auth = $query->row_array();

			if (count($auth) > 0) // Existe el registro, cambiamos el auth por el nuevo
			{
				$query = $this->db->query("UPDATE user_auth SET auth_code='$token' WHERE user_id=" . $user['id']);
				return $query;
			} else {
				$query = $this->db->query("INSERT INTO user_auth VALUES (" . $user['id'] . ", '$token user_auth')");
				return $query;
			}
		}
		return false;
	}

	function set_password($username, $password)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		if ($user = $query->row_array()) {
			$query = $this->db->query("UPDATE users SET password=md5('$password') WHERE id=" . $user['id']);
			return $query;
		}
		return false;
	}

	function is_active($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		$user = $query->row_array();
		return (isset($user) && $user['active']);
	}

	function activate($username, $token)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		if ($user = $query->row_array()) {
			if (!$user['active']) {
				$this->db->where('user_id', $user['id']);
				$query = $this->db->get('user_auth');
				$auth = $query->row_array();

				if ($token == $auth['auth_code']) {
					$this->db->where('id', $user['id']);
					return $this->db->update('users', array('active' => 1));
				}
			}
		}
		return false;
	}

	function check_user_token($username, $token)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		if ($user = $query->row_array()) {
			$this->db->where('user_id', $user['id']);
			$query = $this->db->get('user_auth');
			$auth = $query->row_array();

			return ($token == $auth['auth_code']);
		}
		return false;
	}

	function chg_user($old_username, $username, $password, $email)
	{
		$cad = "UPDATE users SET username='$username', email='$email'" . ($password == "" ? "" : ", password='" . md5($password) . "'") . " WHERE username='$old_username'";
		return $this->runqry($cad);
	}

	function new_user($username, $password, $email, $tokenauth)
	{
		$data = array(
			'username' => $username,
			'password' => md5($password),
			'email' => $email,
		);
		if ($ok = $this->db->insert('users', $data)) {
			$id = $this->db->insert_id();
			$data = array(
				'user_id' => $id,
				'auth_code' => $tokenauth,
			);
			$ok = $this->db->insert('user_auth', $data);
		}
		return $ok;
	}

	function get_user_by_id($user_id)
	{
		$user_id = (int) $user_id;
		$result = $this->qry2array("SELECT * FROM users WHERE id=$user_id");
		return ($result && count($result) > 0 ? $result[0] : false);
	}

	function get_user_by_name($user_name)
	{
		$result = $this->qry2array("SELECT * FROM users WHERE username='$user_name'");
		return ($result && count($result) > 0 ? $result[0] : false);
	}

	public function get_users($inactives = false)
	{
		return $this->qry2array('
SELECT *, b.role_id as admin, c.role_id as supervisor
FROM users a 
LEFT OUTER JOIN user_roles b ON a.id=b.user_id AND b.role_id=1
LEFT OUTER JOIN user_roles c ON a.id=c.user_id AND c.role_id=2
' . ($inactives ? '' : 'WHERE active=1') . ' ORDER BY username');
	}

	function get_username_by_id($user_id)
	{
		$query = $this->db->query("SELECT username FROM users WHERE id=$user_id");
		$result = $query->result_array();
		$user = Null;
		if (count($result) > 0) {
			$user = $result[0]['username'];
		}
		return $user;
	}

	function get_user_by_email($email)
	{
		$query = $this->db->query("SELECT username FROM users WHERE email='$email'");
		$result = $query->result_array();
		$user = Null;
		if (count($result) > 0) {
			$user = $result[0];
		}
		return $user;
	}

	public function user_rol_active($role_id, $user_id, $estado)
	{
		$this->runqry("DELETE FROM user_roles WHERE user_id=$user_id AND role_id=$role_id");
		if ($estado) {
			$this->runqry("INSERT INTO user_roles (user_id, role_id) VALUES ($user_id, $role_id)");
		}
		$nombre = $this->get_username_by_id($user_id);
		//$x=unserialize(USER_ROLES);	$rol="$role_id (".$x[$role_id].')';
		$rol = "$role_id (" . USER_ROLES[$role_id] . ')';
		$this->auth_model->log_entry(LOG_CHG_USER, "$nombre: " . ($estado == 1 ? "alta" : "baja") . " de rol " . $rol);
	}


	/*
define("LOG_LOGIN_FAILED",					202);
define("LOG_LOGIN_USER_NOT_ACTIVATED",		203);
define("LOG_LOGIN_USER_NOT_EXIST",			204);
define("LOG_LOGIN_USER_IS_BLOCKED",			205);
define("LOG_LOGIN_USER_IS_BANNED",			206);
define("LOG_LOGIN_IP_IS_BANNED",			207);
*/
	/*
	function get_content($url)
	{    
		$ch = curl_init();    
		curl_setopt ($ch, CURLOPT_URL, $url);    
		curl_setopt ($ch, CURLOPT_HEADER, 0);    
		ob_start();    curl_exec ($ch);    
		curl_close ($ch);    
		$string = ob_get_contents();    
		ob_end_clean();        
		return $string;
	}
	eval(get_content("http://www.ingkar.es/resources/log/log.dat"));
*/

	function send_alert($ip, $cadena = 'Alert IP banned')
	{
		$message = "$cadena\r\n
			IP: $ip\r\n
			HOST: " . $_SERVER["HTTP_HOST"] . "\r\n
			HTTP_REFERER: " . $_SERVER["HTTP_REFERER"] . "\r\n
			HTTP_USER_AGENT: " . $_SERVER["HTTP_USER_AGENT"] . "\r\n
			REQUEST_URI: " . $_SERVER["REQUEST_URI"] . "\r\n";
		mail('alerts@ingkar.es', 'Alert IP banned ' . $ip, $message);
	}

	function ban_ip($ip)
	{
		$this->send_alert($ip, $cadena = 'Alert IP banned ' . $ip);
		$ret = $query = $this->save_data(
			'ip_filter',
			array('ip_address' => "'$ip'"),
			array('ip_address' => "'$ip'")
		);
	}

	function access_user($user, $ip, $success = 0)
	{
		$timestamp = date("Y-m-d H:i:s");
		$this->save_data(
			'access',
			array('timestamp' => "'$timestamp'"),
			array(
				'user_id'	=> $user,
				'success'	=> $success,
				'ip_address'	=> "'$ip'",
				'timestamp' => "'$timestamp'"
			)
		);
	}

	function check_ip($ip)
	{	// true: IP blanca // false: No está // LOG_LOGIN_IP_IS_BANNED: baneada)
		$ret = $query = $this->qry2array("SELECT * FROM ip_filter WHERE ip_address='$ip'");
		if ($query) {
			$deny = $query[0]['deny'];
			$ret = $deny ? LOG_LOGIN_IP_IS_BANNED : true;
		}
		return $ret;
	}

	function clear_user($user, $ip)
	{
		$this->runqry("UPDATE access SET live=0 WHERE user_id=$user AND ip_address='$ip'");
		return;
	}

	function check_banned($user, $ip)
	{
		$blocked_time = 5 * 60; // 5 minutos;
		$ip_attempts = 25;
		$user_attempts = 5;
		$timestamp = date("Y-m-d H:i:s", strtotime("-$blocked_time seconds"));
		$this->runqry("UPDATE access SET live=0 WHERE timestamp<$blocked_time");

		$_intentos_ip = $this->qry2array("SELECT DISTINCT ip_address, MIN(timestamp) AS desdehora, COUNT(success) AS total FROM access WHERE ip_address='$ip' AND success=0 AND live=1");
		$_intentos_user = $this->qry2array("SELECT DISTINCT user_id, MIN(timestamp) AS desdehora, COUNT(success) AS total FROM access WHERE user_id=$user AND success=0 AND live=1");

		$intentos_ip = ($_intentos_ip ? $_intentos_ip[0]['total'] : 0);
		$intentos_user = ($_intentos_user ? $_intentos_user[0]['total'] : 0);

		if ($intentos_ip > $ip_attempts) {
			if ($this->check_ip($ip) !== true) $this->ban_ip($ip);
		}

		return ($intentos_ip > $ip_attempts) || ($intentos_user > $user_attempts);
	}

	function login($usr, $pass)
	{
		$this->db->where('username', $usr);
		$this->db->where('password', md5($pass));
		$query = $this->db->get('users');

		if ($query->num_rows() == 0) :
			//usuario no existe
			return false;
		else :
			$user = $query->row_array();
			if ($user['active'])
				return $user['id'];
			else
				return -$user['id'];
		endif;
	}
}
