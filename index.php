<?php
require "start.php";

$PAGE = new HTML\Frame();

$PAGE->begin();

var_dump( $GLOBALS['session']->logged_in );

if( $GLOBALS['session']->logged_in )
{
    $_SESSION['url_var']['logout'] = random::string( 32 );
    print "<a href='/login.php?logout&unique={$_SESSION['url_var']['logout']}'>Logout</a>";
} else {
    print "<a href='/login.php'>Login</a>";
}


$PAGE->end();

?>