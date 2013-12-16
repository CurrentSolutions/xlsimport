<?php

include "../../../include/image_processing.php";
include "../../../include/collections_functions.php";

class Resource {
    private $type;
    private $filename;
    private $collection;
    private $access;
    private $fields = array();
    private $keyfield;

    public function __construct( $filename, $collection, $type, $access, $fields, $keyfield ) {
        $this->type = $type;
        $this->filename = $filename;
        $this->collection = $collection;
        $this->access = $access;
        $this->fields = $fields;
        $this->keyfield = $keyfield;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function resourceId() {
        $ref = sql_value( "
            select resource value
            from resource_data
            where resource_type_field='".mysql_real_escape_string($this->keyfield)."' and value='" . mysql_real_escape_string($this->fields[$this->keyfield])."'", 0 );
        return $ref;
    }

    public function updateResource() {
        $rid = $this->resourceId();

        // resource does not exist - create it
        if( $rid == 0 ) {
            $rid = create_resource( $this->type );
        }
        else {
            update_resource_type( $rid, $this->type );
        }

        foreach( $this->fields as $k => $v ) {
            update_field( $rid, $k, $v );
        }

		if( file_exists( $this->filename ) ) {
			$extension = explode( ".", $this->filename );
			if( count( $extension ) > 1 )
				$extension = trim( strtolower( $extension[count( $extension ) - 1] ) );
			else
				$extension = "";
			
			$path = get_resource_path( $rid, true, "", true, $extension );
			copy( $this->filename, $path );
            create_previews( $rid, false, $extension );
			
			# add file extension
			sql_query( "update resource set file_extension='".mysql_real_escape_string($extension)."' where ref='".mysql_real_escape_string($rid)."'" );
		}

        # add resource to collection (if the collection exists)
        if( $this->collection != null ) {
            $col_ref = sql_value( "select ref as value from collection where name='".mysql_real_escape_string($this->collection)."'", 0 );

            if( isset( $col_ref ) ) {
                add_resource_to_collection( $rid, $col_ref );
            }
        }

        # set access rights
        if( $this->access != null ) {
            sql_query( "update resource set access='".mysql_real_escape_string($this->access)."' where ref='".mysql_real_escape_string($rid)."'");
        }

    }

    function createXMLNode( &$xml ) {
        $r = $xml->createElement( "resource" );
        $atype = $xml->createAttribute( "type" );
        $atype->value = $this->type;
        $r->appendChild( $atype );

        // todo: check for not null to not insert empty "fields"?

        // add filename, collection and access rights
        if( $this->collection != null ) {
            $ncol = $xml->createElement( "collection", $this->collection );
            $r->appendChild( $ncol );
        }

        if( $this->filename != null ) {
            $nfil = $xml->createElement( "filename", $this->filename );
            $r->appendChild( $nfil );
        }

        if( $this->access != null ) {
            $nacc = $xml->createElement( "access", $this->access );
            $r->appendChild( $nacc );
        }


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
