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

    /**
     * backup_member()
     * 
     * @purpose
     *  Back up the member db changes
     */
    protected function backup_member()
    {
        $db = $GLOBALS['__mysql__'] ? $GLOBALS['__mysql__'] : new \db( 'member', 'system' );

        // Search through member db which columns to update
        $query = "INSERT INTO `{$GLOBALS['conf']->db['backup']}`.`member_member`
            ( SELECT * FROM )
        
            ON DUPLICATE KEY UPDATE ";
    }
}
/**
SET @sql = CONCAT(
    'SELECT ',
    (
        SELECT
        	REPLACE(GROUP_CONCAT(COLUMN_NAME), 'backup,', '')
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'member' AND TABLE_SCHEMA = 'skeleton_dev_member'
    ),
    ' FROM `skeleton_dev_member`.`member`'
);

PREPARE stmt1 FROM @sql;
EXECUTE stmt1;
 */
?>