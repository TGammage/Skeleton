<?php

require "../start.php";

// Username
$user = "Neo";

// Email
$email = "theone@thematrix.com";

// Password
$password = "Iamthe1!";

$hasher = new SystemCore\PepperedPassword;

$hashed = $hasher->hash( $password );

var_dump( $hashed );

$query = "INSERT INTO `member` ( `username`,`email`,`access` ) VALUES ( :user, :email, :access )";

$param	= array(
	'user'		=> $user,
	'email'		=> $email,
	'access'	=> $hashed
);

$db = new \db( 'member' );

$count = $db->query( $query, $param );

var_dump( $db->last_query );

var_dump( $count );

?>