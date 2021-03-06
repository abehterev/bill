<?php

require_once("err.php");
require_once("db.php");

class LoginSession {
	private $time;
	private $ip_addr;
	private $login_hash;
}

class LoginHash {
	private $hash;
	private $expire;
}

class User {

	private $db_id;
	private $login;
	private $password;
	private $hash_password;

/* CONSTRUCTOR/DESTRUCTOR SECTION */

	private function __construct() {
		#print "Создаем пустой класс ". get_class($this). "\n";
	}

/* PROTECTED SECTION */

	private static function isLoginValid($login){
		return ( preg_match('/^[a-zA-Z0-9]+$/', $login) ? true : false );
	}

	protected function setLogin($login){
		$login = (string)$login;

		if ( $login === "" ){
			$err = Err::USER_LOGIN_EMPTY;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( strlen($login) > 20 ) {
			$err = Err::USER_LOGIN_TOO_LONG;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( ! self::isLoginValid($login) ) {
			$err = Err::USER_LOGIN_NOT_VALID;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( ($res = DB::dbLoginNotExist($login)) > 0 ) {
			$this->login = $login;
			return true;
		}else{
			if ($res < 0) {
				$err = $res;
				user_error( Err::Descr($err) );
			}else{
				$err = Err::USER_LOGIN_CANNOT_CREATE;
				user_error( Err::Descr($err) );
			}
			return $err;
		}
	}

	protected function setPassword($password){
		$password = (string)$password;

		if ( $password === "" ){
			$err = Err::USER_PASSWORD_EMPTY;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( strlen($password) <= 6 ) {
			$err = Err::USER_PASSWORD_TOO_SHORT;
			user_error( Err::Descr($err) );
			return $err;
		}

		$this->password = $password;
		$this->hash_password = hash('sha256', $password);
		return true;
	}

	protected function saveToDb(){
		if ( $err = DB::dbUserSave($this) ){
			return true;
		}else{
			user_error( Err::Descr($err) );
			return $err;
		}

	}

/* PUBLIC SECTION */
	public function getLogin(){
		return (string) $this->login;
	}

	public function getHashPassword(){
		return (string) $this->hash_password;
	}

	public function setDbId($id){
		$this->db_id = (int) $id;
		return true;
	}

	public function loadFromArray($a){
	/*
	 * Function load Object params from associative array ($a).
	 * Need for DB module.
	 */
		$this->db_id = (int) $a['id'];
		$this->login = (string) $a['login'];
		$this->hash_password = (string) $a['password'];
		$this->password = null;
	}

	public static function createNewLogin($login, $password, &$err_buf) {
		$instance = new self();
		if ( ($err_buf = $instance->setLogin($login)) > 0 ) {
			if ( ($err_buf = $instance->setPassword($password)) > 0 ){
				if ( ($err_buf = $instance->saveToDb()) <= 0 ){
					unset($instance);
					return null;
				}
			}else{
				unset($instance);
				return null;
			}
		}else{
			unset($instance);
			return null;
		}
		return $instance;
	}

	public static function getByLogin($login, &$err_buf) {
		
		if ( ($err_buf = DB::dbUserLoad($login, $a)) <= 0 ){
			return null;
		}
		
		$instance = new self();
		$instance->db_id = (int) $a['id'];
		$instance->login = (string) $a['login'];
		$instance->hash_password = (string) $a['password'];
		$instance->password = null;
		return $instance;
	}
	
}


?>
