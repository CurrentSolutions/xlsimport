
<?php

function getUploadsMap( &$conflictsMap ) {
    global $mediaPath;
    $map = Array();

    $directoryIterator = new RecursiveDirectoryIterator( $mediaPath );
    foreach( new RecursiveIteratorIterator( $directoryIterator ) as $pathIterator )
    {
        if( !is_file( $pathIterator ) )
            continue;

        $file = pathinfo( $pathIterator )[ 'basename' ];
        $path = pathinfo( $pathIterator )[ 'dirname' ]."/".$file;

        if( array_key_exists( $file, $map ) ) {
            if( !array_key_exists( $file, $conflictsMap ) ) {
                $conflictsMap[ $file ] = Array( $map[ $file ] );
            }

            array_push( $conflictsMap[ $file ], $path );
        }

        $map[ $file ] = $path;
    }

    return $map;
}

?>