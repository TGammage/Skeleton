<?php
define('LOCATION', 'Front Page');

require "start.php";

$PAGE = new HTML\Frame();

var_dump( $GLOBALS['session']->logged_in );

if( $GLOBALS['session']->logged_in )
{
    $_SESSION['url_var']['logout'] = random::string( 32 );
    $content = "<a href='/login.php?logout&unique={$_SESSION['url_var']['logout']}'>Logout</a>";
} else {
    $content = "<a href='/login.php'>Login</a>";
}


$PAGE->addContent( $content );

$PAGE->output();

?>