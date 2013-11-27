<?php

require_once( "model/excel.php" );

$excel = new Excel( "/var/www/resourcespace/uploads/DATABASE_FILES_UNISA_ARCHIVE.xls" );
echo "File has ".$excel->numRows()." rows and ".$excel->numCols()." columns\n";

for( $i = 1; $i < $excel->numRows(); ++$i ) {
    for( $j = 1; $j < $excel->numCols(); ++$j ) {
        echo $excel->valueAt( $i, $j )." ";
    }
    echo "\n";
}

?>
