<style>
.mappingtable {border-collapse: collapse;}
.mappingtable td {border:1px solid #999;padding:4px;}
.mappingheader, .mappingheader td {background-color:#ddd;font-weight:bold;}
</style>

<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

<div class="Title">Configuration</div>

<form name="fieldmapping" method=GET>
    <table class='mappingtable' width='100%' class='table_excel'>
        <tr>
            <td>Path on server to uploaded media files.</td>
            <td><input name="mediaPath" type="text" value="<?php echo $mediaPath ?>"/></td>
        </tr>
        <tr>
            <td>Number of columns to show in field maping</td>
            <td><input name="maxCols" type="text" value="<?php echo $maxCols ?>"/></td>
        </tr>
        <tr>
            <td>Number of rows to show in preview of mapping</td>
            <td><input name="maxRows" type="text" value="<?php echo $maxRows ?>" /></td>
        </tr>
        <tr>
            <td>Default template for remarks</td>
            <td><textarea rows="4" class="stdwidth" name="template"><?php echo htmlspecialchars( $template ) ?></textarea></td>
        </tr>
        <tr>
            <td colspan=2><input class="stdwidth" type="submit" name="store" value="Speichern" /></td>
        </tr>
    </table>
</form>

</div>
</div>
</div>

