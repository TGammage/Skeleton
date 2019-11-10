<?php

require '../start.php';

$db = new db( 'member', 'tick' );

$q = "
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
";

$link = mysqli_connect(
    $GLOBALS['conf']->db['host']['tick'],
    $GLOBALS['conf']->db['user']['tick'],
    $GLOBALS['conf']->db['access']['tick'],
    $GLOBALS['conf']->db['member']
);
$result = mysqli_query( $link, $q );
var_dump( $result );

$result = $db->query( $q );
var_dump( $result );

?>