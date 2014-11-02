<?php

require_once("user.php");
require_once("err.php");

#if ( $user2 = User::createNewLogin("login2-MoreThanTwentySymbols","pass2", $err_buf) ) {

if ( $user2 = User::createNewLogin("testuser","password2", $err_buf) ) {
	var_dump($user2);
}else{
	print "Error: " . Err::Descr($err_buf) . "\n";
}

?>
