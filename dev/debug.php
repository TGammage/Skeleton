<?php


/**
*		debug_dump()
*
*		@purpose
*			Does a var_dump() on a dark background to save my eyes
*
*		@var mixed $_ Dumped variable
*/
function debug_dump( $_ )
{
	$PAGE = new HTML\Frame();

	unset( $PAGE->css );
	unset( $PAGE->js );

	$PAGE->css	= array( "core" );
	$PAGE->js	= array();

	$PAGE->blank_page = true;

	$PAGE->begin();

	var_dump( $_ );

	$PAGE->end();
}

?>