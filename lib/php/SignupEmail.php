<?php

namespace SystemCore;

class SignupEmail extends email
{
	/** @var int Member ID number */
	private	$user_ID = 0;

	/** @var string Verification code for email */
	private	$verification_code = null;

	/**
	*
	*		Constructor
	*
	*/
	public function __construct( $user_ID, $verification_code )
	{
		$this->user_ID				= $user_ID;
		$this->verification_code	= $verification_code;

		parent::to( $_POST['email'], $_POST['username'] );

		parent::from( "signup@" . $GLOBALS['conf']->base_domain, "System at " . $GLOBALS['conf']->site_title );

		parent::subject( "Welcome " . $_POST['username'] );

		parent::message( self::HTML( $verification_code ), parent::MIME_HTML );

		parent::send();
	}

	/**
	*		HTML()
	*
	*		@purpose
	*			Email HTML framework for signup email
	*
	*		@param string $verification_code	Random string of 32 characters for verifying
	*
	*		@return string The HTML for the email message
	*/
	private function HTML()
	{
		$link = $GLOBALS['conf']->host . "verify_email.php?user_id=" . $this->user_ID . "&user_name=" . $_POST['username'] . "&token=" . $this->verification_code;

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