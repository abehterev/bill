<?php
/* Error code class */

class Err
{

	/* Err codes */
	const USER_LOGIN_CANNOT_CREATE = -11;
	const USER_LOGIN_TOO_LONG = -12;
	const USER_LOGIN_EMPTY = -13;
	const USER_LOGIN_NOT_VALID = -14;

	const USER_PASSWORD_EMPTY = -21;
	const USER_PASSWORD_TOO_SHORT = -22;

	const DB_LOGIN_NOT_SET = -101;
	const DB_PASSWORD_NOT_SET = -102;
	const DB_JSON_READ_ERR = -103;
	const DB_JSON_PARSE_ERR = -104;
	const DB_DATABASE_NOT_SET = -105;
	const DB_CANNOT_CONNECT = -106;
	const DB_CANNOT_USE_DATABASE = -107;
	const DB_PDO_ERR = -108;

	const ERR_UNKNOWN = -65535;

	/* Err codes end */

	private static $descr = array(
		self::USER_LOGIN_CANNOT_CREATE => "USER: Can't create login. Maybe already exists.",
		self::USER_LOGIN_TOO_LONG => "USER: Too long login, need < 20 symbols.",
		self::USER_LOGIN_EMPTY => "USER: Login is empty.",
		self::USER_LOGIN_NOT_VALID => "USER: Login isn't valid.",

		self::USER_PASSWORD_EMPTY => "USER: Password is empty.",
		self::USER_PASSWORD_TOO_SHORT => "USER: Password too short, need > 6 symbols.",

		self::DB_LOGIN_NOT_SET => "DB: Database login not set.",
		self::DB_PASSWORD_NOT_SET => "DB: Database password not set.",
		self::DB_JSON_READ_ERR => "DB: JSON config file read error.",
		self::DB_JSON_PARSE_ERR => "DB: JSON config file parse error.",
		self::DB_DATABASE_NOT_SET => "DB: Database name not set.",
		self::DB_CANNOT_CONNECT => "DB: Cannot connect to the mysql server.",
		self::DB_CANNOT_USE_DATABASE => "DB: Cannot use database.",
		self::DB_PDO_ERR => "DB: PDO connect database error.",


		self::ERR_UNKNOWN => "Error code unknown.",
	);

	public static function Descr($error){
		$text = isset(self::$descr[(int)$error]) ? (string)self::$descr[(int)$error] : (string)self::$descr[self::ERR_UNKNOWN];
		return "(" . $error . ") " . $text;
	}
}

?>
