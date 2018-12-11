<?php

namespace SystemCore;

class CheckEmailVerification
{
	/** @var bool Failure trigger */
	protected $success = true;

	/** @var string Error Message sent back to login page when in DEBUG */
	protected $error_data = '';

	/** @var Database Access */
	private	 $db = null;

	/** @var Email Address */
	private	 $email = null;

    /**
     * 
     * Constructor
     * 
     */
    public function __construct()
    {
        self::vital_check();

        $this->db = new \db( 'member' );

            self::vital_confirm();

            self::update_dbs();

        $this->db = null;

        self::redirect();
    }

    /**
     * vital_check()
     *
     * @purpose
     *  Check for the unique values of $GET['token'], $_GET['user_name'], and $_GET['user_id']
     *
     * @return void
     */
    private function vital_check()
    {
        if( !isset( $_GET['token'] ) )
        {
            self::fail( 'Missing token variable' );
        }

        if( !isset( $_GET['code'] ) )
        {
            self::fail( 'Missing username' );
        }
    }

    /**
     * vital_confirm()
     *
     * @purpose
     *  Validate the unique values of $GET['token'], $_GET['user_name'], and $_GET['user_id']
     *
     * @return void
     */
    private function vital_confirm()
    {
        if( !$this->success )
            return;
        
        if( !preg_match( '/^[\d|\w]{32}$/', $_GET['token'] ) )
        {
            self::fail( 'Bad token format' );
        }

        if( !preg_match( '/^[\d|\w]{32}$/', $_GET['code'] ) )
        {
            self::fail( 'Bad code format' );
        }

        if( !$this->success )
        return;
       
        // Get info from tmp database for identity check
        $query = "SELECT `email`,`email_code`,`expiration`  FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`email_verify` WHERE `id` = ? LIMIT 1";

        $param = array( $_GET['token'] );

        $result = $this->db->query( $query, $param, \PDO::FETCH_ASSOC, true );

        // Member not found
        if( empty( $result ) )
        {
            self::fail( 'Member does not exist on the database' );
            return;
        }

        // Code expired, no database clean up yet
        $expire_time = \DateTime::createFromFormat( 'Y-m-d H:i:s', $result['expiration'] );

        if( $expire_time->getTimestamp() < $_SERVER['REQUEST_TIME'] )
        {
            self::fail( 'Email Verification Code expired' );
            return;
        }

        $query = "SELECT `email_verified` FROM `member` WHERE `email` = ? LIMIT 1";

        $verified = $this->db->query( $query, array( $result['email'] ), \PDO::FETCH_NUM, true );

        // Email already verified
        if( $verified[0] == 1 )
        {
            self::fail( 'Email Already Verified' );
            return;
        }

        // Code Mismatch
        if( $result['email_code'] !== $_GET['code'] )
        {
            self::fail( 'Token Mismatch' );
        }

        // Set Email Address
        $this->email = $result['email'];
    }

    /**
     * update_dbs()
     *
     * @purpose
     *  Update confirmation that email is verfied
     *
     * @return void
     */
    private function update_dbs()
    {
        if( !$this->success )
        return;
       
        $query = "UPDATE `member` SET `email_verified` = 1 WHERE `email` = ? LIMIT 1";

        $param = array( $this->email );

        $count = $this->db->query( $query, $param );

        if( $count == 0 )
        {
            self::fail( 'Update fail in the main database' );
            return;
        }
        
        $query = "DELETE FROM `" . $GLOBALS['conf']->db['tmp'] . "`.`email_verify` WHERE `email` = ?";

        $this->db->query( $query, array( $this->email ) );
   }

    /**
	 * fail()
	 *
	 * @purpose
	 *  Trigger failure for login
	 *
	 * @param string $message Error message
	 *
	 * @return void
	 */
	protected function fail( $message )
	{
		$this->success = false;

		self::error( $message );
	}

	/**
	 * error()
	 *
	 * @purpose
	 *  To build string for return query when in DEBUG
	 *
	 * @param string $message Error message
	 *
	 * @return void
	 */
	protected function error( $message )
	{
		if( strlen( $this->error_data ) > 1 )
		{
			$this->error_data .= "|$message";
		} else {
			$this->error_data = $message;
		}
	}

	/**
	 * redirect()
	 *
	 * @purpose
	 *  Redirection after evaluation
	 *
	 * @return void
	 */
	public function redirect()
	{
		if( $this->success )
		{
			header( "Location:" . $GLOBALS['conf']->host . "main.php?email_verfied=success" );
		} else {
			
			$url = $GLOBALS['conf']->host . "login.php";

			$query ="?email_verfied=failed";

			if( DEBUG )
			{
				$query = "&error=" . urlencode( $this->error_data );
			}

			header( "Location:$url$query" );
		}
	}
}
?>