<?php

include "../../../include/image_processing.php";
include "../../../include/collections_functions.php";

class Resource {
    private $type;
    private $typeName;
    private $filename;
    private $collection;
    private $access;
    private $fields = array();
    private $keyfield;
    private $userid;

    public function __construct( $filename, $collection, $type, $typeName, $access, $fields, $keyfield, $userid=1 ) {
        $this->type = $type;
        $this->typeName = $typeName;
        $this->filename = $filename;
        $this->collection = $collection;
        $this->access = $access;
        $this->fields = $fields;
        $this->keyfield = $keyfield;
        $this->userid = $userid;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getBaseFilename() {
    	return pathinfo($this->filename)['basename'];
    }
    
    public function resourceId() {
        $ref = sql_value( "
            select resource value
            from resource_data
            where resource_type_field='".escapeString( $this->keyfield )."' and value='".escapeString( $this->fields[$this->keyfield] )."'", 0 );
        return $ref;
    }

    public function getType() {
        return $this->type;
    }

    public function getTypeName() {
        return $this->typeName;
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
			sql_query( "update resource set file_extension='".escapeString($extension)."' where ref='".escapeString($rid)."'" );
		}

        # add resource to collection (if the collection exists)
        if( $this->collection != null ) {
            $col_ref = sql_value( "select ref as value from collection where name='".escapeString($this->collection)."'", 0 );

            if( isset( $col_ref ) && $col_ref === 0 ) {
            	$col_ref = create_collection( $this->userid, $this->collection );
            }
            
            if( isset( $col_ref ) && $col_ref !== 0 ) {
                add_resource_to_collection( $rid, $col_ref );
            }
        }

        # set access rights
        if( $this->access != null ) {
            sql_query( "update resource set access='" . escapeString($this->access) . "' where ref='" . escapeString($rid) . "'");
        }

    }

    function createXMLNode( &$xml ) {
        $r = $xml->createElement( "resource" );
        $atype = $xml->createAttribute( "type" );
        $atype->value = utf8_encode( $this->type );
        $r->appendChild( $atype );

        // todo: check for not null to not insert empty "fields"?

        // add filename, collection and access rights
        if( $this->collection != null ) {
            $ncol = $xml->createElement( "collection", utf8_encode( $this->collection ) );
            $r->appendChild( $ncol );
        }

        if( $this->filename != null ) {
            $nfil = $xml->createElement( "filename", utf8_encode( $this->filename ) );
            $r->appendChild( $nfil );
        }

        if( $this->access != null ) {
            $nacc = $xml->createElement( "access", utf8_encode( $this->access ) );
            $r->appendChild( $nacc );
        }


        // add all fields
        foreach( $this->fields as $id => $value ) {
            if( $id == $this->keyfield )
                $field = $xml->createElement( "keyfield", utf8_encode( htmlentities( $value ) ) );
            else {
                $field = $xml->createElement( "field", utf8_encode( htmlentities( $value ) ) );
            }
            $ref = $xml->createAttribute( "ref" );
            $ref->value = utf8_encode( $id );
            $field->appendChild( $ref );
            $r->appendChild( $field );
        }

        return $r;
    }
}

?>
