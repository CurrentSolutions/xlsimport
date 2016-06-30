<?php

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';
include '../../../include/resource_functions.php';

include 'model/filesystem.php';
include 'model/db.php';
include 'model/excel.php';
include 'model/resource.php';
include 'views/view.php';
include 'controllers/synonymes.php';
include 'controllers/mapper.php';
include 'controllers/history.php';
include 'controllers/validator.php';
include 'controllers/config.php';

// Specify the name of this plugin and the heading to display for the page.
$plugin_name = 'xlsimport';
$plugin_page_heading = $lang['xlsimport_configuration'];


$_debug = 1;
$lastAction = 'undefined';
if(isset($_REQUEST['LAST_ACTION']))
{
    $lastAction = $_REQUEST['LAST_ACTION'];
}

switch ($lastAction) {


case 'transformation':
    include '../../../include/header.php';

    $conflictsMap = Array();
    $uploadsMap = getUploadsMap( $conflictsMap );
    $filesystemErrors = Array();
    $filesystemWarnings = Array();
    if( isset( $_REQUEST['updateonly'] ) ) {
        $filesystemValid = 0;
    }
    else {
        $filesystemValid = filesystemValidator( $filesystemErrors, $filesystemWarnings, $conflictsMap);
    }

    // todo: check for variables!
    // todo: rs braucht "name" für "Anmerkungen"
    $excel = new Excel( $mediaPath.$_REQUEST['userfile'] );
    $tableErrors = Array();
    $tableWarnings = Array();
    $tableValid = tableValidator( $tableErrors, $tableWarnings, $excel, $lang );

    $mapper = new Mapper( $excel, $_REQUEST );

    $template = $_REQUEST['remarktemplate'];

    History::save( $excel, $_REQUEST['keyfield'], $template, $_REQUEST );

    $filenameCol = $mapper->getColByName( 'filename' );

    //todo check if this mean we abort the upload entirely
    if( $filenameCol == null ) {
        View::debug( $lang['xlsimport_filename_not_provided'], 1 );
    }

    // check for files to exist
    // $ret = checkForFiles( $excel, $mapper->getColByName( "filename" ), $uploadsMap );
    //  if( $ret !== 0 ) {
    //    View::debug( "Could not find file ".$ret, 1 );
    //    //        break;
    // }
    $xml_source = null;
    $md5r = null;
    // generate all Resources
    $rowErrors = Array();
    $rowWarnings = Array();
    $res = getResources( $excel, $mapper, $template, $uploadsMap, $rowErrors, $rowWarnings, $lang );
    $resourceErrors = Array();
    $resourceWarnings = Array(); 
    $resourcesValid = 0;
    if( isset( $_REQUEST['updateonly'] ) ) {
        $resourcesValid = resourceValidator( $resourceErrors, $resourceWarnings, $res, $lang );
    }
    $rowsValid = rowValidator( $rowErrors, $rowWarnings );
    
    if( $filesystemValid === 0 && $resourcesValid === 0 && $tableValid === 0 && $rowsValid === 0 ) {

        // transform to XML format
        $xml = transformToXML( $res );
        $xml->save( $excel->getSource().".xml" );
        //    echo $xml->saveXML();
        //$xml->saveXML();//proftpd //pathinfo string []

        $xml_source = $xml->saveXML();
        $xml_source = escape_check( $xml_source );
        $xml_source = str_replace( "\\n", "", $xml_source );
        $md5r = md5($scramble_key . $xml_source);
        //    echo $scramble_key;

        foreach( $res as $r ) {
            $r->updateResource();
        }
    }
    View::importXML( $filesystemValid, $filesystemErrors, $filesystemWarnings, $resourcesValid, $resourceErrors, $resourceWarnings, $tableValid, $tableErrors, $tableWarnings, $rowsValid, $rowErrors, $rowWarnings, $baseurl."/uploads/".basename( $excel->getSource() ).".xml", $xml_source, $md5r );
    include '../../../include/footer.php';
    break;


case 'xls_upload':
    include '../../../include/header.php';

    $conflictsMap = Array();
    $uploadsMap = getUploadsMap( $conflictsMap );
    $filesystemErrors = Array();
    $filesystemWarnings = Array();
    $filesystemValid = filesystemValidator( $filesystemErrors, $filesystemWarnings, $conflictsMap);

    // In PHP kleiner als 4.1.0 sollten Sie $HTTP_POST_FILES anstatt 
    // $_FILES verwenden.

    $uploadfile = basename($_FILES['userfile']['name']);
    $uploadpath = $mediaPath . $uploadfile;

    if( move_uploaded_file( $_FILES['userfile']['tmp_name'], $uploadpath ) ) {
        //View::debug( "Datei ist valide und wurde erfolgreich hochgeladen.", 0 );

        error_reporting(E_ALL ^ E_NOTICE);

        $excel = new Excel( $uploadpath );

        $tableErrors = Array();
        $tableWarnings = Array();
        $tableValid = tableValidator( $tableErrors, $tableWarnings, $excel, $lang );

        $mapping = History::getLastMapping( basename( $excel->getSource() ) );

        // leads to 'transformation'
        // show only first three rows of excel table
        View::mapFields( $mapping, $excel, $filesystemValid, $filesystemErrors, $tableValid, $tableErrors, $tableWarnings );

    } else {
        View::debug( $lang['xlsimport_error_on_xls_file_upload'], 0 );
    }


    include '../../../include/footer.php';
    break;


default:
    include '../../../include/header.php';

    $conflictsMap = Array();
    $uploadsMap = getUploadsMap( $conflictsMap );
    $filesystemErrors = Array();
    $filesystemWarnings = Array();
    $filesystemValid = filesystemValidator( $filesystemErrors, $filesystemWarnings, $conflictsMap);
    // leads to 'xls_upload'
    View::chooseXLS($filesystemValid, $filesystemErrors);
    include '../../../include/footer.php';
    break;
}


function getResources( &$excel, &$mapper, $template, &$uploadsMap, &$errorMap, &$warningsMap, $lang ) {
    global $mediaPath, $userref;
    $resources = array();

    $keyId = $mapper->getIdByName( $_REQUEST[ 'keyfield' ] );

    $keys = array();

    for( $row = 2; $row <= $excel->numRows(); ++$row ) {

        $type = null;
        $typeName = null;
        $collection = null;
        $filename = null;
        $fields = array();
        $access = null;
        $remark = $template;

        $isempty = true;

        for( $col = 1; $col <= $excel->numCols(); ++$col ) {

            // first try to substitute field in template
            $remark = str_replace( "%".$col."%", $excel->valueAt( $row, $col ), $remark );
            if( $excel->valueAt( 1, $col ) != "" ) {
                $remark = str_replace( "%".$excel->valueAt( 1, $col )."%", $excel->valueAt( $row, $col ), $remark );
            }

            $name = $mapper->getNameByCol( $col );
            $id = $mapper->getIdByName( $name );

            if( $name == null )
                continue;

            $v = $excel->valueAt( $row, $col );
            if( $v != "" )
                $isempty = false;

            switch( strtolower( $name ) ) {
            case "filename":
                if( isset( $_REQUEST['updateonly'] ) )
                    continue;
                // choose correct subdirectory - use uploadsMap
                $b = pathinfo( $v );
                $b = $b['basename'];
                if( $v ==''){
                    $errorMap[$row] = sprintf( $lang['xlsimport_error_no_filename'], $row );
                    break;
                }
                if( !array_key_exists( $b, $uploadsMap) ){
                    $errorMap[$row] = sprintf( $lang['xlsimport_error_file_not_found'], $row, $v );
                    break;
                }
                $filename = $uploadsMap[ $b ];
                break;

            case "collection_create":
                $collection = $v;
                break;

            case "type":
                $type = $mapper->getResourceType( $v );
                $typeName = $v;
                break;

            case "access":
                $access = $mapper->getAccessType( $v );
                break;

            default:
                $fields[ $id ] = $v;
            }
        }

        $id = $mapper->getIdByName( "" ); // eigentlich sollte das feld remarks heißen - hat aber in RS als einziges Feld keinen "name"... strange!
        $fields[ $id ] = $remark;

        if( $isempty ) {
            unset( $errorMap[$row] );
            continue;
        }

        if( $fields[ $keyId ] == "" )
            $errorMap[$row] = sprintf( $lang['xlsimport_error_no_key_value'], $row, $_REQUEST['keyfield'] );
        else if( isset( $keys[ $fields[ $keyId ] ] ) )
            $errorMap[$row] = sprintf( $lang['xlsimport_error_key_value_not_unique'], $row, $keys[ $fields[ $keyId ] ] );
        else
            $keys[ $fields[ $keyId ] ] = $row;

        // TODO: have a check box for keyfield column
        $r = new Resource( $filename, $collection, $type, $typeName, $access, $fields, $mapper->getIdByName( $_REQUEST[ 'keyfield' ] ), $userref);
        $resources[$row] = $r;
    }

    return $resources;
}


function transformToXML( &$resources ) {
    $xml = new DOMDocument( "1.0" );
    $xml->formatOutput = true;

    $set = $xml->createElement( "resourceset" );
    $xml->appendChild( $set );

    foreach( $resources as $res ) {
        $node = $res->createXMLNode( $xml );

        $set->appendChild( $node );
    }

    return $xml;
}

?>
