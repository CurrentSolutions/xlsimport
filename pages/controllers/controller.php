<?php

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';
include '../../../include/resource_functions.php';

include 'model/excel.php';
include 'model/resource.php';
include 'model/filesystem.php';
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
$mediaPath = '/var/www/resourcespace/uploads/';
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
    $filesystemValid = filesystemValidator( $filesystemErrors, $filesystemWarnings, $conflictsMap);

    // todo: check for variables!
    // todo: rs braucht "name" für "Anmerkungen"
    $excel = new Excel( $mediaPath.$_REQUEST['userfile'] );
    $tableErrors = Array();
    $tableWarnings = Array();
    $tableValid = tableValidator( $tableErrors, $tableWarnings, $excel );

    $mapper = new Mapper( $excel, $_REQUEST );
    $template = $_REQUEST['remarktemplate'];

    History::save( $excel, $_REQUEST['keyfield'], $template, $_REQUEST );

    $filenameCol = $mapper->getColByName( "filename" );
    if( $filenameCol == null ) {
        View::debug( "Filename not provided", 1 );
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
    $res = getResources( $excel, $mapper, $template, $uploadsMap );
    $rowErrors = Array();
    $rowWarnings = Array();
    $rowsValid = rowValidator( $rowErrors, $rowWarnings, $res, $uploadsMap );
    
    if( $filesystemValid === 0 && $tableValid === 0 && $rowsValid === 0 ){
	
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
    View::importXML( $filesystemValid, $filesystemErrors, $filesystemWarnings, $tableValid, $tableErrors, $tableWarnings, $rowsValid, $rowErrors, $rowWarnings, $baseurl."/uploads/".basename( $excel->getSource() ).".xml", $xml_source, $md5r );

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
	$tableValid = tableValidator( $tableErrors, $tableWarnings, $excel );

        $mapping = History::getLastMapping( basename( $excel->getSource() ) );

        // leads to 'transformation'
        // show only first three rows of excel table
        View::mapFields( $mapping, $excel, 3 /*nice :) #TODO */, $filesystemValid, $filesystemErrors, $tableValid, $tableErrors, $tableWarnings );

    } else {
        View::debug( "Beim Hochladen der Datei ist ein Fehler aufgetreten.\n" );
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


function getResources( &$excel, &$mapper, $template, &$uploadsMap ) {
    global $mediaPath;
    $resources = array();

    for( $row = 2; $row <= $excel->numRows(); ++$row ) {

        $type = null;
        $collection = null;
        $filename = null;
        $fields = array();
        $access = null;
        $remark = $template;

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

            switch( strtolower( $name ) ) {
            case "filename":
                // choose correct subdirectory - use uploadsMap
	        $b = pathinfo( $v )['basename'];
	        if( array_key_exists( $b, $uploadsMap ) ) {
                    $filename = $uploadsMap[ $b ];
                }
                break;

            case "collection":
                $collection = $v;
                break;

            case "type":
                $type = $mapper->getResourceType( $v );
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

        // TODO: have a check box for keyfield column
        $r = new Resource( $filename, $collection, $type, $access, $fields, $mapper->getIdByName( $_REQUEST[ 'keyfield' ] ) );
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
