<?php
require "start.php";

new Login;

class Login
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
			$system = new SystemCore\Login\CheckLogin;
			$system->redirect();

			return;
		}

		if( isset( $_GET['logout'] ) )
		{
			$system = new SystemCore\Login\CheckLogout;
			$system->redirect();

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

		if( $GLOBALS['session']->logged_in )
		{
			if( !isset( $_SESSION['url_key']['logout'] ) ){
				$_SESSION['url_key']['logout']	= random::string( 16 );
			}

			echo "
<h2>Welcome</h2><p>Currently Logged in as {$_SESSION['user']['name']}
<br><a href='" . $GLOBALS['conf']->host . "login.php?logout&unique={$_SESSION['url_key']['logout']}.php'>Logout</a></p>";

		} else {
			$_SESSION['url_key']['login']	= random::string( 16 );
			$_SESSION['var_key']['login']	= random::string( 16 );

			$placeholder = ucfirst( $GLOBALS['conf']->login['identify_by'] );

			$prefill = isset( $_GET['identify'] ) ? urldecode( $_GET['identify'] ) : '';

			if( strlen( $prefill ) > 0 )
			{
				$autofocus_identify = '';
				$autofocus_access = ' autofocus';
			} else {
				$autofocus_identify = ' autofocus';
				$autofocus_access = '';
			}

			echo "
<form method='POST' action='?unique={$_SESSION['url_key']['login']}'>
	<input type='hidden' name='unique' value='{$_SESSION['var_key']['login']}' />
	<input type='text' name='identify' placeholder='$placeholder' value='$prefill'$autofocus_identify /><br>
	<input type='password' name='access' placeholder='Password'$autofocus_access /><br>
	<input type='submit' value='Login'/>
</form>
<a href='account_recovery.php'>Forgot My Password</a><br>
<a href='signup.php'>Signup</a>";
		}

		$PAGE->end();
	}
}
?>