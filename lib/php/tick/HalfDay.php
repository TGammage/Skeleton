<?php
namespace Tick;

class HalfDay extends TickModel
{
    /** @var string Used for logging */
    public  $self   = __CLASS__;

    
    /**
     * clean_sessions()
     * 
     * @purpose
     *  Removes files leftover in the sessions folder.
     *  Removes session odl records on the db.
     */
    public function clean_sessions()
    {
        /**
         * Garbage Collect Session Directory
         */
        session_save_path( $GLOBALS['conf']->dir['session'] );

        session_start();

        session_gc();

        session_destroy();

        /**
         * Clean Out DB
         */
        $db = $GLOBALS['__mysql__'] ? $GLOBALS['__mysql__'] : new \db( 'member', 'system' );

        $cookie_life = $GLOBALS['conf']->cookie['life'] / ( 60 * 60 * 24 );

        $time = $GLOBALS['tick_time']->format( \Calendar::MYSQL_DATETIME );

        $query = "DELETE FROM `{$GLOBALS['conf']->db['tmp']}`.`session` WHERE `session_created` <= DATE_SUB( '$time', INTERVAL {$cookie_life} DAY )";

        $db->query( $query );
    }
}
?>