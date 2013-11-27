<?php

class Mapper {
    
    private $colToName = array();
    private $nameToId = array();

    private $nameToCol = array();
    private $idToName = array();

    private $typeMap = array();

    private $synonymes;

    public function __construct( $excel, $firstMapping ) {
        $this->synonymes = new Synonymes();

        $this->getColumnMapping( $excel, $firstMapping );
        $this->getResourceSpaceNameMap();

        $this->getTypeMapping();
    }

    public static function getFields() {
        $resourcefields = array();
        // todo: language

        $resourcefields[ 'unused' ] = 'Nicht zuweisen';
        $resourcefields[ 'filename' ] = 'Dateiname';
        $resourcefields[ 'type' ] = 'Resource type';
        $resourcefields[ 'category' ] = 'Kategorie';
        $resourcefields[ 'collection' ] = 'Kollektion';
        $resourcefields[ 'access' ] = 'Zugriffsrechte';

        $fields = sql_query( "select ref from resource_type_field" );

        for( $i = 0; $i < count( $fields ); ++$i ) {
            $field = get_field( $fields[$i]["ref"] );
            $resourcefields[ $field["name"] ] = $field["title"];
        }


        return $resourcefields;
    }

    public function getResourceType( $typename ) {
        $typename = strtolower( $typename );
        $name = null;

        $name = $this->synonymes->mapType( $typename );

        if( array_key_exists( $name, $this->typeMap ) )
            return $this->typeMap[ $name ];
        else
            return null;
    }

    public function getAccessType( $accessname ) {
        $accessname = strtolower( $accessname );

        return $this->synonymes->mapAccess( $accessname );
    }


    public function getIdByName( $name ) {
        if( !array_key_exists( $name, $this->nameToId ) ) {
            return null;
        }
        else {
            return $this->nameToId[ $name ];
        }
    }

    public function getNameByCol( $col ) {
        if( !array_key_exists( $col, $this->colToName ) )
            return null;

        return $this->colToName[ $col ];
    }

    public function getIdByCol( $col ) {
        if( !array_key_exists( $col, $this->colToName ) )
            return null;

        $name = $this->getNameByCol( $col );
        return $this->getIdByName( $name );
    }

    public function getColByName( $name ) {
        if( ! array_key_exists( $name, $this->nameToCol ) )
            return null;

        return $this->nameToCol[ $name ];
    }

    private function getColumnMapping( $excel, $firstMapping ) {
        for( $col = 1; $col <= $excel->numCols(); ++$col ) {
            if( $firstMapping[ $col ] == 'unused' )
                continue;

            $this->colToName[ $col ] = $firstMapping[ $col ];
            $this->nameToCol[ $this->colToName[ $col ] ] = $col;
        }
    }

    private function getResourceSpaceNameMap() {
        $map = sql_query( "select name, ref from resource_type_field" );

        for( $i = 0; $i < count( $map ); ++$i ) {
            $this->nameToId[ $map[$i]["name"] ] = $map[$i]["ref"];
            $this->idToName[ $map[$i]["ref"] ] = $map[$i]["name"];
        }
    }

    private function getTypeMapping() {
        $map = sql_query( "select name, ref from resource_type" );

        for( $i = 0; $i < count( $map ); ++$i ) {
            $this->typeMap[ strtolower( $map[$i]["name"] ) ] = $map[$i]["ref"];
        }
    }
}

?>
