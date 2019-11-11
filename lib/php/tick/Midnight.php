<?php
namespace Tick;

class Midnight extends TickModel
{
    /** @var string Used for logging */
    public  $self   = __CLASS__;

    /**
     * cleanup_bans()
     * 
     * @purpose
     *  To remove the expired bans from DB
     */
    protected function cleanup_bans()
    {
        $db = $GLOBALS['__mysql__'] ? $GLOBALS['__mysql__'] : new \db( 'member', 'system' );

        $lift = date( \Calendar::MYSQL_DATETIME, $GLOBALS['tick_time']->getTimestamp() - 1 );

        // IP Bans
        $query = "DELETE FROM `{$GLOBALS['conf']->db['tmp']}`.`ip_banned` WHERE `finish_time` <= '$lift'";
        
        $db->query( $query );

        // Account Bans
        $query = "DELETE FROM `{$GLOBALS['conf']->db['tmp']}`.`account_banned` WHERE `finish_time` <= '$lift'";

        $db->query( $query );
    }

    /**
     * cleanup_emails()
     *
     * @purpose
     *  To remove expired email verifications and account recoveries from DB
     */
    protected function cleanup_emails()
    {
        $db = $GLOBALS['__mysql__'] ? $GLOBALS['__mysql__'] : new \db( 'member', 'system' );

        $lift = date( \Calendar::MYSQL_DATETIME, $GLOBALS['tick_time']->getTimestamp() - 1 );

        // Account Recovery Emails
        $query = "DELETE FROM `{$GLOBALS['conf']->db['tmp']}`.`account_recovery` WHERE `expiration` <= '$lift'";
    
        $db->query( $query );

        // Verification Emails
        $query = "DELETE FROM `{$GLOBALS['conf']->db['tmp']}`.`email_verify` WHERE `expiration` <= '$lift'";
    
        $db->query( $query );
    }
}
?>