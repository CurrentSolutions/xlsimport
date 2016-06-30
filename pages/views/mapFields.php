<style>
.mappingtable {border-collapse: collapse;}
.mappingtable td {border:1px solid #999;padding:4px;}
.mappingheader, .mappingheader td {background-color:#ddd;font-weight:bold;}
</style>

<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

<div class="Title"><?php echo $lang['xlsimport_mapping'] ?></div>

    <form name="fieldmapping" method=GET>
<?php

$col = 1;
$colstart = $col;

while( $colstart <= $excel->numCols() ) {
?>
    <table class='mappingtable'>
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
    <input type=hidden name="userfile" value="<?php echo basename( $excel->getSource() )?>" />
    <input type="hidden" name="LAST_ACTION" value="transformation" />

    <div class="Question">
        <label><?php echo $lang["xlsimport_mapfields_notes"]?></label>
        <textarea rows="4" class="stdwidth" name="remarktemplate"><?php echo $template ?></textarea>
        <p></p>
        <div class="clearerleft" />
    </div>

    <div class="Question">
        <label><?php echo $lang["xlsimport_mapfields_keyfield"]?></label>
        <select class="stdwidth" name="keyfield" id="keyfield" onChange="rsKeyFieldSelected();" ></select>
        <div class="clearerleft" />
        <div id="chooseKeyfieldMsg"><font color="red"><?php echo $lang['xlsimport_error_choose_keyfield']; ?></font></div>
    </div>

    <div class="Question">
        <?php echo $lang["xlsimport_mapfields_update_only"]?>
        <input type="checkbox" <?php if( $mapping[ 'updateOnly' ] == 1 ) print "checked" ?> name="updateonly" id="updateonly" value="1">
        <div class="clearerleft" />
    </div>

    <div class="QuestionSubmit">
        <label></label>
        <input class="stdwidth" disabled type="submit" id="submitButton" value="<?php echo $lang["xlsimport_mapfields_continue"] ?>" />
    </div>
    </form>
</div>
</div>
</div>

<script type="text/javascript">
    var defaultSelectValue = 'unused';
    var rsSelected = new Array;
    var rsKeyField;
    var rsFields = new Array;
    var rsNoFields = new Array;
<?php

$fields = Mapper::getFields($lang);

foreach( $fields as $n => $t ) {
?>
    rsFields[ <?php echo "'$n'"?> ] = <?php echo "'$t'" ?>;
    <?php if (array_key_exists($n, Mapper::$unmapableFields)) { ?>
    rsNoFields[ <?php echo "'$n'"?> ] = <?php echo "'$t'"; }?>;
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
<?php
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

<script src="<?php global $baseurl; echo $baseurl;?>/plugins/xlsimport/pages/views/selektor.js" type="text/javascript"></script>
