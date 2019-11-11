<?php

/**
 * Page Specific Definitions
 */
define( 'LOCATION', 'Mock Page' );

require "start.php";


$PAGE = new HTML\Frame();

/**
 * Add Modules
 */
# $PAGE->js[]   = 'filepath and filename';
# $PAGE->css[]  = 'filepath and filename';

/**
 * Empty Modules
 */
# $PAGE->js     = null;
# $PAGE->css    = null;

/**
 * Add Raw
 */
# $PAGE->addJs();
# $PAGE->addCss();

/**
 * Change Title
 */
# $PAGE->title = "New Title";

/**
 * Output Blank Page
 */
# $PAGE->blank = true;

/**
 * Add Page Content
 */
# $PAGE->addContent();

/**
 * Output HTML Page
 */
$PAGE->output();

?>