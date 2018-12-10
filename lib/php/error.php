<?php

namespace SystemCore;

error_reporting( E_ALL );
#error_reporting( 0 );

set_error_handler( function( $severity, $message, $file, $line ){
	throw new SystemCore\ErrorHandler( $severity, $message, $file, $line );
});



class ErrorHandler extends \Exception
{
	public function __construct( $severity = 0, $message = '', $file = 'not given', $line = 'unknown' )
	{
		parent::__construct( $message );

		$this->severity	= $severity;
		$this->file		= $file;
		$this->line		= $line;
		$this->date		= new \DateTime();

		/*
		print("================================================<br><br>");

		var_dump( $this->message );
		var_dump( $this->code );
		var_dump( $this->file );
		var_dump( $this->line );
		var_dump( $this->getMessage() );
		var_dump( $this->getCode() );
		var_dump( $this->getFile() );
		var_dump( $this->getLine() );
		var_dump( $this->getTrace() );
		var_dump( $this->getTraceAsString() );
		var_dump( $this->__toString() );
		*/

		if( DEBUG && !SYSTEM_REQUEST )
		{
			if( SYSTEM_REQUEST )
			{
				print self::report();
			} else {
				print self::report( 'html' );
			}
		}

		self::log_report();
	}

	public function log_report( $date = false )
	{
		$filename = $GLOBALS['conf']->dir['error'] . '/' . $this->date->format( 'Y-m-d' ) . '.log';

		// Create new log if needed
		if( !is_file( $filename ) )
		{
			touch( $filename );
			chmod( $filename, '0640' );
		}

		$report = self::report();

		$f = fopen( $filename, 'a' );

		if( !fwrite( $f, $report ) )
		{
			fclose( $f );
			die( "Fatal Error : Not Logged" );
		};

		fclose( $f );
	}

	public function report( $output = 'text' )
	{
		$time 	= $this->date->format( 'H:i:s' );

		//print( self::getTraceAsString() );

		if( $output == 'html' )
		{
			$trace	= preg_replace( '/(#\d)/', "<br>$0", self::getTraceAsString() );
		} else {
			$trace	= preg_replace( '/&#10;/', "\r\n", self::getTraceAsString() );
		}

		if( !SYSTEM_REQUEST )
		{
			$host = "HTTP_HOST : {$_SERVER['HTTP_HOST']}\r\nSCRIPT_NAME : {$_SERVER['SCRIPT_NAME']}\r\nREQUEST_URI : {$_SERVER['REQUEST_URI']}\r\n\r\n";
		}

		$report = "================================================\r\n";
		$report .= "Time : " . $time . "\r\n\r\n";
		$report	.=	$host;
		$report .= "Severity : " . $this->severity . "\r\n\r\n";
		$report .= "File : " . $this->file . "(" . $this->line . ")\r\n\r\n";
		$report .= "Message :\r\n'" . self::getMessage() . "'\r\n\r\n";
		$report .= "Trace :\r\n$trace\r\n\r\n";

		if( $output == 'html' )
		{
			$prefix = "<div class='x-debug' style='/*display:none*/'>\r\n";
			$suffix = "\r\n</div>";

			$report = $prefix . preg_replace( '/\r\n/', '<br>', $report ) . $suffix;
		}

		return $report;
	}
}

?>