<?php

namespace SystemCore\Login;

class CheckLogout extends \SystemCore\check
{
	/**
	 *
	 * Constructor
	 *
	 */
	public function __construct()
	{
		// Token Matching
		if( parent::key_check( 'logout', true ) )
			$GLOBALS['session']->session_force_logout();
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

