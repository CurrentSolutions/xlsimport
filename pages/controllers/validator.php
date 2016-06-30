
<?php


//returns $valid 0=noerrors no warnings, 1=warnings, 2=errors
function filesystemValidator( &$errors, &$warnings , &$conflictsMap ) {
    $valid = 0;
    foreach( $conflictsMap as $key => $value ){
        $valid = 2;
        $errors[ $key ] = $value;
    }
    return $valid;
}

//returns $valid 0=noerrors no warnings, 1=warnings, 2=errors
function resourceValidator( &$errors, &$warnings, &$resources, &$lang ){
    $valid = 0;
    foreach( $resources as $row => $res ){
        $rid = $res->resourceId();
        if( $rid == 0 ){
            $valid = 2;
            array_push( $errors, sprintf( $lang['xlsimport_error_resource_not_found'], $row ) );
        }

        if( $res->getType() == 0 ) {
            $valid = 2;
            array_push( $errors, sprintf( $lang['xlsimport_error_resource_type_unknown'], $row, $res->getTypeName() ) );
        }
    }
    return $valid;
}

//returns $valid 0=noerrors no warnings, 1=warnings, 2=errors
function tableValidator( &$errors, &$warnings, &$excel, &$lang ){
    $valid = 0;
    for( $col = 1; $col <= $excel->numCols(); $col++ ) {
        if( empty($excel->valueAt( 1, $col )) ){
            for( $row = 2; $row <= $excel->numRows(); $row++ ){
                if( $excel->valueAt( $row, $col) !== '' ){
                    array_push( $warnings, sprintf( $lang['xlsimport_warning_column_without_name'], chr( $col + 64 )) );
                    $row = $excel->numRows();
                    $valid = 1;
                }
            }
        }else{
            for( $comp_col = ($col + 1); $comp_col <= $excel->numCols(); $comp_col++ ) {
                if($excel->valueAt( 1, $col ) === $excel->valueAt( 1, $comp_col )){
                    array_push( $errors, sprintf( $lang['xlsimport_warning_colname_duplicate'], chr( $col + 64 ), chr( $comp_col + 64 )));
                    $valid = 2;
                }
            }
        }
    }
    return $valid;
}

//returns $valid 0=noerrors no warnings, 1=warnings, 2=errors
//function rowValidator( &$errors, &$warnings, &$resources, &$uploadsMap ){
//    $valid = 0;
//    $tmp = checkForFiles( $errors, $warnings, $resources, $uploadsMap );
//    if($tmp > $valid) $valid = $tmp;
//    return $valid;
//}

//returns $valid 0=noerrors no warnings, 1=warnings, 2=errors
function rowValidator( &$errors, &$warnings ){
    $valid = 0;
    if( count($warnings) > 0 ) $valid = 1;
    if( count($errors) > 0 ) $valid = 2;
    return $valid;
}

//the obsolete but in any case the array in array thing looks stupid -khp
function checkForFiles( &$errors, &$warnings, &$resources, &$uploadsMap, &$lang ) {
  //    global $mediaPath;
    $valid = 0;
    foreach( $resources as $row => $res ) {
        $filename = $res->getFilename();
	    $tmp = checkFile( basename( $filename ), $uploadsMap );
	    if ( $tmp === 2 ){
	        $valid = $tmp;
	        if( !array_key_exists( $row, $errors)) $errors[$row] = Array();
	        if( $filename === '' ) array_push( $errors[$row], 'In Zeile ' . $row . ': Dateifeld ist leer.');
	        else array_push( $errors[$row], sprintf($lang['xlsimport_error_file_not_found'], $row, $filename)); 
	    }
    }
    return $valid;
}

function checkFile( $filename,  &$uploadsMap ) {
  //    global $mediaPath;
    if( !array_key_exists( $filename, $uploadsMap ) ) {
        return 2;
    }
    return 0;
}


?>
