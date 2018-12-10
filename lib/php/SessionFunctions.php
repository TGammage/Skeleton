<?php

namespace SystemCore;

class SessionFunctions
{
	/** @var string Old Session ID (for regeneration recording) */
	protected	$old_session_id = false;


	/**
	*		init()
	*
	*		@purpose
	*			Start a session and handle parameters
	*
	*		@return	bool
	*/
	protected function init()
	{
		// Session ID Cookie Name
		session_name( $GLOBALS['conf']->cookie['session_name'] );

		session_set_cookie_params(
			// Expiration
			$GLOBALS['conf']->cookie['life'],
			// Path
			'/',
			// Domain ( Empty string will force HTTP_HOST as domain )
			'',
			// Secure (cookie sent only over secure connections)
			false,
			// HTTP Only (No Javascript Access)
			true
		);

		session_save_path( $GLOBALS['conf']->dir['session'] );

		return session_start();
	}

	/**
	*		session_extension()
	*
	*		@purpose
	*			Will extend the session cookie life determined by the configuration.
	*
	*		@return	void
	*/
	protected function session_extension()
	{
		if( $GLOBALS['conf']->cookie['extend'] )
		{
			setcookie( 
				$GLOBALS['conf']->cookie['session_name'],
				session_id(),
				time() + $GLOBALS['conf']->cookie['life'],
				'/',
				'',
				false,
				true
			);
		}
	}

	/**
	*		session_regenerate_timer()
	*
	*		@purpose
	*			To reduce the chances of a session fixation attack
	*
	*		@param bool $delete Delete old session id
	*
	*		@return	void
	*/
	public function session_regenerate_timer( $delete = true )
	{
		if( isset( $_SESSION['id_life_start'] ) )
		{
			$time_difference = $_SERVER['REQUEST_TIME'] - $_SESSION['id_life_start'];

			if( $time_difference > $GLOBALS['conf']->session['regenerate_after'] )
			{
				$this->old_session_id = session_id();

				session_regenerate_id( $delete );

				$_SESSION['id_life_start'] = $_SERVER['REQUEST_TIME'];
			}
		} else {
			$_SESSION['id_life_start'] = $_SERVER['REQUEST_TIME'];
		}
	}

	/**
	*		session_kill()
	*
	*		@purpose
	*			To truncate $_SESSION.
	*
	*		@param bool $destroy Destroys old session
	*
	*		@return	bool
	*/
	public function session_kill( $delete = true )
	{
		if( isset( $_COOKIE[ $GLOBALS['conf']->cookie['session_name'] ] ) )
		{
			setcookie(
				$GLOBALS['conf']->cookie['session_name'],
				'',
				time() - 1,
				'/'
			);
		}

		$_SESSION = array();

		$cleared = empty( $_SESSION );

		session_regenerate_id( $delete );

		$_SESSION['id_life_start'] = $_SERVER['REQUEST_TIME'];

		return $cleared;
	}
}
?>