<?php

namespace SystemCore;

class CheckProfileUpdate extends check
{
    /** @var array All possible incoming names for information */
    private $possible_updates   = array(
        'email', 'first_name', 'last_name',
        'access', 'confirm_access'
    );

    /** @var array Submitted information for updating */
    private $submitted_updates   = array();

    /**
     *
     * Constructor
     *
     */
    public function __construct()
    {
        debug_dump( $_POST );
        // Check keys
        parent::key_check( 'profile' );
        // Check incoming data
        self::info_check();
        var_dump( $this->submitted_updates );
        // Determine if supplied data needs changing
        // Check if email exists on the server
        // Update database
        // Send out update email if needed
        // Redirect back to profile
    }

    /**
     * info_check()
     *
     * @purpose
     *  Determines if information supplied is valid. If a value is omitted,
     *  we will skip updating this piece of information.
     *  Because an email address must be unique to the server, we will have to check
     *  the database for an existing address before updating
     *
     * @return void
     */
    private function info_check()
    {
        $db = new \db( 'member' );

        foreach( $this->possible_updates as $key )
        {
            if( isset( $_POST[ $key ] ) && strlen( $_POST[ $key ] ) > 0 )
            {
                $this->submitted_updates[ $key ] = $_POST[ $key ];

                switch( $key )
                {
                    case 'email' :

                        require_once( $GLOBALS['conf']->dir['php'] . '/is_email.php' );

                        // Check if we have a valid email
                        if( !is_email( $_POST[ $key ] ) )
                        {
                            parent::fail( 'Invalid Email Address' );
                            return;
                        }

                        // Check for email address on the database tied to a different account
                        $query = "SELECT `id` FROM `member` WHERE `email` = ? LIMIT 1";

                        $param = array( $_POST[ $key ] );

                        $id = $db->query( $query, $param, \PDO::FETCH_NUM, true );

                        if( count( $id ) > 0 )
                        {
                            if( $id[0] !== $_SESSION['user']['id'] )
                            {
                                parent::fail( 'Email address already exists on the server' );
                                return;
                            }

                            /**
                             * By here, we have found an existsing email on the database, and we know it belongs
                             * to the account submitting, therefore there is no need to update this email
                             */
                            unset( $this->submitted_updates['email'] );
                        }

                    break;

                    case 'first_name':

                        if( !preg_match( \regex::FIRST_NAME ) )
                        {
                            parent::fail( 'Bad Format First Name' );
                            return;
                        }

                    break;
                }
            }
        }

        
    }
}

?>