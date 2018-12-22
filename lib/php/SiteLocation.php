<?php

namespace SystemCore;

class SiteLocation
{
    /**
     *
     * Constructor
     *
     */
    public function __construct( $location, $db = false )
    {
        $db = $db ? $db : new \db();

        $query = "UPDATE `" . $GLOBALS['conf']->db['main'] . "`.`member` SET `location` = '$location' WHERE `id` = ? LIMIT 1";

        $db->query( $query, array( $_SESSION['user']['id'] ) );

        unset( $db );
    }
}

?>