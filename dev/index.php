<?php
require "../start.php";

$PAGE = new HTML\Frame();

$PAGE->begin();

var_dump( $GLOBALS['session']->logged_in );

$PAGE->end();

?>