<?php

namespace SystemCore;

class check
{
    /**
     * Error Constants
     */
    const NO_ERROR          	= 100;
    const MISSING_URL_TOKEN 	= 101;
    const MISSING_URL_KEY   	= 102;
    const MISSING_VAR_TOKEN 	= 103;
    const MISSING_VAR_KEY   	= 104;
    const MISMATCH_URL_KEY  	= 105;
    const MISMATCH_VAR_KEY		= 106;


	/** @var bool Failure trigger */
	protected	$success = true;

	/** @var string Error Message sent back to login page when in DEBUG */
	protected	$error_data = '';

	/**
	*		key_check()
	*
	*		@purpose
	*			To check for and validate unique keys. XSS protection
	*
	*		@param string	$key_name		Name of key we are looking for in $_SESSION
	*		@param bool		$url_key_only	Determines whether we check for a $_POST['unique'] token as well
	*
	*		@return bool
	*/
	protected function key_check( $key_name, $url_key_only = false )
	{
		// Client Side Token
		if( !isset( $_GET['unique'] ) )
		{
			self::fail( self::MISSING_URL_TOKEN );
		}

		// String to match against token
		if( !isset( $_SESSION['url_key'][ $key_name ] ) )
		{
			self::fail( self::MISSING_URL_KEY );
		}

		// Skip if not looking for $_POST['unique']
		if( !$url_key_only )
		{
			// Client Side Token
			if( !isset( $_POST['unique'] ) )
			{
				self::fail( self::MISSING_VAR_TOKEN );
			}

			// String to match against token
			if( !isset( $_SESSION['var_key'][ $key_name ] ) )
			{
				self::fail( self::MISSING_VAR_KEY );
			}			
		}

		// Return Now if missing keys
		if( !$this->success )
			return;

		// Token matches?
		if( $_SESSION['url_key'][ $key_name ] !== $_GET['unique'] )
		{
			self::fail( self::MISMATCH_URL_KEY );
		}

		// Skip if not looking to match $_POST['unique']
		if( !$url_key_only )
		{
			// Token matches?
			if( $_SESSION['var_key'][ $key_name ] !== $_POST['unique'] )
			{
				self::fail( self::MISMATCH_VAR_KEY );
			}
		}

		return $this->success;
	}

	/**
	*		fail()
	*
	*		@purpose
	*			Trigger failure for login
	*
	*		@param string $message Error message
	*
	*		@return void
	*/
	protected function fail( $message )
	{
		$this->success = false;

		self::error( $message );
	}

	/**
	*		error()
	*
	*		@purpose
	*			To build string for return query when in DEBUG
	*
	*		@param string $message Error message
	*
	*		@return void
	*/
	protected function error( $message )
	{
		if( strlen( $this->error_data ) > 1 )
		{
			$this->error_data .= "+$message";
		} else {
			$this->error_data = $message;
		}
	}
}
?>