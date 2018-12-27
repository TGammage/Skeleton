<?php
/**
 * Page Specific Definitions
 */
define( 'LOCATION', 'Main Page' );

require "start.php";


$PAGE = new HTML\Frame();

$PAGE->begin();

		if( $GLOBALS['session']->logged_in )
		{
			if( !isset( $_SESSION['url_key']['logout'] ) ){
				$_SESSION['url_key']['logout']	= random::string( 16 );
			}

			echo "
<h2>Welcome</h2><p>Currently Logged in as {$_SESSION['user']['name']}
<br><a href='" . $GLOBALS['conf']->host . "login.php?logout&unique={$_SESSION['url_key']['logout']}'>Logout</a></p>";

		} else {
			echo "
<h2>Welcome</h2><p>Currently not logged in.
<br><a href='" . $GLOBALS['conf']->host . "login.php'>Login</a></p>";

		}

		echo "
<p>
<a href='" . $GLOBALS['conf']->host . "profile.php'>Profile</a>
</p>";

		// var_dump( $_SESSION );

$PAGE->end();

?>