<?php
/*
 */

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';
include '../../../include/resource_functions.php';

// Specify the name of this plugin and the heading to display for the page.
$plugin_name = 'xlsimport';
//$plugin_page_heading = $lang['test_configuration1'];

// Build the $page_def array of descriptions of each configuration variable the plugin uses.

// Do the page generation ritual -- don't change this section.
include '../../../include/header.php';

include 'controllers/config.php';
include 'views/view.php';

if( isset( $_REQUEST['store'] ) ) {
    if( isset( $_REQUEST['maxCols'] ) ) {
        $maxCols = $_REQUEST['maxCols'];
        putConfig( "maxCols", $maxCols );
    }
    if( isset( $_REQUEST['maxRows'] ) ) {
        $maxRows = $_REQUEST['maxRows'];
        putConfig( "maxRows", $maxRows );
    }
    if( isset( $_REQUEST['template'] ) ) {
        $template = $_REQUEST['template'];
        putConfig( "template", $template );
    }
}
View::configure();

include '../../../include/footer.php';

?>
