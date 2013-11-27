
<?php

class Resource {
    private $type;
    private $category;
    private $filename;
    private $collection;
    private $access;
    private $fields = array();
    private $keyfield;

    public function __construct( $filename, $collection, $category, $type, $access, $fields, $keyfield ) {
        $this->type = $type;
        $this->category = $category;
        $this->filename = $filename;
        $this->collection = $collection;
        $this->access = $access;
        $this->fields = $fields;
        $this->keyfield = $keyfield;
    }

    function createXMLNode( &$xml ) {
        $r = $xml->createElement( "resource" );
        $atype = $xml->createAttribute( "type" );
        $atype->value = $this->type;
        $r->appendChild( $atype );

        // todo: check for not null to not insert empty "fields"?

        // add category, filename and collection
        $ncol = $xml->createElement( "collection", $this->collection );
        $nfil = $xml->createElement( "filename", $this->filename );
        $ncat = $xml->createElement( "category", $this->category );
        $nacc = $xml->createElement( "access", $this->access );
        $typename = $xml->createAttribute( "typename" );
        $typename->value = "Kategorie";
        $ncat->appendChild( $typename );

        $r->appendChild( $ncol );
        $r->appendChild( $ncat );
        $r->appendChild( $nfil );
        $r->appendChild( $nacc );

        // add all fields
        foreach( $this->fields as $id => $value ) {
            if( $id == $this->keyfield )
                $field = $xml->createElement( "keyfield", $value );
            else
                $field = $xml->createElement( "field", $value );
            $ref = $xml->createAttribute( "ref" );
            $ref->value = $id;
            $field->appendChild( $ref );
            $r->appendChild( $field );
        }

        return $r;
    }
}

?>
