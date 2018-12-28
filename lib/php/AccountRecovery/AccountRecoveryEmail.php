<?php

namespace SystemCore\AccountRecovery;

class AccountRecoveryEmail extends \SystemCore\email
{
	/** @var string Unique identifier used to determining which account we are recovering */
	public	$token = false;

	/** @var string Verification code for email */
	public	$code = null;

	/** @var bool Determination of success */
	public	$success = true;

	/**
	 * Constructor
	 *
	 * @purpose
	 *   Organizes tmp database for email verification, then sends signup email
	 *
	 * @param	string $email		Email address for new signup
	 * @param	string $username	Desired username for the new signup
	 *
	 * @return void
	 */
	public function __construct( $email, $username )
	{
        if( !self::db_update( $email ) )
        {
            $this->success = false;
			return;
        }

		parent::to( $_POST['email'], $username );

		parent::from( "signup@" . $GLOBALS['conf']->base_domain, "System at " . $GLOBALS['conf']->site_title );

		parent::subject( "Account Recovery at " . $GLOBALS['conf']->site_title );

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
		$this->code	= \random::string( 8 );
		$tomorrow	= date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + ( 60 * 60 * 24 ) );

		$db =  new \db( 'tmp' );

		$query = "DELETE FROM `account_recovery` WHERE `email` = ?";

		$db->query( $query, array( $email ) );

		do
		{
			$this->token = \random::string( 32 );

			$query = "SELECT `email` FROM `account_recovery` WHERE `id` = ? LIMIT 1";
	
			$count = $db->query( $query, array( $this->token ) );
	
		} while( $count > 0 );

		$query = "INSERT INTO `account_recovery` ( `expiration`, `id`, `email_code`, `email` ) VALUES ( '$tomorrow', ?, ?, ? );";

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
		$link = $GLOBALS['conf']->host . "account_recovery.php?request=code_entry&token=" . $this->token;

		$html = "
<html>
<head>

<title>Account Recovery at " . $GLOBALS['conf']->site_title . "</title>
</head>

<body>

<h1>Account Recovery</h1>
<p>We have received your request to recover your account with us. If you have received this email without your consent,
please contact us.

Please click the link below or enter the url into your browser.
Copy and paste the code where prompted to continue the recovery process.
</p>

<h2>" . $this->code . "</h2>

<h3><a href='$link'>Continue Account Recovery</a></h3>

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