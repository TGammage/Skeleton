<?php

namespace SystemCore;

class CheckLogout extends check
{
	/**
	 *
	 * Constructor
	 *
	 */
	public function __construct()
	{
		// Token Matching
		parent::key_check( 'logout', true );

		if( $this->success )
		{
			$GLOBALS['session']->session_force_logout();
		}
	}

	/**
	 * redirect()
	 *
	 * @purpose
	 *  Redirection after evaluation
	 *
	 * @return void
	 */
	public function redirect()
	{
		if( $this->success )
		{
			header( "Location:" . $GLOBALS['conf']->host . "login.php" );
		} else {
			
			$url = $GLOBALS['conf']->host . "main.php";

			$query ="";

			if( DEBUG )
			{
				$query = "?error=" . urlencode( $this->error_data );
			}

			header( "Location:$url$query" );
		}
	}
}
?>

