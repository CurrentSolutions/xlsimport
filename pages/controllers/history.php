<?php
class History {
    public static function save( $excel, $keyfield, $template, $mapping, $updateOnly ) {
        $serializedMap = "1=".$mapping[1];
        for( $col = 2; $col <= $excel->numCols(); $col++ ) {
            $serializedMap .= "&".$col."=".$mapping[$col];
        }

        sql_query( "INSERT INTO xlsimport_history
            SET
            date=NOW(),
            filename='".escapeString( basename( $excel->getSource() ) )."',
            keyfield='".escapeString( $keyfield )."',
            template='".escapeString( $template )."',
            mapping='".escapeString( $serializedMap )."',
            updateOnly='".escapeString( $updateOnly )."'" );
    }


    public static function getAllMappings( $filename ) {
        $mapping = sql_query( "SELECT * FROM xlsimport_history
                                WHERE filename='".escapeString( $filename )."'
                                ORDER BY id DESC");

        for( $i = 0; $i < count( $mapping ); $col++ ) {
            parse_str( $mapping[$i][ 'mapping' ], $mapping[$i][ 'map' ] );
        }

        return $mapping;
    }


    public static function getLastMapping( $filename ) {
        $mapping = sql_query( "SELECT * FROM xlsimport_history
                                WHERE filename='".escapeString( $filename )."'
                                ORDER BY id DESC
                                LIMIT 1");

        if( count( $mapping ) == 0 )
            return null;#
        $mapping = $mapping[0];
        parse_str( $mapping[ 'mapping' ], $mapping[ 'map' ] );

        return $mapping;
    }
}
?>
