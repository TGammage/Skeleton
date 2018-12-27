<?php

namespace SystemCore;

class CheckLogin extends check
{
	/** @var mysql Database handle */
	private	$db = null;

	/** @var int Member ID number */
	private	$user_ID = 0;

	/** @var string Return variables sent back to login page */
	private	$return_data = '';

	/**
	 *
	 * Constructor
	 * 
	 */
	public function __construct()
	{
		// Token Matching
		parent::key_check( 'login' );

		// Confirm Credentials
		$this->db = new \db( 'member', 'portal' );

			self::identifier_check();

			self::banned_check();

			self::password_check();

		$this->db = null;

		// Set up session
		self::new_login();
	}

	/**
	 * identifier_check()
	 *
	 * @purpose
	 *  To check for and validate identity portion of credentials.
	 *
	 * @return void
	 */
	private function identifier_check()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		if( !isset( $_POST['identify'] ) )
		{
			parent::fail( "Missing Identifier" );
			return;
		}

		$identifier = $GLOBALS['conf']->login['identify_by'];

		switch( $identifier )
		{
			case "username";

				if( !preg_match( \regex::USERNAME, $_POST['identify'] ) )
				{
					parent::fail( "Bad Pattern Identifier" );
					return;
				}

			break;

			case "email";

				require_once $GLOBALS['conf']->dir['php'] . "is_email.php";

				if( !is_email( $_POST['identify'] ) )
				{
					parent::fail( "Bad Pattern Identifier" );
					return;
				}

			break;

			default:

				parent::fail( "Configuration Setting Invalid" );
				return;

			break;
		}

		$query = "SELECT `id`, `lockout` FROM `member` WHERE `$identifier` = ? LIMIT 1";

		$param = array( $_POST['identify'] );

		$result = $this->db->query( $query, $param, \PDO::FETCH_ASSOC, true );

		if( !$result )
		{
			parent::fail( "Member Not Found" );
			return;
		}

		if( $result['lockout'] == 1 )
		{
			parent::fail( "Member Account Locked" );
			return;
		}

		$this->user_ID = $result['id'];
	}

	/**
	 * banned_check()
	 *
	 * @purpose
	 *  To determine if this account or ip is registered with a ban on the server
	 *
	 * @return void
	 */
	private function banned_check()
	{
		if( !$this->success )
			return;

		$query = "( SELECT `finish_time` FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`ip_banned` WHERE `ip` = ? LIMIT 1 ) UNION ALL ( SELECT `finish_time` FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`account_banned` WHERE `id` = ? LIMIT 1 )";

		$param = array( $_SERVER['REMOTE_ADDR'], $this->user_ID );

		$bans = $this->db->query( $query, $param, \PDO::FETCH_ASSOC );

		if( count( $bans ) > 0 )
		{
			foreach( $bans as $array )
			{
				$timestamp = date_create_from_format( "Y-m-d H:i:s", $array['finish_time'] );

				if( $timestamp->getTimestamp() >= $_SERVER['REQUEST_TIME'] )
				{
					parent::fail( "Member banned" );
					return;
				}
			}
		}
	}

	/**
	 * password_check()
	 *
	 * @purpose
	 *  To check for and validate password portion of credentials.
	 *
	 * @return void
	 */
	private function password_check()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		if( !isset( $_POST['access'] ) )
		{
			parent::fail( "Missing Password" );
			return;
		}

		if( !preg_match( \regex::PASSWORD, $_POST['access'] ) )
		{
			parent::fail( "Bad Pattern Password" );
			return;
		}

		$identifier = $GLOBALS['conf']->login['identify_by'];

		$query = "SELECT `access` FROM `member` WHERE `$identifier` = ? LIMIT 1";

		$param = array( $_POST['identify'] );

		$result = $this->db->query( $query, $param, \PDO::FETCH_ASSOC, true );

		$hasher = new PepperedPassword;

		if( !$hasher->verify( $_POST['access'], $result['access'] ) )
		{
			parent::fail( "Bad Password" );
		}
	}

	/**
	 * new_login()
	 *
	 * @purpose
	 * 	Commences new login
	 *
	 * @return void
	 */
	private function new_login()
	{
		$this->db = new \db( 'member' );

			self::generate_new_session();

			self::session_criticals();

			self::remove_account_recovery();

			self::max_session_restriction();

			self::set_cookie_hashes();

		$this->db = null;
	}

	/**
	 * generate_new_session()
	 *
	 * @purpose
	 *  Generates new session id and establishes new session in database
	 *
	 * @return void
	 */
	private function generate_new_session()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		// Prevent session fixation attacks, regenerates session_id()
		$GLOBALS['session']->session_kill();

		// Add session into database
		$query = "INSERT INTO `session` ( `user_id`,`session_id`,`user_agent`,`user_ip` ) VALUES ( ?, ?, ?, ? );";

		$param = array( $this->user_ID, session_id(), $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'] );

		$count = $this->db->query( $query, $param );

		// Verification of new session
		if( $count != 1 )
		{
			$query = "SELECT `user_id` FROM `session` WHERE `session_id` = ? AND `user_id` = " . $this->user_ID;

			$count = $this->db->query( $query, array( session_id() ) );

			if( $count == 0 )
			{
				parent::fail( "Failed to establish a new session in database." );
			}
		}
	}

	/**
	 * session_critical()
	 *
	 * @purpose
	 *  Grab common critical information from the databases
	 *
	 * @return void
	 */
	private function session_criticals()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$query = "SELECT * FROM `profile_info` WHERE `id` = " . $this->user_ID . " LIMIT 1";

		$criticals = $this->db->query( $query, null, \PDO::FETCH_ASSOC, true );

		$_SESSION['user']['id']				= $this->user_ID;
		$_SESSION['user']['name']			= $criticals['username'];
		$_SESSION['user']['first_name']		= $criticals['first_name'];
		$_SESSION['user']['last_name']		= $criticals['last_name'];
		$_SESSION['user']['email']			= $criticals['email'];
		$_SESSION['user']['email_verified']	= $criticals['email_verified'];
		$_SESSION['user']['last_updated']	= $criticals['last_updated'];
		$_SESSION['sec_level']				= $criticals['sec_level'];

	}

	/**
	 * remove_account_recovery()
	 *
	 * @purpose
	 * 	Remove any existing codes in the tmp database used for account recovery on a successful login
	 *
	 * @return void
	 */
	private function remove_account_recovery()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$query = "DELETE FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`account_recovery` WHERE `email` = ? LIMIT 1;";

		$this->db->query( $query, array( $_SESSION['user']['email'] ) );
	}

	/**
	 * max_session_restriction()
	 *
	 * @purpose
	 *  Generates new session id and establishes new session in member database
	 *
	 * @return void
	 */
	private function max_session_restriction()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		// Skip if config allows infinite sessions
		if( !$GLOBALS['conf']->session['simultaneous_count'] )
			return;

		$query = "SELECT `user_id` FROM `session` WHERE `user_id` =  " . $this->user_ID;

		$count = $this->db->query( $query );

		// Delete excess sessions if over the limit
		if( $count > $GLOBALS['conf']->session['simultaneous_count'] )
		{
			$remove_count		= $count - $GLOBALS['conf']->session['simultaneous_count'];
			$remove_technique	= $GLOBALS['conf']->session['max_bump_technique'];

			
			// Delete session files 
			$query = "SELECT `session_id` FROM `session` WHERE `user_id` =  " . $this->user_ID . " ORDER BY `$remove_technique` ASC LIMIT $remove_count";

			$sessions = $this->db->query( $query, null, \PDO::FETCH_NUM );

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

			// Delete sessions off database
			$query = "DELETE FROM `session` WHERE `user_id` =  " . $this->user_ID . " ORDER BY `$remove_technique` ASC LIMIT $remove_count";

			$this->db->query( $query );
		}
	}

	/**
	 * set_cookie_hashes()
	 *
	 * @purpose
	 *  Sets $_COOKIE hashes used to verify login during live session
	 *
	 * @return void
	 */
	private function set_cookie_hashes()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$_SESSION['session_hash'] = \random::string( 16 );

		setcookie(
			'id',
			$this->user_ID,
			time() + $GLOBALS['conf']->cookie['life'],
			'/',
			'',
			false,
			true
		);

		setcookie(
			'hash',
			md5( session_id() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $this->user_ID . $_SESSION['session_hash'] ),
			time() + $GLOBALS['conf']->cookie['life'],
			'/',
			'',
			false,
			true
		);
		$_SESSION['cookie_hash'] = md5( session_id() . $_SERVER['HTTP_USER_AGENT'] . $this->user_ID . $_SESSION['session_hash'] );
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
			header( "Location:" . $GLOBALS['conf']->host . "main.php" );
		} else {
			
			$url = $GLOBALS['conf']->host . "login.php";

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