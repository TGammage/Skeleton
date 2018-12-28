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

		$inputs = array(
			'username', 'email',
			'first_name', 'last_name',
			'access', 'confirm_access'
		);

		$preset = array();

		foreach( $inputs as  $key )
		{
			$preset[$key] = isset( $_GET[ $key ] ) ? urldecode( $_GET[ $key ] ) : '';
		}

		echo "
<form method='POST' action='?unique={$_SESSION['url_key']['signup']}'>
	<input type='hidden' name='unique' value='{$_SESSION['var_key']['signup']}' />
	<input type='text' name='username' placeholder='Username' value='{$preset['username']}' autofocus /><br>
	<input type='text' name='email' placeholder='email' value='{$preset['email']}'/><hr>
	<input type='text' name='first_name' placeholder='First Name' value='{$preset['first_name']}'/><br>
	<input type='text' name='last_name' placeholder='Last Name' value='{$preset['last_name']}'/><hr>
	<input type='password' name='access' placeholder='Password' /><br>
	<input type='password' name='confirm_access' placeholder='Confirm Password' /><br>
	<input type='submit' value='Signup'/>
</form>";

		$PAGE->end();
	}
}
?>