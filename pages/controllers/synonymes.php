<?php

class Synonymes {
    private $typesynonymes = array();
    private $accesssynonymes = array();

    // These are synonymes to ResourceSpace types.
    // All the synonymes can then be used in the "type" column.
    public function __construct( ) {
        $ts = sql_query( "SELECT word, synonym FROM xlsimport_synonymes WHERE type='type'" );
        $as = sql_query( "SELECT word, synonym FROM xlsimport_synonymes WHERE type='access'" );

        if( count( $ts ) == 0 && count( $as ) == 0 )
            $this->setupDefaultSynonymes();

        foreach( $ts as $i => $s ) {
            $this->typesynonymes[ $s['synonym'] ] = $s['word'];
        }
        foreach( $as as $i => $s ) {
            $this->accesssynonymes[ $s['synonym'] ] = $s['word'];
        }
    }

    public function mapType( $synonym ) {
        if( array_key_exists( $synonym, $this->typesynonymes ) )
            return $this->typesynonymes[ $synonym ];
        else
            return $synonym;
    }

    public function mapAccess( $synonym ) {
        if( array_key_exists( $synonym, $this->accesssynonymes ) )
            return $this->accesssynonymes[ $synonym ];
        else
            return $synonym;
    }

    private function setupDefaultSynonymes() {

        $query = "INSERT INTO xlsimport_synonymes (type, word, synonym) VALUES ";

        include 'model/synonymes.default.php';

        $this->typesynonymes = $typesynonymes;
        $this->accesssynonymes = $accesssynonymes;

        $first = true;
        foreach( $typesynonymes as $s => $w ) {
            if( $first )
                $first = false;
            else
                $query .= ",";
            $query .= "('type', '$w', '$s')";
        }

        $first = true;
        foreach( $accesssynonymes as $s => $w ) {
            $query .= ",('access', '".mysql_real_escape_string($w)."', '".mysql_real_escape_string($s)."')";
        }

        sql_query( $query );

    }
}

?>
