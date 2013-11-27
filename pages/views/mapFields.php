<style>
.mappingtable {border-collapse: collapse;}
.mappingtable td {border:1px solid #999;padding:4px;}
.mappingheader, .mappingheader td {background-color:#ddd;font-weight:bold;}
</style>

<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

<div class="Title">Mapping</div>

    <form name="fieldmapping" method=GET>
<?php

$col = 1;
$colstart = $col;

while( $colstart <= $excel->numCols() ) {
?>
    <table class='mappingtable' width='100%' class='table_excel'>
<?php
    for( $row=1; $row <= $excel->numRows() && $row <= $maxRows; $row++ ) {
?> 
        <tr>
<?php
        for( $col = $colstart; $col <= $excel->numCols() && $col - $colstart < $maxCols; $col++ ) {
            if( $row == 1 )
                $style = " class='mappingheader'";
            else
                $style = "";
?>
            <td<?php echo $style?>><?php echo $excel->valueAt( $row, $col ) ?></td>
<?php
        }
?>
        </tr>
<?php
    }
?>
        <tr>
<?php
    for( $col = $colstart; $col <= $excel->numCols() && $col - $colstart < $maxCols; $col++ ) {

        $colName = $col; //'col' . chr(($col+64));
?>
            <td>
                <select class="shrtwidth" name="<?php echo $colName ?>" size="1" id="<?php echo $colName ?>Select" onChange="rsFieldSelected(<?php echo $col ?>)"></select>
            </td>
<?php
    }
?>
        </tr>
    </table><br>

<?php

    $colstart = $col;
}
?>
    <input type=hidden name="userfile" value="<?php echo pathinfo( $excel->getSource() )[ 'basename' ]?>" />
    <input type="hidden" name="LAST_ACTION" value="transformation" />

    <div class="Question">
        <label>Template für Anmerkungen:</label>
        <textarea rows="4" class="stdwidth" name="remarktemplate"><?php echo $template ?></textarea>
        <p></p>
        <div class="clearerleft" />
    </div>

    <div class="Question">
        <label>Schlüsselfeld:</label>
        <select class="stdwidth" name="keyfield" id="keyfield" onChange="rsKeyFieldSelected()" ></select>
        <div class="clearerleft" />
    </div>

    <div class="QuestionSubmit">
        <label></label>
        <input class="stdwidth" type="submit" value="los geht's" />
    </div>
    </form>
</div>
</div>
</div>

<script type="text/javascript">
    var defaultSelectValue = 'unused';
    var rsSelected = new Object;
    var rsKeyField;
    var rsFields = new Object;
<?php

$fields = Mapper::getFields();

foreach( $fields as $n => $t ) {
?>
    rsFields[ <?php echo "'$n'"?> ] = <?php echo "'$t'" ?>;
<?php
}

// do mapping...
if( $mapping != null ) {
    if( array_key_exists( "map", $mapping ) ) {
        foreach( $mapping[ 'map' ] as $col => $val ) {
            if( $val == 'unused' )
                continue;
?>
    rsSelected[ <?php echo ($col-1)?> ] = <?php echo "'$val'"?>;
<?
        }
    }

    if( array_key_exists( "keyfield", $mapping ) ) {
?>
    rsKeyField = '<?php echo $mapping['keyfield']?>';
<?php
    }
}
?>
</script>

<?php include 'views/selektor.js' ?>

