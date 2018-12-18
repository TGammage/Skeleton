<?php

namespace SystemCore;

class Session extends SessionFunctions
{
	/** @var int User DB ID */
	public		$user_ID = null;

	/** @var bool User has successfully logged in */
	public		$logged_in = false;

	/** @var bool User has been banned from site */
	protected	$banned = false;

	/** @var Database Access */
	private		$db	= null;


	/**
	 *
	 * Constructor
	 * 
	 */
	public function __construct()
	{
		if( !parent::init() )
		{
			throw new ErrorHandler( 2, 'Could Not Start Session', __file__, 27  );
		}

		self::session_login_check();

		// Life Extension of cookies (session)
		parent::session_extension();

		self::hash_extension();

		parent::session_regenerate_timer();

		self::update_cookie_hash();


		$this->db = new \db( 'member' );
			
			self::site_ban_check();

			self::member_functions();

		unset( $this->db );
	}

	/**
	 * session_login_check()
	 *
	 * @purpose
	 *  To check if client has successfully logged in with this session_id.
	 *
	 * @return void
	 */
	protected function session_login_check()
	{
		if(
			isset( $_COOKIE['hash'] )
		&&	isset( $_COOKIE['id'] )
		&&	isset( $_SESSION['session_hash'] )
		){
			$match = md5( session_id() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_COOKIE['id'] . $_SESSION['session_hash'] );

			if( $_COOKIE['hash'] === $match )
			{
				$this->logged_in = true;

				return;
			}
		}

		$this->logged_in = false;
	}

	/**
	 * hash_extension()
	 *
	 * @purpose
	 *  Will extend the hash and id cookie life determined by the configuration.
	 *
	 * @return void
	 */
	protected function hash_extension()
	{
		if( !$this->logged_in )
			return;

		if( $GLOBALS['conf']->cookie['extend'] )
		{
			setcookie( 
				'id',
				$_COOKIE['id'],
				time() + $GLOBALS['conf']->cookie['life'],
				'/',
				'',
				false,
				true
			);

			setcookie( 
				'hash',
				$_COOKIE['hash'],
				time() + $GLOBALS['conf']->cookie['life'],
				'/',
				'',
				false,
				true
			);
		}
	}

	/**
	 * update_cookie_hash()
	 *
	 * @purpose
	 *  Will update $_COOKIE['hash'] for staying logged in.
	 *  Use after regenerating session ID.
	 *
	 * @return void
	 */
	public function update_cookie_hash()
	{
		if( !$this->logged_in )
			return;

		// No changes to session ID, skip
		if( !$this->old_session_id )
			return;

		// Update the Cookie Hash
		setcookie(
			'hash',
			md5( session_id() . $_SERVER['HTTP_USER_AGENT'] . $_COOKIE['id'] . $_SESSION['session_hash'] ),
			time() + $GLOBALS['conf']->cookie['life'],
			'/',
			'',
			false,
			true
		);
	}

	/**
	 * site_ban_check()
	 *
	 * @purpose
	 *  To see if user has been banned from log in.
	 *
	 * @return void
	 */
	public function site_ban_check()
	{
		if( !$this->logged_in )
		{
			// Check for an ip ban only when not logged in
			$query = "SELECT `finish_time` FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`ip_banned` WHERE `ip` = ? LIMIT 1";

			$param = array( $_SERVER['REMOTE_ADDR'] );

			$ban = $this->db->query( $query, $param, \PDO::FETCH_ASSOC, true );

			if( $ban )
			{
				$timestamp = date_create_from_format( "Y-m-d H:i:s", $ban['finish_time'] );

				// Check expiration of ban
				if( $timestamp->getTimestamp() >= $_SERVER['REQUEST_TIME'] )
				{
					if( !preg_match( '/banned\.php/', $_SERVER['PHP_SELF'] ) )
					{
						header( "Location: " . $GLOBALS['conf']->host . "banned.php" );
						exit();
					}
				}
			}
		} else {

			$query = "( SELECT `finish_time` FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`ip_banned` WHERE `ip` = ? LIMIT 1 ) UNION ALL ( SELECT `finish_time` FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`account_banned` WHERE `id` = ? LIMIT 1 )";

			$param = array( $_SERVER['REMOTE_ADDR'], $_SESSION['user']['id'] );
	
			$bans = $this->db->query( $query, $param, \PDO::FETCH_ASSOC );
	
			if( count( $bans ) > 0 )
			{
				// Check expirations of bans
				foreach( $bans as $array )
				{
					$timestamp = date_create_from_format( "Y-m-d H:i:s", $array['finish_time'] );

					if( $timestamp->getTimestamp() >= $_SERVER['REQUEST_TIME'] )
					{
						self::session_force_logout( true );
						return;
					}
				}
			}
		}
	}

	/**
	 * member_functions()
	 *
	 * @purpose
	 *  Run common session functions for member who is currently logged in
	 *
	 * @return void
	 */
	private function member_functions()
	{
		if( !$this->logged_in )
			return;

		self::session_max_quantity();

		self::update_db_session_activity();

		self::update_session_user_info();
	}

	/**
	 * session_max_quantity()
	 *
	 * @purpose
	 *  To see if session is still live on database.
	 *  If a successful login creates an excessive amount of live sessions,
	 *  one will deleted to make room for new session. This will check if
	 *  current session is still valid on database. When configuration is
	 *  set to infinite simultaneous sessions, this check will be skipped.
	 *
	 * @return void
	 */
	protected function session_max_quantity()
	{

		if( !$GLOBALS['conf']->session['simultaneous_count'] )
			return;

		$query = "SELECT `last_active` FROM `session` WHERE `user_id` = ? AND `session_id` = ? LIMIT 1";

		// Use old id, when regenerated, because database has not been updated with new id yet
		$session_id	= $this->old_session_id ? $this->old_session_id : session_id();

		$param = array( $_SESSION['user']['id'], $session_id );

		$count = $this->db->query( $query, $param );

		// This is no longer a live session, logout
		if( $count == 0 )
		{
			self::session_force_logout();
		}
	}

	/**
	 * update_db_session_activity()
	 *
	 * @purpose
	 *  Will update the database with user activity info.
	 *
	 * @return void
	 */
	protected function update_db_session_activity()
	{
		$param		= array( 'knownID' => session_id(), 'userID' => $_SESSION['user']['id'] );
		$addendum	= '';

		// Update to new session id when regenerated
		if( $this->old_session_id )
		{
			$addendum = ", `session_id` = :newID";

			$param['knownID']	= $this->old_session_id;
			$param['newID']		= session_id();
		}

		$query = "UPDATE `session` SET `last_active` = NOW()$addendum WHERE `user_id` = :userID AND `session_id` = :knownID LIMIT 1";

		$this->db->query( $query, $param );
	}

	/**
	 * update_session_user_info()
	 *
	 * @purpose
	 *  Will update the user information in $_SESSION if necessary.
	 *
	 * @return void
	 */
	public function update_session_user_info()
	{
		$query = "SELECT `last_updated` FROM `member` WHERE `id` = ? LIMIT 1";

		$last_updated = $this->db->query( $query, array( $_SESSION['user']['id'] ), \PDO::FETCH_NUM, true );

		$timestamp = strtotime( $last_updated[0] );

		if( $timestamp > $_SESSION['user']['last_updated'] )
		{
			$query = "SELECT `username`, `email`, `sec_level` FROM `member` WHERE `id` = ? LIMIT 1";

			$update = $this->db->query( $query, array( $_SESSION['user']['id'] ), \PDO::FETCH_ASSOC, true );

			$_SESSION['user']['name']			= $update['username'];
			$_SESSION['user']['email']			= $update['email'];
			$_SESSION['user']['last_updated']	= $timestamp;
			$_SESSION['sec_level']				= $update['sec_level'];
		}
	}

	/**
	 * session_force_logout()
	 *
	 * @purpose
	 *  Will log out member from site.
	 *
	 * @param	bool	$all	Force logout of every live session tied to member
	 *
	 * @return void
	 */
	public function session_force_logout( $all = false )
	{
		// No longer logged in
		$this->logged_in = false;

		$db = new \db( 'member' );

		// Update Location
		new SiteLocation( 'Logout', $db );

		$user_ID = $_SESSION['user']['id'];

		// Delete session files for all sessions tied to this account ( ban purposes )
		if( $all )
		{
			$query = "SELECT `session_id` FROM `session` WHERE `user_id` =  ?";

			$sessions = $db->query( $query, array( $user_ID ), \PDO::FETCH_NUM );

			$retain_sesion_id = session_id();
			session_commit();

			foreach( $sessions as $id )
			{
				session_id( $id[0] );
				session_start();
				session_destroy();
				session_commit();
			}

			// Session restoration
			session_id( $retain_sesion_id );
			session_start();
		}

		// Delete session(s) from database
		$query = "DELETE FROM `session` WHERE `user_id` = :userID";

		$param = array( 'userID' => $user_ID );

		// Slim down parameters to find just this session
		if( !$all )
		{
			$query .= " AND ( `session_id` = :ID ";

			$param['ID'] = session_id();

			if( strlen( $this->old_session_id ) > 0 )
			{
				$query .= " OR `session_id` = :oldID )";

				$param['oldID'] = $this->old_session_id;
			} else {
				$query .= ")";
			}
		}

		$db->query( $query, $param );

		// Destroy Cookies
		setcookie(
			'id',
			'',
			time() - 1,
			'/',
			'',
			false,
			true
		);

		setcookie(
			'hash',
			'',
			time() - 1,
			'/',
			'',
			false,
			true
		);

		// Destroy Session
		parent::session_kill();
	}
}

?>