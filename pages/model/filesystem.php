
<?php

$mediaPath = '/var/www/resourcespace/uploads/';

function getUploadsMap( &$conflictsMap ) {
    global $mediaPath;
    $map = Array();

    try {
        $directoryIterator = new RecursiveDirectoryIterator( $mediaPath );
        foreach( new RecursiveIteratorIterator( $directoryIterator ) as $pathIterator )
        {
            if( !is_file( $pathIterator ) )
                continue;

            $pi = pathinfo( $pathIterator );
            $file = $pi[ 'basename' ];
            $path = $pi[ 'dirname' ]."/".$file;

            // skip hidden files
            if( $file[0] == "." )
                continue;

            if( array_key_exists( $file, $map ) ) {
                if( !array_key_exists( $file, $conflictsMap ) ) {
                    $conflictsMap[ $file ] = Array( $map[ $file ] );
                }

                array_push( $conflictsMap[ $file ], $path );
            }

            $map[ $file ] = $path;
        }
    }
    catch( Exception $e ) {
        print( $e->getMessage() );
    }

    return $map;
}

?>
