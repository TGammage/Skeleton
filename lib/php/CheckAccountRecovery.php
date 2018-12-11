<?php

namespace SystemCore;

class CheckAccountRecovery extends check
{
    /**
     *
     * Constructor
     *
     */
    public function __construct()
    {
        if( !isset( $_GET['step'] ) )
        {
            header( "Location:/account_recovery.php" );
        }

        parent::key_check( 'account_recovery' );

        if( !$this->success )
        {
            header( "Location:/account_recovery.php" );
        }
        
        switch( $_GET['step'] )
        {
            default :
                header( "Location:/account_recovery.php" );
            break;

            case 'email_submission':
                self::email_submission_check();
            break;

            case 'code_entry':
                self::code_entry_check();
            break;

            case 'password_update':
                self::update_password();
            break;
        }
    }

    /**
     * email_submission_check()
     *
     * @purpose
     *  Will check email format to determine if an actual email.
     *  Afterwards will check the database to confirm we have a member with this email.
     *  Last we will confirm that this email has been verified.
     *
     * @return void
     */
    private function email_submission_check()
    {
        // Check incoming email address format
        require $GLOBALS['conf']->dir['php'] . "/is_email.php";

		if( !is_email( $_POST['email'] ) )
		{
            header( "Location:/account_recovery.php?request=email_unknown" );
        }

        // Email search on database
        $db = new \db( 'member' );

        $query = "SELECT `email_verified`,`username` FROM `member` WHERE `email` = ? LIMIT 1";

        $param = array( $_POST['email'] );

        $result = $db->query( $query, $param, \PDO::FETCH_ASSOC, true );

        if( empty( $result ) )
        {
            header( "Location:/account_recovery.php?request=email_unknown" );
        }
        elseif( $result['email_verified'] == 0 )
        {
            // Email has not been verified, send new email verification code
            new SignupEmail( $_POST['email'], $result['username'] );
        } else {
            // Send Account Recovery Email
            $email = new AccountRecoveryEmail( $_POST['email'], $result['username'] );

            if( !$email->success )
            {
                header( "Location:/account_recovery.php" );
            }

            header( "Location:/account_recovery.php?request=code_entry&token=" . $email->token );
        }
    }

    /**
     * code_entry_check()
     *
     * @purpose
     *  Checks the incoming code supplied by the client and validates it with on hand code in database
     *
     * @return void
     */
    private function code_entry_check()
    {
        if( !isset( $_POST['token'] ) || !isset( $_POST['recovery_code'] ) )
        {
            header( "Location:account_recovery.php" );
        }

        if(
            !preg_match( '/^[A-Za-z0-9]{8}$/', $_POST['recovery_code'] )
        ||  !preg_match( '/^[A-Za-z0-9]{32}$/', $_POST['token'] )
        )
        {
            header( "Location:account_recovery.php" );
        }

        $db = new \db( 'tmp' );

        $query = "SELECT `email`, `email_code`, `expiration` FROM `account_recovery` WHERE `id` = ? LIMIT 1";

        $result = $db->query( $query, array( $_POST['token'] ), \PDO::FETCH_ASSOC, true );

        if( empty( $result ) )
        {
            header( "Location:account_recovery.php" );
        }

        // Determine if code expired
        $expiration = date_create_from_format( "Y-m-d H:i:s", $result['expiration'] );
 
        if( $_SERVER['REQUEST_TIME'] > $expiration->getTimestamp() )
        {
            header( "Location:account_recovery.php?error=code+expired" );
        }

        // Code match
        if( $_POST['recovery_code'] !== $result['email_code'] )
        {
            header( "Location:account_recovery.php?request=code_entry&token={$_POST['token']}" );
        }

        // Move on to new password entry
        $_SESSION['url_key']['password_recovery'] = \random::string( 16 );

        header( "Location:account_recovery.php?request=create_new_password&token={$_POST['token']}&unique={$_SESSION['url_key']['password_recovery']}" );
    }

    /**
     * update_password()
     *
     * @purpose
     *  Update new password to database.
     *  Remove account recovery information in tmp database.
     *
     * @return void
     */
    private function update_password()
    {
        // print __class__ . "::" . __function__ . "()<br>";exit();
        // Empty checks
        if( 
            !isset( $_POST['token'] )
        ||  !isset( $_GET['unique'] )
        ||  !isset( $_POST['unique'] )
        ||  !isset( $_POST['access'] )
        ||  !isset( $_POST['confirm_access'] )
        ||  !isset( $_SESSION['url_key']['account_recovery'] )
        ||  !isset( $_SESSION['var_key']['account_recovery'] )
        )
       {
            header( "Location:account_recovery.php" );
        }

        // Key Check
        if(
            $_SESSION['url_key']['account_recovery'] !== $_GET['unique']
        ||  $_SESSION['var_key']['account_recovery'] !== $_POST['unique']
        ||  !preg_match( '/^[A-Za-z0-9]{32}$/', $_POST['token'] )
        )
        {
            $_SESSION['url_key']['password_recovery'] = \random::string( 16 );
            header( "Location:account_recovery.php" );
        }

        // Password match check
        if( $_POST['access'] !== $_POST['confirm_access'] )
        {
            $_SESSION['url_key']['password_recovery'] = \random::string( 16 );
            header( "Location:account_recovery.php?error=password+mismatch&request=create_new_password&token={$_POST['token']}&unique={$_SESSION['url_key']['password_recovery']}" );
        }

        // Password format check
        if( !preg_match( $GLOBALS['conf']->regex['password'], $_POST['access'] ) )
        {
            $_SESSION['url_key']['password_recovery'] = \random::string( 16 );
            header( "Location:account_recovery.php?error=password+bad+format&request=create_new_password&token={$_POST['token']}&unique={$_SESSION['url_key']['password_recovery']}" );
        }

        $db = new \db( 'tmp' );

        $query = "SELECT `email`, `expiration` FROM `account_recovery` WHERE `id` = ? LIMIT 1";

        $result = $db->query( $query, array( $_POST['token'] ), \PDO::FETCH_ASSOC, true );

        if( empty( $result ) )
        {
            header( "Location:account_recovery.php" );
        }

        // Determine if code expired
        $expiration = date_create_from_format( "Y-m-d H:i:s", $result['expiration'] );
 
        if( $_SERVER['REQUEST_TIME'] > $expiration->getTimestamp() )
        {
            header( "Location:account_recovery.php?error=code+expired" );
        }

        // Update new password in the member database
		$hasher = new PepperedPassword;

		$password = $hasher->hash( $_POST['access'] );

		// Add new member to the member database
		$query = "UPDATE `" . $GLOBALS['conf']->db['member'] . "`.`member` SET `access` = ? WHERE `email` = ? LIMIT 1 ";

		$param = array( $password, $result['email'] );

		$count = $db->query( $query, $param );

		if( $count != 1 )
		{
            header( "Location:account_recovery?error=password+update" );
        }

        $db->query( "DELETE FROM `account_recovery` WHERE `email` = ?", array( $result['email'] ) );

        header( "Location:login.php?message=account_recovery_success" );
    }
}

?>