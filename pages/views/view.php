
<?php
//include '../../../include/general.php';

class View {
    public static $debug = 1;

    public static function debug( $line, $threshold ) {
        if( $threshold <= View::$debug ) {
            echo $line."</br>\n";
        }
    }


    public static function configure() {
        global $maxCols;
        global $maxRows;
        global $template;
        include 'views/configure.php';
    }


    public static function chooseXLS( $filesystemValid , &$filesystemErrors ) {
        global $lang;
        //if filesystem contained errors
        if( $filesystemValid !== 0 ){
            $fallback = 'startover';
            include 'views/error.php';
            return;
        }
        include 'views/chooseXLS.php';
    }

    public static function error( &$filesystemErrors ) {
        include 'views/error.php';
    }

    public static function importXML( $filesystemValid, &$filesystemErrors, &$filesystemWarnings, $tableValid, &$tableErrors, &$tableWarnings, $rowsValid, &$rowErrors, &$rowWarnings, $xmlurl, $xml_source, $md5r ) {
        global $lang;
        if($filesystemValid !== 0 || $tableValid !== 0 || $rowsValid !== 0 ){
            $fallback = 'startOver';
            include 'views/error.php';
            return;
        }
        include 'views/importXML.php';
    }

    public static function mapFields( $mapping, $excel, $numRows, $filesystemValid, &$filesystemErrors , $tableValid, $tableErrors, $tableWarnings) {
	global $lang;
        //if filesystem contained errors
        if($filesystemValid !== 0 || $tableValid !== 0 ){
	  $fallback = 'startOver';
	  include 'views/error.php';
	  return;
	}
        global $maxRows;
        global $maxCols;
        global $template;

        if( $mapping != null && array_key_exists( "template", $mapping ) )
            $template = $mapping[ 'template' ];

        $js_vars = View::buildJSVars( $excel, $maxRows );
        echo $js_vars;
	
        include 'views/mapFields.php';
    }

    private static function buildJSVars( &$excel, $maxRows ) {
        $numRows = $excel->numRows();
        $numCols = $excel->numCols();

        $js_vars = '';
        $js_vars = $js_vars . '<script type="text/javascript">'."\n".'<!-- to hide script contents from old browsers'."\n";
        $js_vars = $js_vars . "var js_data = [];\n";
        for( $row=1; $row <= $numRows && $row <= $maxRows; $row++ ) {
            $js_vars = $js_vars . "js_data[".($row - 1)."] = [];\n";
            for( $col = 1; $col <= $numCols; $col++ ) {
                $js_vars = $js_vars . "js_data[".($row-1)."][".($col-1)."] = ".'"'.$excel->valueAt( $row, $col ).'"'.";\n";
            }
        }
        $js_vars = $js_vars . "// end hiding contents from old browsers  -->\n</script>";

        return $js_vars;
    }


}
?>
