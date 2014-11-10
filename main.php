<?php

require_once("user.php");
require_once("err.php");

/* TDD */

/* test #1
	create login more 20 symbols
*/
if ( $user1 = User::createNewLogin("loginMoreThanTwentySymbols","some-pass2", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #2
	create empty login
*/
if ( $user2 = User::createNewLogin("","pass2", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #3
	create empty password
*/
if ( $user3 = User::createNewLogin("login3","", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #4
	create trash login
*/
if ( $user4 = User::createNewLogin("login4-Trash_Symbols@!CanBe#Unavaiable","", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #5
	Create Valid User 
*/
if ( $user5 = User::createNewLogin("testuser9","password", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

?>
