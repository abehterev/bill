<?php

require_once("err.php");

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

/* CONSTRUCTOR/DESTRUCTOR SECTION */

	function __construct() {
		#print "Создаем пустой класс ". get_class($this). "\n";
	}

/* PROTECTED SECTION */

	private static function isLoginFree($login){
		#return false;
		return true;
	}

	private static function isLoginValid($login){
		return ( preg_match('/^[a-zA-Z0-9]+$/', $login) ? true : false );
	}

	protected function setLogin($login){
		$login = (string)$login;

		if ( $login === "" ){
			$err = Err::LOGIN_EMPTY;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( strlen($login) > 20 ) {
			$err = Err::LOGIN_TOO_LONG;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( ! self::isLoginValid($login) ) {
			$err = Err::LOGIN_NOT_VALID;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( self::isLoginFree($login) ) {
			$this->login = $login;
			return true;
		}else{
			$err = Err::LOGIN_CANNOT_CREATE;
			user_error( Err::Descr($err) );
			return $err;
		}
	}

	protected function setPassword($password){
		$password = (string)$password;

		if ( $password === "" ){
			$err = Err::PASSWORD_EMPTY;
			user_error( Err::Descr($err) );
			return $err;
		}

		if ( strlen($password) <= 6 ) {
			$err = Err::PASSWORD_TOO_SHORT;
			user_error( Err::Descr($err) );
			return $err;
		}

		$this->password = $password;
		return true;
	}

	protected function saveToDb(){
		print "-> Save user ".$this->login." with password [".$this->password."] to DB\n";
	}

/* PUBLIC SECTION */

	public static function createNewLogin($login, $password, &$err_buf) {
		$instance = new self();
		if ( ($err_buf = $instance->setLogin($login)) > 0 ) {
			if ( ($err_buf = $instance->setPassword($password)) > 0 ){
				$instance->saveToDb();
			}else{
				$instance = null;
			}
		}else{
			$instance = null;
		}
		return $instance;
	}
}

?>
