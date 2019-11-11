<?php

require '../start.php';

$db = new db( 'member', 'tick' );

$q = "
    SELECT GROUP_CONCAT(CONCAT('`', COLUMN_NAME, '`'))
        FROM INFORMATION_SCHEMA.COLUMNS WHERE
        TABLE_SCHEMA = 'skeleton_dev_member'
        AND TABLE_NAME = 'member'
        AND COLUMN_NAME NOT IN ('backup')
        AND COLUMN_NAME NOT IN ('last_updated')
";

// $link = mysqli_connect(
//     $GLOBALS['conf']->db['host']['tick'],
//     $GLOBALS['conf']->db['user']['tick'],
//     $GLOBALS['conf']->db['access']['tick'],
//     $GLOBALS['conf']->db['member']
// );
// $result = mysqli_query( $link, $q );
// $array = mysqli_fetch_array( $result );
// var_dump( $array[0] );

$result = $db->query( $q, null, PDO::FETCH_NUM, true );
var_dump( $result );

?>