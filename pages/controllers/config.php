
<?php

include 'controllers/config.default.php';

function loadConfiguration() {
    global $maxCols;
    global $maxRows;
    global $template;
    global $mediaPath;

    global $defaultMaxCols;
    global $defaultMaxRows;
    global $defaultTemplate;
    global $defaultMediaPath;
    global $storagedir;
    
    $maxCols = safeGetConfig( "maxCols", $defaultMaxCols );
    $maxRows = safeGetConfig( "maxRows", $defaultMaxRows );
    $template = safeGetConfig( "template", $defaultTemplate );
    $mediaPath = safeGetConfig( "mediaPath", $defaultMediaPath );
    
    // build better default value
    if (!strcmp($mediaPath, $defaultMediaPath)) {
    	$xlsImportDir = str_replace('filestore-', 'xlsimport-', $storagedir);
    	if (file_exists($xlsImportDir)) $mediaPath = $xlsImportDir . '/';
    }
    
}

function safeGetConfig( $name, $defaultValue ) {
    $config = sql_query( "select value from xlsimport_config WHERE name='".escapeString( $name )."'", true );

    if( count( $config ) == 0 ) {
        sql_query( "insert into xlsimport_config set name='".escapeString( $name )."', value='".escapeString( $defaultValue )."'" );
        return $defaultValue;
    }

    return $config[0]["value"];
}

function putConfig( $name, $value ) {
    $config = sql_query( "select value from xlsimport_config WHERE name='".escapeString( $name )."'", true );

    if( count( $config ) == 0 ) {
        sql_query( "insert into xlsimport_config set name='".escapeString( $name )."', value='".escapeString( $value )."'" );
    }
    else {
        sql_query( "update xlsimport_config set value='".escapeString( $value )."' where name='".escapeString( $name )."'" );
    }
}

loadConfiguration();

?>
