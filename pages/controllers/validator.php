
<?php


function checkForFiles( &$excel, $fileColumn, &$uploadsMap ) {
    global $mediaPath;
    for( $row = 2; $row <= $excel->numRows(); ++$row ) {
        $filename = $excel->valueAt( $row, $fileColumn );

        if( !array_key_exists( $filename, $uploadsMap ) ) {
            return false;
        }
    }

    return true;
}


?>
