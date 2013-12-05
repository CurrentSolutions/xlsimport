    <form enctype="multipart/form-data" action="<?php $_SERVER['SERVER_NAME'] ?>" method="POST">
        <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
        <!--    <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" /> -->
        <input type="hidden" name="LAST_ACTION" value="<?php echo $fallback ?>" /><!-- Zurueck an den Anfang-->
        <?php if(isset($excel)){ ?>
            <input type=hidden name="userfile" value="<?php echo pathinfo( $excel->getSource() )[ 'basename' ]?>" type="file"/>
	<?php }?>
        <input class="stdwidth" type="submit" value="neu laden" />
    </form>
<?php 
if( $filesystemValid !== 0 ){ 
?>
<div class="RecordBox">
<div class="RecordPanel" style="background:red">
<div class="RecordHeader">
    <div class="Title"><font color="white">Fehler im Dateisystem, folgende Dateinamen sind redundant:</font></div>
    <?php foreach($filesystemErrors as $key => $value){ ?>
        <table  style ="background-color:white; border-radius: 10px; margin: 10px; border-spacing: 2; border-collapse: separate;">
        <colgroup>
             <col width="60">
             <col width="30000">
        </colgroup>
        <?php foreach($value as $line){?>
                <tr>
                    <td>
                        <label><font color="red"><?php echo $key ?></font></label>
                    </td>
                    <td>
                        <label><font color="red"><?php echo $line ?></font></label>
                    </td>
                </tr>
        <?php }?>
        </table>		
    <?php } ?>
</div>
</div>
</div>
<?php 
}
if( isset($tableValid) ){
    if( $tableValid !== 0 ){ 
?>
<div class="RecordBox">
<div class="RecordPanel" style="background:red">
<div class="RecordHeader">
    <div class="Title"><font color="white">Fehler in der Tabelle:</font></div>
        <table  style ="background-color:white; border-radius: 10px; margin: 10px; border-spacing: 2; border-collapse: separate;">
        <colgroup>
             <col width="30000">
        </colgroup>
        <?php foreach($tableErrors as $value){ ?>
            <tr>
                <td>
                    <label><font color="red"><?php echo $value ?></font></label>
                </td>
            </tr>
        <?php }?>
        </table>		
        <table  style ="background-color:white; border-radius: 10px; margin: 10px; border-spacing: 2; border-collapse: separate;">
        <colgroup>
             <col width="30000">
        </colgroup>
        <?php foreach($tableWarnings as $value){ ?>
            <tr>
                <td>
                    <label><font color="orange"><?php echo $value ?></font></label>
                </td>
            </tr>
        <?php }?>
        </table>		
</div>
</div>
</div>
<?php 
    } 
}
if( isset( $rowsValid )){
    if( $rowsValid !== 0 ){ 
?>
<div class="RecordBox">
<div class="RecordPanel" style="background:red">
<div class="RecordHeader">
    <div class="Title"><font color="white">Fehler in einzelnen Zeilen:</font></div>
    <?php foreach($rowErrors as $key => $value){ ?>
        <table  style ="background-color:white; border-radius: 10px; margin: 10px; border-spacing: 2; border-collapse: separate;">
        <colgroup>
             <col width="60">
             <col width="30000">
        </colgroup>
        <?php foreach($value as $line){?>
                <tr>
                    <td>
                        <label><font color="red"><?php echo $key ?></font></label>
                    </td>
                    <td>
                        <label><font color="red"><?php echo $line ?></font></label>
                    </td>
                </tr>
        <?php }?>
        </table>		
    <?php } ?>
</div>
</div>
</div>
<?php 
    }
}
?>
