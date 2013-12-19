<?php

require_once 'external/excelreader/excel_reader2.php';

class Excel {
    private $reader = null;
    private $source;

    function __construct( $filename ) {

        $this->source = $filename;

        $this->reader = new Spreadsheet_Excel_Reader( $filename, true, "UTF-16LE" );

        $this->reencode();
    }

    public function getSource() {
        return $this->source;
    }

    private function reencode() {
        for( $row = 1; $row <= $this->numRows(); $row++ ) {
            $a[$row] = array();

            for( $col = 1; $col <= $this->numCols(); $col++ ) {
                $this->reader->reencode( $row, $col, 'UTF-8' );
            }
        }
    }

    public function numRows() {
        return $this->reader->rowcount();
    }

    public function numCols() {
        return $this->reader->colcount();
    }

    public function valueAt( $row, $col ) {
        return $this->reader->val( $row, $col );
    }

    public function dataAsArray() {
        $a = array();

        for( $row = 1; $row <= $this->numRows(); $row++ ) {
            $a[$row] = array();

            for( $col = 1; $col <= $this->numCols(); $col++ ) {
                $a[$row][$col] = $this->valueAt( $row, $col );
            }
        }

        return $a;
    }
}

?>
