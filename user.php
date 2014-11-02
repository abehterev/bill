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

/* CONSTRUCTOR/DESTRUCTOR SECTION */

	function __construct() {
		#print "Создаем пустой класс ". get_class($this). "\n";
	}

/* PROTECTED SECTION */

	private static function isLoginFree($login){
		if ( ($res = DB::dbLoginExist($login)) >= 0 ){
			return ! $res;
		}else{
			$error = $res;
			return $error;
		}
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

		if ( ($res = self::isLoginFree($login)) > 0 ) {
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
