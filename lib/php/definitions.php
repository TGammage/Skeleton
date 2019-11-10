<?php
	
/*
*		Definitions			@var_type
*		
*		OS					string 'linux'|'windows'|'undefined'
*		SYSTEM_REQUEST		boolean
*		IS_TICK				boolean
*		IS_DEV				boolean
*		IS_LOCAL			boolean
*		DEBUG				boolean
*/


/** SYSTEM_REQUEST */
if( !isset( $_SERVER['SERVER_SOFTWARE'] ) )
{
	define( 'SYSTEM_REQUEST', true );
} else {
	define( 'SYSTEM_REQUEST', false );
}

/** OS */
if( SYSTEM_REQUEST )
{
	if( isset( $_SERVER['argv'][2] ) )
	{
		define( 'OS', $_SERVER['argv'][2] );
	} else {
		define( 'OS', 'undefined' );
	}
} else {
	switch( true )
	{
		case ( preg_match( "/win64/i", $_SERVER['SERVER_SOFTWARE'] ) ) :
			define( 'OS', 'windows');
		break;

		case ( preg_match( "/ubuntu/i", $_SERVER['SERVER_SOFTWARE'] ) ) :
			define( 'OS', 'linux');
		break;

		default :
			define( 'OS', 'undefinied' );
		break;
	}
}

/** ROOT_DIR */
if( !SYSTEM_REQUEST )
{
	// Document root from http access
	define( 'ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] );
} else {
	// Changed to current working directory in tick
	define( 'ROOT_DIR', getcwd() );
}



// IS_TICK
if( SYSTEM_REQUEST )
{
	$path_to_tick_file = ROOT_DIR . DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, array( 'lib', 'php', 'tick', 'tick.php' ) );

	if( $_SERVER['PHP_SELF'] === $path_to_tick_file )
	{
		define( "IS_TICK", true );
	} else {
		define( "IS_TICK", false );
	}
} else {
	define( "IS_TICK", false );
}

/** IS_DEV */
if( !SYSTEM_REQUEST )
{
	if( substr( $_SERVER['HTTP_HOST'], 0, 3 ) === 'dev' )
	{
		define( 'IS_DEV', true );
	} else {
		define( 'IS_DEV', false );
	}
} else {
	define( 'IS_DEV', true );
}


/** IS_LOCAL */
if( !SYSTEM_REQUEST )
{
	if( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' )
	{
		define( "IS_LOCAL", true );
	} else {
		define( "IS_LOCAL", false );
	}
} else 
{
	define( "IS_LOCAL", true );
}


/** DEBUG */
if( IS_LOCAL || IS_DEV )
{
	if( $debug )
	{
		define( "DEBUG", true );
	} else {
		define( "DEBUG", false );
	}
} else {
	define( "DEBUG", false );
}

?>