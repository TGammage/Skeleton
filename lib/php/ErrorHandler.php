<?php
/**
*		@class SystemCore\ErrorHandler()
*
*		@purpose
*			Broad spectrum error handling. Log error reports and debug if requested.
*			Note : Live Production Version will not allow debug to turn no, but will log errors.
*/
namespace SystemCore;


/** Error Reporting Toggle */

error_reporting( E_ALL );
#error_reporting( 0 );



/**
*		Error Action
*/
set_error_handler( function( $severity, $message, $file, $line )
{
	throw new ErrorHandler( $severity, $message, $file, $line );
});



class ErrorHandler extends \Exception
{
	/**
	 * Constructor
	 *
	 * @param int		severity	Error Severity
	 * @param string	message		Error Message
	 * @param string	file		File Causing Error
	 * @param int		line		Line Causing Error
	 */
	public function __construct( $severity = 0, $message = '', $file = 'not given', $line = 'unknown' )
	{
		parent::__construct( $message );

		$this->severity	= $severity;
		$this->file		= $file;
		$this->line		= $line;
		$this->date		= new \DateTime();


		if( DEBUG )
		{
			// Debug Visible Responses
			if( SYSTEM_REQUEST )
			{
				print self::report();
			} else {
				print self::report( 'html' );
			}
		}

		self::log_report();
	}

	/**
	*		log_report()
	*
	*		Logs modified version of error in a log named by today's date
	*/
	public function log_report()
	{
		// Error Log determined by date
		$filename = $GLOBALS['conf']->dir['error'] . '/' . $this->date->format( 'Y-m-d' ) . '.log';

		// Create new log if needed
		if( !is_file( $filename ) )
		{
			touch( $filename );
			chmod( $filename, '0660' );
		}

		$report = self::report();

		// Append to error log
		$f = fopen( $filename, 'a' );

		if( !fwrite( $f, $report ) )
		{
			fclose( $f );
			die( "Fatal Error : Not Logged" );
		};

		fclose( $f );
	}

	/**
	*		report()
	*
	*		@param	string	output
	*			Return text modifiations. Defaults line feed to "\r\n".
	*			HTML return will add a <div> prefix and </div> suffix
	*			with line feeds of "<br>".
	*/
	public function report( $output = 'text' )
	{
		// Timestamp of Error
		$time = $this->date->format( 'H:i:s' );

		// Report Output Format
		if( $output == 'html' )
		{
			$trace	= preg_replace( '/(#\d)/', "<br>$0", self::getTraceAsString() );

			$lf	= "<br>";
		} else {
			$trace	= preg_replace( '/&#10;/', "\r\n", self::getTraceAsString() );

			$lf = "\r\n";
		}

		// Where did this request come from
		$host = !SYSTEM_REQUEST ? $_SERVER['HTTP_HOST'] : "NO HOST (SYSTEM_REQUEST)";

		$request_info = "HTTP_HOST : $host{$lf}SCRIPT_NAME : {$_SERVER['SCRIPT_NAME']}{$lf}REQUEST_URI : {$_SERVER['REQUEST_URI']}$lf$lf";

		// Error Report Information
		$report = "================================================$lf";
		$report .= "Time : $time$lf";
		$report	.=	$request_info;
		$report .= "Severity : " . $this->severity . "$lf$lf";
		$report .= "File : " . $this->file . "(" . $this->line . ")$lf$lf";
		$report .= "Message :$lf'" . self::getMessage() . "'$lf$lf";
		$report .= "Trace :$lf$trace$lf$lf";

		// Prefix/Suffix Modifications
		if( $output == 'html' )
		{
			$prefix = "<div class='x-debug' style='/*display:none*/'>\r\n";
			$suffix = "\r\n</div>";

			$report = $prefix . $report . $suffix;
		}

		return $report;
	}
}

?>