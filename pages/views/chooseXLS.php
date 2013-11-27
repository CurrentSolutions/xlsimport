
<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

<div class="Title">XLS-Dateiwahl</div>

<!-- Die Encoding-Art enctyoe MUSS wie dargestellt angegeben werden -->
<form enctype="multipart/form-data" action="<?php $_SERVER['SERVER_NAME'] ?>" method="POST">
    <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
<!--    <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" /> -->
    <input type="hidden" name="LAST_ACTION" value="xls_upload" />

    <div class="Question">
        <label>XLS-Datei:</label>
        <input class="stdwidth" name="userfile" type="file" />
        <div class="clearerleft" />
    </div>

    <div class="QuestionSubmit">
        <label></label>
        <input class="stdwidth" type="submit" value="Datei senden" />
    </div>
</form>

</div>
</div>
</div>
