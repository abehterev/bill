<?php

require_once("err.php");

class DB { /* Singleton */
	private static $db_instance = null;
	private static $jsonConfigFile = "db.json";

	private $mysql_link;

	private $host;
	private $port;
	private $database;
	private $login;
	private $password;

/* CONSTRUCT */
	private function __clone(){} /* protect clone() */
	private function __wakeup(){} /* protect wakeup() serialize */

	/*
	 * 	NOTE: PRIVATE CONSTRUCT
	 *	$db = new DB($login, $password, $database, [$host, $port]) 
	 *
	*/
	private function __construct($login, $password, $database, $host = 'localhost', $port = 3306) {
		$this->host = (string)$host;
		$this->port = (int)$port;
		$this->database = (string)$database;
		$this->login = (string)$login;
		$this->password = (string)$password;
	}

	private static function readConfigFromJSON($filename, &$err_buf){
		if ( ($json = (string)file_get_contents($filename)) !== false ){
			if ( $config = json_decode($json) ){

				if ( !isset($config->{'host'}) ) 
					$config->{'host'} = "localhost";

				if ( !isset($config->{'port'}) ) 
					$config->{'port'} = 3306;

				if ( !isset($config->{'database'}) ) {
					$err_buf = Err::DB_DATABASE_NOT_SET;
					user_error( Err::Descr($err_buf) );
					return null;
				}
				elseif ( !isset($config->{'login'}) ) {
					$err_buf = Err::DB_LOGIN_NOT_SET;
					user_error( Err::Descr($err_buf) );
					return null;
				}
				elseif ( !isset($config->{'password'}) ) {
					$err_buf = Err::DB_PASSWORD_NOT_SET;
					user_error( Err::Descr($err_buf) );
					return null;
				}else{
					if ( self::$db_instance === null ) {
						self::$db_instance = new DB(
							$config->{'login'},
							$config->{'password'},
							$config->{'database'},
							$config->{'host'},
							$config->{'port'}
						);
					}
					return self::$db_instance;
				}
			}else{
				$err_buf = Err::DB_JSON_PARSE_ERR;
				user_error( Err::Descr($err_buf) );
				return null;
			}
		}else{
			$err_buf = Err::DB_JSON_READ_ERR;
			user_error( Err::Descr($err_buf) );
			return null;
		}
	}

	private function dbConnect(){
		try {
			$this->mysql_link = new PDO('mysql:host='.$this->host.';port='.$this->port.';dbname='.$this->database.';charset=utf8',
				$this->login, $this->password);
			$this->mysql_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->mysql_link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); /* Disable prepared statement emulation layer */
			$this->mysql_link->setAttribute(PDO::MYSQL_ATTR_DIRECT_QUERY, false); /* same */
			$this->mysql_link->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false); /* send correct types from sql server */
			return true;
		}
		catch(PDOException $e){
			$error = Err::DB_PDO_CONN_ERR;
			user_error( $e->getMessage() );
			user_error( Err::Descr($error) );
			return $error;
		}

	/* MySQL connect old method
		if( $this->mysql_link = mysql_connect($this->host, $this->login, $this->password) ) {
			if ( mysql_select_db($this->database) ){
				return true;
			}else{
				$error = Err::DB_CANNOT_USE_DATABASE;
				user_error( Err::Descr($error) );
				return $error;
			}
		}else{
			$error = Err::DB_CANNOT_CONNECT;
			user_error( Err::Descr($error) );
			return $error;
		}
	*/

	}

	private static function dbInit(&$err_buf){
		if ( self::$db_instance === null ) {
			if ( !self::readConfigFromJSON(self::$jsonConfigFile, $err_buf) ){
				return null;
			}else{
				$instance = self::$db_instance;
				if ( !( ($err_buf = $instance->dbConnect()) > 0) ) {
					return null;
				}
			}
		}
		return self::$db_instance;
	}

/* PUBLIC SECTION */

	/*
	 * Check user exist in DB.
	 * 
	 *
	 * BOOL dbLoginExist($login, [$id])
	 *
	 * RETURN:
	 * 	TRUE or FALSE
	 * 	if set second param return also USERID by link
	 * ERROR:
	 * 	if error then return ERR_CODE ( <0 )
	 */

	public static function dbLoginExist($login, &$id=null){
		if ( self::dbInit( $err_buf ) ){
			$instance = self::$db_instance;
			try{
				
				$stmt = $instance->mysql_link->prepare("SELECT id FROM users WHERE login=? LIMIT 1");
				$stmt->bindValue(1, $login, PDO::PARAM_STR);
				$stmt->execute();
				
				$stmt->setFetchMode(PDO::FETCH_ASSOC);

				$count = $stmt->rowCount();
				if ( $count > 0 ) {
					$id = (int)$stmt->fetch()['id'];
					return true;
				}
				return false;
			}catch(PDOException $e){
				$error = Err::DB_PDO_QUERY_ERR;
				user_error( $e->getMessage() );
				user_error( Err::Descr($error) );
				return $error;
			}
		}else{
			user_error( Err::Descr($err_buf) );
			return $err_buf;
		}

	}

	public static function dbUserSave(&$user){
		if ( self::dbInit( $err_buf ) ){
			$instance = self::$db_instance;
			try{
				$stmt = $instance->mysql_link->prepare("INSERT INTO users (login,password) VALUES(?,?)");
				$stmt->execute( [$user->getLogin(), $user->getHashPassword()] );
				$user->setDbId( $instance->mysql_link->lastInsertId() );
				return true;
			}catch(PDOException $e){
				$error = Err::DB_PDO_QUERY_ERR;
				user_error( $e->getMessage() );
				user_error( Err::Descr($error) );
				return $error;
			}
		}else{
			user_error( Err::Descr($err_buf) );
			return $err_buf;
		}
	}
}


/* TEST SECTION */
/*
if ( ($res = DB::dbLoginExist("testuser", $user_id)) >= 0 ){
	( $res ) ? print "exist (userid: $user_id)\n" : print "not exist\n";
}else{
	$error = $res;
	print "[dbLoginExist] Error: " . Err::Descr($error) . "\n";
}
 */
/*
if ( $db2 = DB::readConfigFromJSON("db.json", $err_buf) ){
	var_dump($db2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

if ( $db3 = DB::readConfigFromJSON("db.json", $err_buf) ){                       
	        var_dump($db3);                                                          
}else{                                                                           
	        print "Error: " . Err::Descr($err_buf) . "\n";                           
}
 */
?>
