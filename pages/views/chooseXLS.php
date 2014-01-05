<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordHeader"> 

   <div class="Title"><?php echo $lang["xlsimport_choosexls_title"] ?></div>

<!-- Die Encoding-Art enctyoe MUSS wie dargestellt angegeben werden -->
<form enctype="multipart/form-data" action="<?php $_SERVER['SERVER_NAME'] ?>" method="POST">
    <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
<!--    <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" /> -->
    <div class="Question">
        <label><? echo $lang["xlsimport_choosexls_title"] ?></label>
        <input class="stdwidth" name="userfile" type="file" />
        <div class="clearerleft" />
    </div>

    <div class="QuestionSubmit">
        <label></label>
        <input type="hidden" name="LAST_ACTION" value="xls_upload" />
   <input class="stdwidth" type="submit" value=<? echo $lang["xlsimport_choosexls_title"] ?> />
    </div>
</form>

</div>
</div>
</div>
