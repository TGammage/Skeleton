<?php

namespace SystemCore\Session;

class Clearance
{
    /**
     *
     * Constructor
     * 
     */
    public function __construct()
    {
        if( $_SESSION['sec_level'] < CLEARANCE )
        {
            header( "Location:main.php" );
        }
    }
}
?>