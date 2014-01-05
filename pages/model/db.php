<?php

function escapeString( $s ) {
    global $db;
    global $use_mysqli;

    if( $use_mysqli ) {
        return mysqli_real_escape_string( $db, $s );
    }
    else {
        return mysql_real_escape_string( $s, $db );
    }
}


?>
