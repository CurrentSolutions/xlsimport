
<?php

include 'controllers/config.default.php';

function loadConfiguration() {
    global $maxCols;
    global $maxRows;
    global $template;

    global $defaultMaxCols;
    global $defaultMaxRows;
    global $defaultTemplate;

    $maxCols = safeGetConfig( "maxCols", $defaultMaxCols );
    $maxRows = safeGetConfig( "maxRows", $defaultMaxRows );
    $template = safeGetConfig( "template", $defaultTemplate );
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
