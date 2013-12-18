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

$fields = sql_query( "select ref, resource_type, order_by from resource_type_field order by resource_type, order_by" );
for( $i = 0; $i < count( $fields ); $i++ ) {
	$f = get_field( $fields[$i]["ref"] );
	echo "Field: ".$f["ref"]." und ".$f["title"]."<br>\n";
	var_dump( get_field( $fields[$i]["ref"] ) );
}


$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

var_dump(json_decode($json));
var_dump(json_decode($json, true));


include '../../../include/footer.php';

?>
