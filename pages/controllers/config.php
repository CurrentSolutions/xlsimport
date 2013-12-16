
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
    $config = sql_query( "select value from xlsimport_config WHERE name='".mysql_real_escape_string($name)."'", true );

    if( count( $config ) == 0 ) {
        sql_query( "insert into xlsimport_config set name='".mysql_real_escape_string($name)."', value='".mysql_real_escape_string($defaultValue)."'" );
        return $defaultValue;
    }

    return $config[0]["value"];
}

loadConfiguration();

?>
