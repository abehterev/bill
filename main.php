<?php

require_once("user.php");
require_once("err.php");

/* TDD */

/* test #1
	create login more 20 symbols
*/
print(">> [1] create login more 20 symbols\n");
if ( $user1 = User::createNewLogin("loginMoreThanTwentySymbols","some-pass2", $err_buf) ) {
	var_dump($user1);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #2
	create empty login
*/
print(">> [2] create empty login\n");
if ( $user2 = User::createNewLogin("","pass2", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #3
	create empty password
*/
print(">> [3] create empty password\n");
if ( $user3 = User::createNewLogin("login3","", $err_buf) ) {
	var_dump($user3);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #4
	create trash login
*/
print(">> [4] create trash login\n");
if ( $user4 = User::createNewLogin("login_@!CanBe#","", $err_buf) ) {
	var_dump($user4);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #5
	Create Valid User 
*/
print(">> [5] Create Valid User\n");
if ( $user5 = User::createNewLogin("testuser9","password", $err_buf) ) {
	var_dump($user5);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #6
	Create Valid User 
*/
print(">> [6] Create Dup of Valid User\n");
if ( $user6 = User::createNewLogin("testuser9","password", $err_buf) ) {
	var_dump($user6);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #7
	Get Invalid User 
*/
print(">> [7] Get Invalid User\n");
if ( $user7 = User::getByLogin("testuser999", $err_buf) ) {
	var_dump($user7);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

/* test #8
	Get Valid User 
*/
print(">> [8] Get Valid User\n");
if ( $user8 = User::getByLogin("testuser9", $err_buf) ) {
	var_dump($user8);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

?>
