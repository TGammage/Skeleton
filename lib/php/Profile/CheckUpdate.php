<?php

namespace SystemCore\Profile;

class CheckUpdate extends \SystemCore\check
{
    /** @var array All possible incoming names for information */
    private $possible_updates = array(
        'email', 'first_name', 'last_name', 'access',
        'omit_first_name', 'omit_last_name'
    );

    /** @var array Submitted information for updating */
    private $submitted_updates = array();

    /** @var bool New email verification required */
    private $new_email = false;

    /** @var Database connection */
    private $db = null;

    /**
     *
     * Constructor
     *
     */
    public function __construct()
    {
        // debug_dump( $_POST );
        // print( preg_replace( "/,/", '<br>', implode( ',', $_POST ) ) );
        // Check keys
        parent::key_check( 'profile' );

        if( $this->success )
        {
            $this->db = new \db( 'member' );

            // Check incoming data
            self::info_check();

            // Update database
            self::update_database();
        }

        // Redirect back to profile
        self::redirect();
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
        // Run through possible income
        foreach( $this->possible_updates as $key )
        {
            // Only focus on information that is submitted
            if( isset( $_POST[ $key ] ) && strlen( $_POST[ $key ] ) > 0 )
            {
                // Add to list of focused submissions
                $this->submitted_updates[ $key ] = $_POST[ $key ];

                // Check incoming submission
                switch( $key )
                {
                    case 'email' :

                        require_once( $GLOBALS['conf']->dir['php'] . '/is_email.php' );

                        // Check if we have a valid email
                        if( !is_email( $_POST[ $key ] ) )
                        {
                            parent::fail( UpdateError::INVALID_EMAIL );
                            return;
                        }

                        // Check if submitted email is same as email for this user in member database
                        if( $_SESSION['user']['email'] === $_POST[ $key ] )
                        {
                            unset( $this->submitted_updates[ $key ] );
                            break;
                        }

                        // Check for email address on the database tied to a different account
                        $query = "SELECT * FROM `member` WHERE `email` = ? LIMIT 1";

                        $param = array( $_POST[ $key ] );

                        $count = $this->db->query( $query, $param );

                        if( $count > 0 )
                        {
                            parent::fail( UpdateError::DUPLICATE_EMAIL );
                            return;
                        }

                        // Trigger new email verification
                        $this->new_email = true;

                    break;

                    case 'first_name':

                        if( !preg_match( \regex::FIRST_NAME, $_POST[ $key ] ) )
                        {
                            parent::fail( UpdateError::FORMAT_FIRST_NAME );
                            return;
                        }

                        // Check if submitted first name is same as first name for this user in member database
                        if( $_SESSION['user']['first_name'] === $_POST[ $key ] )
                        {
                            unset( $this->submitted_updates[ $key ] );
                        }
 
                    break;

                    case 'last_name':

                        if( !preg_match( \regex::LAST_NAME, $_POST[ $key ] ) )
                        {
                            parent::fail( UpdateError::FORMAT_LAST_NAME );
                            return;
                        }

                        // Check if submitted first name is same as first name for this user in member database
                        if( $_SESSION['user']['last_name'] === $_POST[ $key ] )
                        {
                            unset( $this->submitted_updates[ $key ] );
                        }
 
                    break;

                    case 'access':

                        if( !isset( $_POST['confirm_access'] ) )
                        {
                            parent::fail( 'Missing Password Confirmation' );
                            return;
                        }

                        if( !preg_match( \regex::PASSWORD, $_POST[ $key ] ) )
                        {
                            parent::fail( UpdateError::FORMAT_PASSWORD );
                            return;
                        }

                        if( $_POST[ $key ] !== $_POST['confirm_access'] )
                        {
                            parent::fail( UpdateError::CONFIRM_PASSWORD );
                            return;
                        }

                        // Check if submitted password is same as password for this user in member database
                        $result = $this->db->query(
                            "SELECT `access` FROM `member` WHERE `id` = {$_SESSION['user']['id']} LIMIT 1",
                            null,
                            \PDO::FETCH_ASSOC,
                            true
                        );

                        $hasher = new \SystemCore\PepperedPassword;

                        if( $hasher->verify( $_POST[ $key ], $result['access'] ) )
                        {
                            unset( $this->submitted_updates[ $key ] );
                        } else {
                            $this->submitted_updates[ $key ] = $hasher->hash( $_POST[ $key ] );
                        }
                 
                    break;

                    case 'omit_first_name' :

                        $this->submitted_updates['first_name'] = null;

                        unset( $this->submitted_updates[ $key ] );

                    break;

                    case 'omit_last_name' :

                        $this->submitted_updates['last_name'] = null;

                        unset( $this->submitted_updates[ $key ] );

                    break;
                }
            }
        } 
    }

    /**
     * update_database()
     *
     * @purpose
     *  Update member database based off information we discovered to be different from self::info_check()
     *  We can find what needs to be updated in $this->submitted_updates
     *
     * @return void
     */
    private function update_database()
    {
        if( !$this->success )
            return;

        // Skip if nothing needs updating
        if( count( $this->submitted_updates ) == 0 )
            return;

            // New timestamp for `last_updated`. This triggers session to update member information about self
        $timestamp = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );

        $query  = "UPDATE `member` SET `last_updated` = '$timestamp'";
        $param  = array();

        foreach( $this->submitted_updates as $col => $value )
        {
            $query .= ", `$col` = :$col";

            $param[ $col ] = $value;
        }

        if( $this->new_email )
        {
            $query .= ", `email_verified` = 0";
        }

        $query .= " WHERE `id` = {$_SESSION['user']['id']} LIMIT 1";

        $count = $this->db->query( $query, $param );

        if( $count == 0 )
        {
            parent::fail( UpdateError::DATABASE_UPDATE );
            return;
        }

        if( $this->new_email )
        {
            new UpdateEmailVerification( $_POST['email'], $_SESSION['user']['name'] );
        }
    }

    /**
     * redirect()
     *
     * @purpose
     *  Headers out to profile.php with error code
     *
     * @return void
     */
    private function redirect()
    {
        if( $this->error_data == '' )
        {
            $this->error_data = UpdateError::NO_ERROR;
        }

        header( "Location:profile.php?response=" . $this->error_data );
    }
}

?>