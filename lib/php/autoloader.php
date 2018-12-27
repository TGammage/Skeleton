<?php
/*
 * Class Autoloader
 *
 * File name must Class name (case sensitive)
 */

spl_autoload_register(
	function( $incoming )
	{
		// Split up namespace and class
		$array = explode( '\\', $incoming );
		$class = array_pop( $array );
		$namespace = implode( '\\', $array );

		// Point to directory collectives for different Namespaces
		switch( $namespace )
		{
			// ROOT_DIR\lib\php
			case 'SystemCore' :
				$pathname = $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR;
			break;

			// ROOT_DIR\lib\php\Signup
			case 'SystemCore\Signup' :
				$pathname = $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR . "Signup" . DIRECTORY_SEPARATOR;
			break;

			// ROOT_DIR\lib\php\Profile
			case 'SystemCore\Profile' :
				$pathname = $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR . "Profile" . DIRECTORY_SEPARATOR;
			break;

			// ROOT_DIR\lib\html
			case 'HTML' :
				$pathname = $GLOBALS['conf']->dir['html'] . DIRECTORY_SEPARATOR;
			break;

			// ROOT_DIR\lib\php
			default :
				$pathname = $GLOBALS['conf']->dir['php'] . DIRECTORY_SEPARATOR;
			break;
		}

		// Filename and Path
		$file = $pathname . $class . '.php';

		// Determine if file exists
		if( is_file( $file ) )
		{
			// Load File
			require_once $file;
		} else {
			$array = debug_backtrace();

			throw new SystemCore\ErrorHandler( 1, "Autoloader could not find file : $file", $array[1]['file'], $array[1]['line'] );
		}
	});

/**
 *
 * Load Debug Module
 *
 */
if( DEBUG )
{
	require $GLOBALS['conf']->dir['dev'] . DIRECTORY_SEPARATOR . "debug.php";
}
?>