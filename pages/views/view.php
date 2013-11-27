
<?php


class View {
    public static $debug = 1;

    public static function debug( $line, $threshold ) {
        if( $threshold <= View::$debug ) {
            echo $line."</br>\n";
        }
    }


    public static function chooseXLS() {
        include 'views/chooseXLS.php';
    }

    public static function importXML( $xmlurl, $xml_source, $md5r ) {
        include 'views/importXML.php';
    }

    public static function mapFields( $mapping, $excel, $numRows ) {
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
