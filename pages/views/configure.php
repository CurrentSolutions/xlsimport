<?php global $lang;?>
<style>
.mappingtable {border-collapse: collapse;}
.mappingtable td {border:1px solid #999;padding:4px;}
.mappingheader, .mappingheader td {background-color:#ddd;font-weight:bold;}
</style>

<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

<div class="Title"><?php echo $lang['xlsimport_setup_title'];?></div>

<form name="fieldmapping" method=GET>
    <table class='mappingtable'>
        <tr>
            <td><?php echo $lang['xlsimport_setup_path'];?></td>
            <td><input name="mediaPath" type="text" style="width:75%; height:auto; resize: none; overflow: auto;" value="<?php echo $mediaPath ?>"/></td>
        </tr>
        <tr>
            <td><?php echo $lang['xlsimport_setup_columns'];?></td>
            <td><input name="maxCols" type="text" value="<?php echo $maxCols ?>"/></td>
        </tr>
        <tr>
            <td><?php echo $lang['xlsimport_setup_rows'];?></td>
            <td><input name="maxRows" type="text" value="<?php echo $maxRows ?>" /></td>
        </tr>
        <tr>
            <td><?php echo $lang['xlsimport_setup_remarks'];?></td>
            <td><textarea rows="4" class="stdwidth" name="template"><?php echo htmlspecialchars( $template ) ?></textarea></td>
        </tr>
        <tr>
            <td colspan=2><input class="stdwidth" type="submit" name="store" value="<?php echo $lang["save"];?>" /></td>
        </tr>
    </table>
</form>

</div>
</div>
</div>

