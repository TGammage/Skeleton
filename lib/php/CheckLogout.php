<?php

namespace SystemCore;

class CheckLogout extends check
{
	/**
	*
	*		Constructor
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
	*		redirect()
	*
	*		@purpose
	*			Redirection after evaluation
	*
	*		@return void
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

/**

INSERT INTO `session` (`session_id`,`user_id`,`user_agent`) VALUES
('ho3ci1oe2399raejmk0olcihp0', 1, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36'),
('ho3ci1oe2399raejmk0olcihp6', 1, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36'),
('ho3ci1oe2399raejmk0olcihp7', 1, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36'),
('ho3ci1oe2399raejmk0olcihp8', 1, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36'),
('ho3ci1oe2399raejmk0olcihp9', 1, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36')



*/
?>

