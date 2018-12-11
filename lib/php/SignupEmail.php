<?php

namespace SystemCore;

class SignupEmail extends email
{
	/** @var string Unique identifier used to determining which email we are verifying */
	private	$token = null;

	/** @var string Verification code for email */
	private	$code = null;

	/**
	 * Constructor
	 *
	 * @purpose
	 *   Organizes tmp database for email verification, then sends signup email
	 *
	 * @param	string $email		Email address for new signup
	 * @param	string $username	Desired username for the new signup
	 *
	 * @return bool
	 */
	public function __construct( $email, $username )
	{
		if( !self::db_update( $email ) )
			return;

		parent::to( $email, $username );

		parent::from( "signup@" . $GLOBALS['conf']->base_domain, "System at " . $GLOBALS['conf']->site_title );

		parent::subject( "Welcome " . $username );

		parent::message( self::HTML(), parent::MIME_HTML );

		parent::send();
	}

	/**
	 * db_update()
	 *
	 * @purpose
	 *  Inserts needed data for verification to the tmp database.
	 *
	 * @param	string	$email	Email address for new member, assume unclean data
	 *
	 * @return bool
	 */
	private function db_update( $email )
	{
		// Add email verification code to email verify list
		$this->code	= \random::string( 32 );
		$tomorrow	= date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + ( 60 * 60 * 24 ) );

		$db =  new \db( 'tmp' );

		$query = "DELETE FROM `email_verify` WHERE `email` = ?";

		$db->query( $query, array( $email ) );

		do
		{
			$this->token = \random::string( 32 );

			$query = "SELECT `email` FROM `email_verify` WHERE `id` = ? LIMIT 1";
	
			$count = $db->query( $query, array( $this->token ) );
	
		} while( $count > 0 );

		$query = "INSERT INTO `email_verify` ( `expiration`, `id`, `email_code`, `email` ) VALUES ( '$tomorrow', ?, ?, ? );";

		$param = array( $this->token, $this->code, $email );

		$count = $db->query( $query, $param );

		unset( $db );

		return $count == 1;
	}

	/**
	 * HTML()
	 *
	 * @purpose
	 *  Email HTML framework for signup email
	 *
	 * @return string The HTML for the email message
	 */
	private function HTML()
	{
		$link = $GLOBALS['conf']->host . "verify_email.php?token=" . $this->token . "&code=" . $this->code;

		$html = "
<html>
<head>

<title>Welcome to " . $GLOBALS['conf']->site_title . "</title>
</head>

<body>

<h1>Welcome to " . $GLOBALS['conf']->site_title . "</h1>
<p>Greetings. Thank you for you interest in " . $GLOBALS['conf']->site_title . ". For account security and password recovery purposes,
we need to verify your email account.

Please click on the link below.
</p>

<h3><a href='$link'>Verify your Email Address</a></h3>

<p>
If the link above does not work, please copy and paste this URL into your browser.<br><br>

<h3>$link</h3>
</p>

</body>
</html>
";

		return $html;
	}
}
?>