<?php

namespace SystemCore\Signup;

class CheckSignup extends \SystemCore\check
{
	/** @var mysql Database handle */
	private	$db = null;

	/** @var int Member ID number */
	private	$user_ID = 0;

	/** @var string Verification code for email */
	private	$verification_code = null;

	/** @var string Token (unique identifier for email verification) */
	private	$token = null;

    /** @var array All possible incoming names for information */
    private $possible_input = array(
		'username'			=> 'mandatory',
		'email'				=> 'mandatory',
		'first_name'		=> 'optional',
		'last_name'			=> 'optional',
		'access'			=> 'mandatory',
		'confirm_access'	=> 'mandatory'
    );

    /** @var array Submitted information for updating */
    private $submitted_input = array();

	/**
	 *
	 * Constructor
	 *
	 */
	public function __construct()
	{
		// Token Matching
		if( parent::key_check( 'signup' ) )
		{
			self::vital_check();

			$this->db = new \db( 'member' );

				self::db_check();

				// Set up session
				self::add_member();

			$this->db = null;
		}

		self::redirect();
	}

	/**
	 * vital_check()
	 *
	 * @purpose
	 *	To check patterns of incoming information
	 *
	 * @return void
	 */
	private function vital_check()
	{
		// Empty Check
		foreach( $this->possible_input as $key => $required )
		{
			if( $required === 'mandatory' )
			{
				// Mandatory Inputs
				if( !isset( $_POST[ $key ] ) )
				{
					switch( $key )
					{
						case 'username'			: parent::fail( SignupError::MISSING_USERNAME );break;
						case 'email'			: parent::fail( SignupError::MISSING_EMAIL ); break;
						case 'access'			: parent::fail( SignupError::MISSING_PASSWORD ); break;
						case 'confirm_access'	: parent::fail( SignupError::MISSING_CONFIRM ); break;
					}
				}
				elseif( strlen( $_POST[ $key ] ) == 0 )
				{
					switch( $key )
					{
						case 'username'			: parent::fail( SignupError::EMPTY_USERNAME );break;
						case 'email'			: parent::fail( SignupError::EMPTY_EMAIL ); break;
						case 'access'			: parent::fail( SignupError::EMPTY_PASSWORD ); break;
						case 'confirm_access'	: parent::fail( SignupError::EMPTY_CONFIRM ); break;
					}
				} else {
					
					// Input Submitted, skip confirmation of password
					if( $key !== 'confirm_access' )
						$this->submitted_input[ $key ] = $_POST[ $key ];
				}
			} else {
				// Optional Inputs
				if( isset( $_POST[ $key ] ) && strlen( $_POST[ $key ] ) > 0 )
				{
					$this->submitted_input[ $key ] = $_POST[ $key ];
				}
			}
		}

		// When empty or missing one of the mandatory variables, skip next
		if( !$this->success )
			return;

		// Pattern Checks
		foreach( $this->submitted_input as $key => $value )
		{
			switch( $key )
			{
				case 'username':

					if( !preg_match( \regex::USERNAME, $value ) )
						parent::fail( SignupError::FORMAT_USERNAME );

				break;

				case 'email':

					require $GLOBALS['conf']->dir['php'] . "/is_email.php";

					if( !is_email( $value ) )
						parent::fail( SignupError::INVALID_EMAIL );

				break;

				case 'access':

					if( !preg_match( \regex::PASSWORD, $value ) )
						parent::fail( SignupError::FORMAT_PASSWORD );

					if( $value !== $_POST['confirm_access'] )
						parent::fail( SignupError::CONFIRM_PASSWORD );

				break;

				case 'first_name':

					if( !preg_match( \regex::FIRST_NAME, $value ) )
						parent::fail( SignupError::FORMAT_FIRST_NAME );

				break;

				case 'last_name':

					if( !preg_match( \regex::LAST_NAME, $value ) )
						parent::fail( SignupError::FORMAT_LAST_NAME );

				break;
			}
		}
	}

	/**
	 * db_check()
	 *
	 * @purpose
	 *	To check database for existing accounts with signup attempt
	 *
	 * @return void
	 */
	private function db_check()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		// Duplicate Email Check
		$query = "SELECT `id` FROM `member` WHERE `email` = ? LIMIT 1";

		$param = array( $_POST['email'] );

		$count = $this->db->query( $query, $param );

		if( $count > 0 )
			parent::fail( SignupError::DUPLICATE_EMAIL );

		// Duplicate Username Check
		$query = "SELECT `id` FROM `member` WHERE `username` = ? LIMIT 1";

		$param = array( $_POST['username'] );

		$count = $this->db->query( $query, $param );

		if( $count > 0 )
			parent::fail( SignupError::DUPLICATE_USERNAME );
	}

	/**
	 * add_member()
	 *
	 * @purpose
	 *	Commences new member addition. Send email for verfication, add to database,
	 *
	 * @return void
	 */
	private function add_member()
	{
		self::new_member_database_insertions();

		if( $GLOBALS['conf']->login['signup'] )
			self::new_member_login();
	}

	/**
	 * new_member_database_insertions()
	 *
	 * @purpose
	 *	Create new member on databases
	 *
	 * @return void
	 */
	private function new_member_database_insertions()
	{
		// Skip on prior failure
		if( !$this->success )
			return;

		$hasher = new \SystemCore\PepperedPassword;

		$this->submitted_input['access'] = $hasher->hash( $_POST['access'] );

		// Add new member to the member database
		$query = "INSERT INTO `member` ( ";
		$value = " ) VALUES ( ";
		$param = array();

		$x = 0;

		foreach( $this->submitted_input as $key => $input )
		{
			if( $x > 0 )
			{
				$query .= ',';
				$value .= ',';
			} 

			$query .= "`$key`";
			$value .= ":$key";

			$param[ $key ] = $input;

			$x++;
		}

		$value .= " )";
		$query .= $value;

		$count = $this->db->query( $query, $param );

		if( $count != 1 )
		{
			parent::fail( SignupError::MEMBER_DB_INSERTION );
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
			parent::fail( SignupError::MAIN_DB_INSERTION );
			return;
		}

		// New member email verification
		$mail = new SignupEmail( $_POST['email'], $_POST['username'] );
		var_dump( $mail->sent );

		if( !$mail->sent )
		{
			parent::fail( SignupError::SEND_EMAIL );
			return;
		}

		// Unlock new member in member database
		$query = "UPDATE `member` SET `lockout` = 0 WHERE `id` = " . $this->user_ID . " LIMIT 1";

		$count = $this->db->query( $query );

		if( $count != 1 )
			parent::fail( Signup::MEMBER_UNLOCK );
	}

	/**
	 * new_member_login()
	 *
	 * @purpose
	 *	First time login of new member
	 *
	 * @return void
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

		new \SystemCore\CheckLogin;
	}

	/**
	 * redirect()
	 *
	 * @purpose
	 *	Redirection after evaluation
	 *
	 * @return void
	 */
	private function redirect()
	{
		if( $this->success )
		{
			// Success
			$append = SignupError::NO_ERROR;
		} else {
			//Failure
			$append = $this->error_data;
		}

		header( "Location:signup.php?response=$append" );
		// print( "Location:signup.php?response=$append" );
	}
}
?>