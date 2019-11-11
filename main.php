<?php
/**
 * Page Specific Definitions
 */
define( 'LOCATION', 'Main Page' );

require "start.php";


$PAGE = new HTML\Frame();


$content = '';

if( $GLOBALS['session']->logged_in )
{
	// Create Logout Token
	if( !isset( $_SESSION['url_key']['logout'] ) )
		$_SESSION['url_key']['logout'] = random::string( 16 );

	// Welcome Message
	$log_link = "<a href='{$GLOBALS['conf']->host}login.php?logout&unique={$_SESSION['url_key']['logout']}'>Logout</a>";

} else {
	$log_link = "<a href='" . $GLOBALS['conf']->host . "login.php'>Login</a></p>";
}

$message = $GLOBALS['session']->logged_in ? "Logged in as {$_SESSION['user']['name']}" : "not logged in";

$content .= "
<h2>Welcome</h2>
<p>
	Currently $message.
	<br>$log_link
</p>
<p><a href='{$GLOBALS['conf']->host}profile.php'>Profile</a></p>";


$PAGE->addContent( $content );

$PAGE->output();

?>