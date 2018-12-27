<?php
require "start.php";

new Signup;

class Signup
{
	/**
	*
	*
	*		Constructor
	*
	*/
	public function __construct()
	{
		if( $_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			new SystemCore\Signup\CheckSignup;

			return;
		}

		self::HTML();
	}

	/**
	*		HTML()
	*
	*		@purpose
	*			Gather's the HTML and loads the page
	*
	*		@return void
	*/
	protected function HTML()
	{
		$PAGE = new HTML\Frame();

		$PAGE->begin();

		$_SESSION['url_key']['signup']	= random::string( 16 );
		$_SESSION['var_key']['signup']	= random::string( 16 );

		$placeholder = ucfirst( $GLOBALS['conf']->login['identify_by'] );

		echo "
<form method='POST' action='?unique={$_SESSION['url_key']['signup']}'>
	<input type='hidden' name='unique' value='{$_SESSION['var_key']['signup']}' />
	<input type='text' name='username' placeholder='Username' value='' autofocus /><br>
	<input type='text' name='email' placeholder='email' value=''/><hr>
	<input type='text' name='first_name' placeholder='First Name' value=''/><br>
	<input type='text' name='last_name' placeholder='Last Name' value=''/><hr>
	<input type='password' name='access' placeholder='Password' value='' /><br>
	<input type='password' name='confirm_access' placeholder='Confirm Password' value='' /><br>
	<input type='submit' value='Signup'/>
</form>";

		$PAGE->end();
	}
}
?>