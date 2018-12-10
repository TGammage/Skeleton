<?php

$date = new DateTime();

require "../../../start.php";

print "Tick is commencing at : " . $date->format( 'h:i:s' ) . "\r\n";

$test_time = DateTime::createFromFormat( 'Y-m-d H:i:s', '2018-12-09 23:52:21');
print $test_time->getTimestamp();
?>