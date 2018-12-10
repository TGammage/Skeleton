<?php

require 'start.php';

new AccountRecovery;

class AccountRecovery
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
			$system = new SystemCore\CheckAccountRecovery;
			$system->redirect();

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

		$PAGE->begin();

		self::HTML_contents();

		$PAGE->end();
    }

    /**
     * HTML_contents()
     *
     * @purpose
     *  This will dynamically changed based off a URL variable.
     *  By default it will load a form asking for the recovery email.
     *  Once the email has been supplied, it will build a page for entering code sent to email
     *
     * @return string HTML page content
     */
    private function HTML_contents()
    {
        $request = 'default';

        if( isset( $_GET['request'] ) )
        {
            $request = $_GET['request'];
        }

        switch( $request )
        {
            // Email Collection Page
            default :

                $_SESSION['url_key']['account_recovery'] = random::string( 32 );
                $_SESSION['var_key']['account_recovery'] = random::string( 32 );

                echo "
            <h2>Account Recovery</h2>
            <p>If you have forgotten your password and need help in recovering your account, enter your email address below.</p>";
            
                echo "
            <form method='POST' action='?step=email_submission&unique={$_SESSION['url_key']['account_recovery']}'>
                <input type='hidden' name='unique' value='{$_SESSION['var_key']['account_recovery']}' />
                <input type='text' name='email' placeholder='Email' autofocus /><br><br>
                <input type='submit' value='Recover'/>
            </form>";

            break;

            case 'email_unknown':

                $_SESSION['url_key']['account_recovery'] = random::string( 32 );
                $_SESSION['var_key']['account_recovery'] = random::string( 32 );

                echo "
            <h2>Email Not Recognized</h2>
            <p>The email supplied was not found in our database. Please verfiy the spelling is correct and resubmit the email address.</p>";
            
                echo "
            <form method='POST' action='?unique={$_SESSION['url_key']['account_recovery']}'>
                <input type='hidden' name='unique' value='{$_SESSION['var_key']['account_recovery']}' />
                <input type='text' name='email' placeholder='Email' autofocus /><br><br>
                <input type='submit' value='Recover'/>
            </form>";

            break;

            case 'code_entry':

                $_SESSION['url_key']['account_recovery'] = random::string( 32 );
                $_SESSION['var_key']['account_recovery'] = random::string( 32 );

                echo "
            <h2>Recovery Code</h2>
            <p>You should have received an email containing a code for recovery. Please enter this code below.</p>";
            
                echo "
            <form method='POST' action='?step=code_entry&unique={$_SESSION['url_key']['account_recovery']}'>
                <input type='hidden' name='unique' value='{$_SESSION['var_key']['account_recovery']}' />
                <input type='text' name='recovery_code' placeholder='Recovery Code' autofocus /><br><br>
                <input type='submit' value='Recover'/>
            </form>";

            break;

            case 'create_new_password':

                $_SESSION['url_key']['account_recovery'] = random::string( 32 );
                $_SESSION['var_key']['account_recovery'] = random::string( 32 );

                echo "
            <h2>Create New Password</h2>
            <p>Almost finished. Please create a new password for your account.</p>";
            
                echo "
            <form method='POST' action='?step=password_update&unique={$_SESSION['url_key']['account_recovery']}'>
                <input type='hidden' name='unique' value='{$_SESSION['var_key']['account_recovery']}' />
                <input type='hidden' name='email' value='{$_SESSION['var_key']['account_recovery']}' />
                <input type='password' name='access' placeholder='Password' autofocus /><br>
                <input type='password' name='confirm_access' placeholder='Confirm Password' /><br><br>
                <input type='submit' value='Create'/>
            </form>";

            break;
        }
    }
}

?>