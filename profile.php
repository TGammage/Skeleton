<?php

/**
 * Page Specific Definitions
 */
define( 'LOCATION', 'Profile Information' );
define( 'LOGGEDINONLY', true );

require "start.php";

new Profile;

class Profile
{
	/**
	 *
	 * Constructor
	 *
	 */
	public function __construct()
	{
		if( $_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			new SystemCore\Profile\CheckUpdate;

			return;
		}

		self::HTML();
	}

	/**
	 * HTML()
	 *
	 * @purpose
	 *  Gather's the HTML and loads the page
	 *
	 * @return void
	 */
	protected function HTML()
	{
		$PAGE = new HTML\Frame();

        $_SESSION['url_key']['profile']	= random::string( 16 );
        $_SESSION['var_key']['profile']	= random::string( 16 );

        // Get info
        $db = new \db( 'member' );

        $query = "SELECT `email`,`first_name`,`last_name` FROM `member` WHERE `id` = {$_SESSION['user']['id']} LIMIT 1";

		$member_info = $db->query( $query, null, \PDO::FETCH_ASSOC, true );

		if( isset( $_GET['response'] ) )
		{
			$response	= \SystemCore\Profile\UpdateError::message( $_GET['response'] );
			$message	= "<quote>Server Response : $response</quote>";
		} else {
			$message = '';
		}

        $content = "
<h3>Profile Information For : {$_SESSION['user']['name']}</h3>
$message
<form method='POST' action='?unique={$_SESSION['url_key']['profile']}'>
    <input type='hidden' name='unique' value='{$_SESSION['var_key']['profile']}' />
    <label>Email Address : </label><input type='text' name='email' value='{$member_info['email']}' /><hr>
	<label>First Name : </label><input type='text' name='first_name' value='{$member_info['first_name']}' />
	<label>Omit</label><input type='checkbox' name='omit_first_name' value='1' /><br>
	<label>Last Name : </label><input type='text' name='last_name' value='{$member_info['last_name']}' />
	<label>Omit</label><input type='checkbox' name='omit_last_name' value='1' /><hr>
    <label>Password : </label><input type='password' name='access' value='' /><br>
    <label>Confirm Password : </label><input type='password' name='confirm_access' value='' /><br><br>
    <input type='submit' value='Update Profile' />
</form>
<a href='main.php'>Main Page</a>";

		$PAGE->addContent( $content );

		$PAGE->output();
	}
}
?>