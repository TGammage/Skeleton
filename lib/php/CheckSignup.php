<?php

namespace SystemCore;

class CheckSignup extends check
{
	/** @var mysql Database handle */
	private	$db = null;

	/** @var int Member ID number */
	private	$user_ID = 0;

	/** @var string Verification code for email */
	private	$verification_code = null;

	/** @var string Token (unique identifier for email verification) */
	private	$token = null;

	/** @var string Return variables sent back to login page */
	private	$return_data = '';

	/**
	*
	*		Constructor
	*
	*/
	public function __construct()
	{
		// Token Matching
		parent::key_check( 'signup' );

		self::pattern_check();

		$this->db = new \db( 'member' );

			self::db_check();

			// Set up session
			self::add_member();

		$this->db = null;

		self::redirect();
	}

	/**
	*		pattern_check()
	*
	*		@purpose
	*			To check patterns of incoming information
	*
	*		@return void
	*/
	private function pattern_check()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		// Empty Check
		if( !isset( $_POST['username'] ) )
		{
			parent::fail( "Missing Username" );
		}

		if( !isset( $_POST['email'] ) )
		{
			parent::fail( "Missing Email" );
		}

		if( !isset( $_POST['access'] ) )
		{
			parent::fail( "Missing Password" );
		}

		if( !isset( $_POST['confirm_access'] ) )
		{
			parent::fail( "Missing Password Confirmation" );
		}

		// When missing one of the variables, skip next
		if( !$this->success )
			return;

		// Pattern Checks
		if( !preg_match( $GLOBALS['conf']->regex['username'], $_POST['username'] ) )
		{
			parent::fail( "Bad Pattern Username" );
		}

		if( !preg_match( $GLOBALS['conf']->regex['password'], $_POST['access'] ) )
		{
			parent::fail( "Bad Pattern Password" );
		}

		if( $_POST['access'] !== $_POST['confirm_access'] )
		{
			parent::fail( "Password Confirmation Mismatch" );
		}

		require $GLOBALS['conf']->dir['php'] . "/is_email.php";

		if( !is_email( $_POST['email'] ) )
		{
			parent::fail( "Unaccepted Email" );
		}
	}

	/**
	*		db_check()
	*
	*		@purpose
	*			To check database for existing accounts with signup attempt
	*
	*		@return void
	*/
	private function db_check()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$query = "SELECT `id` FROM `member` WHERE `email` = ? LIMIT 1";

		$param = array( $_POST['email'] );

		$count = $this->db->query( $query, $param );

		if( $count > 0 )
		{
			parent::fail( "Email exists in database" );
		}

		$query = "SELECT `id` FROM `member` WHERE `username` = ? LIMIT 1";

		$param = array( $_POST['username'] );

		$count = $this->db->query( $query, $param );

		if( $count > 0 )
		{
			parent::fail( "Username exists in database" );
		}
	}

	/**
	*		add_member()
	*
	*		@purpose
	*			Commences new member addition. Send email for verfication, add to database,
	*
	*		@return void
	*/
	private function add_member()
	{
		self::new_member_database_insertions();
		self::new_member_login();
	}

	/**
	*		new_member_database_insertions()
	*
	*		@purpose
	*			Create new member on databases
	*
	*		@return void
	*/
	private function new_member_database_insertions()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$hasher = new PepperedPassword;

		$password = $hasher->hash( $_POST['access'] );

		// Add new member to the member database
		$query = "INSERT INTO `member` ( `username`, `email`, `access` ) VALUES ( ?, ?, ? );";

		$param = array( $_POST['username'], $_POST['email'], $password );

		$count = $this->db->query( $query, $param );

		if( $count != 1 )
		{
			parent::fail( "Failed to create new member in member database" );
			return;
		}

		// Get ID of new member
		$query = "SELECT `id` FROM `member` WHERE `username` = ? AND `email` = ? LIMIT 1";

		$param = array( $_POST['username'], $_POST['email'] );

		$result = $this->db->query( $query, $param, \PDO::FETCH_NUM, true );

		$this->user_ID = $result[0];

		// Add member to the main database as well using ID from member database
		$query = "INSERT INTO `" . $GLOBALS['conf']->db['main'] . "`.`member` ( `id` ) VALUES ( {$result[0]} );";

		$count = $this->db->query( $query );

		if( $count != 1 )
		{
			parent::fail( "Failed to create new member in main database" );
			return;
		}

		// New member email verification
		if( !new SignupEmail( $_POST['email'], $_POST['username'] ) )
		{
			parent::fail( 'Could not generate new email for verification' );
			return;
		}

		// Unlock new member in member database
		if( !$this->success )
			return;

		$query = "UPDATE `member` SET `lockout` = 0 WHERE `id` = " . $this->user_ID . " LIMIT 1";

		$count = $this->db->query( $query );

		if( $count != 1 )
		{
			parent::fail( "Failed to unlock new member" );
		}
	}

	/**
	*		new_member_login()
	*
	*		@purpose
	*			First time login of new member
	*
	*		@return void
	*/
	private function new_member_login()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		// Logout 
		if( $GLOBALS['session']->logged_in )
			$GLOBALS['session']->session_force_logout();

		// Login Essentials
		$_POST['identify']				= $_POST[ $GLOBALS['conf']->login['identify_by'] ];
		$_GET['unique']					= 'asd';
		$_POST['unique']				= 'asd';
		$_SESSION['url_key']['login']	= 'asd';
		$_SESSION['var_key']['login']	= 'asd';

		new CheckLogin;
	}

	/**
	*		redirect()
	*
	*		@purpose
	*			Redirection after evaluation
	*
	*		@return void
	*/
	private function redirect()
	{
		if( $this->success )
		{
			header( "Location:" . $GLOBALS['conf']->host . "main.php" );
		} else {
			
			$url = $GLOBALS['conf']->host . "signup.php";

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