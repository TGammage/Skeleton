<?php
/**
 *		@filename start.php
 *
 *		@purpose
 *			To setup and initialize backbone of the site.
 */


define( 'SITE_DIRECTORY', 'Skeleton' );


/**
 *
 *		Debug Mode
 *
 */


$debug = true;
#$debug = @$_GET['debug'] == true ? true : false;


/**
 *
 *		Site Definitions
 *
 */


require 'lib/php/definitions.php';


/**
 *
 *		Configuration
 *
 */


require ROOT_DIR . DIRECTORY_SEPARATOR . 'lib/x/config.php';

$GLOBALS['conf'] = new SystemCore\Config;


/**
 *
 *		Error Handler
 *
 */


require $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR . 'ErrorHandler.php';


/**
 *
 *		Autoloader
 *
 */


require $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR . 'autoloader.php';


/**
 *
 *		Session
 *
 */

if( !SYSTEM_REQUEST )
	$GLOBALS['session'] = new SystemCore\Session\Session();


/**
 *
 *		Logged In Restriction
 *
 */


if( defined( 'LOGGEDINONLY' ) && LOGGEDINONLY && !$GLOBALS['session']->logged_in )
{
	header( 'Location:' . $GLOBALS['conf']->host );
	exit();
}



/**
 *
 *		Site Location
 *
 */


if( defined( 'LOCATION' ) && $GLOBALS['session']->logged_in )
	new SystemCore\Session\SiteLocation( LOCATION );




//var_dump( $_SERVER );

?>
