<?php

namespace SystemCore;

class check
{
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
	*		@return void
	*/
	protected function key_check( $key_name, $url_key_only = false )
	{
		// Client Side Token
		if( !isset( $_GET['unique'] ) )
		{
			self::fail( "Missing URL Key" );
		}

		// String to match against token
		if( !isset( $_SESSION['url_key'][ $key_name ] ) )
		{
			self::fail( "Missing Session URL Key" );
		}

		// Skip if not looking for $_POST['unique']
		if( !$url_key_only )
		{
			// Client Side Token
			if( !isset( $_POST['unique'] ) )
			{
				self::fail( "Missing VAR Key" );
			}

			// String to match against token
			if( !isset( $_SESSION['var_key'][ $key_name ] ) )
			{
				self::fail( "Missing Session VAR Key" );
			}			
		}

		// Return Now if missing keys
		if( !$this->success )
			return;

		// Token matches?
		if( $_SESSION['url_key'][ $key_name ] !== $_GET['unique'] )
		{
			self::fail( "Mismatch URL Key" );
		}

		// Skip if not looking to match $_POST['unique']
		if( !$url_key_only )
		{
			// Token matches?
			if( $_SESSION['var_key'][ $key_name ] !== $_POST['unique'] )
			{
				self::fail( "Mismatch VAR Key" );
			}
		}
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
			$this->error_data .= "|$message";
		} else {
			$this->error_data = $message;
		}
	}
}
?>