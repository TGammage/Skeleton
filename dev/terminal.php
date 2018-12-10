<?php

$date = new DateTime();

//require "../start.php";

print "System Request is commencing at : " . $date->format( 'h:i:s' ) . "\r\n";

var_dump( ini_get( 'session.bg_maxlifetime' ) );
?>
