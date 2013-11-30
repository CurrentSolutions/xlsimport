
<div class="RecordBox">
<div class="RecordPanel"> 

<div class="Title">XML Ergebnisse</div>

<div class="TabbedPanel">
<div class="RecordStory">
<p>
    You can also review the XML ingredients for remote import 
    <a href="<?php echo $xmlurl;?>">here</a>.
</p>
</div>
</div>

<p>
<FORM action="/resourcespace/plugins/remoteimport/pages/update.php" method="POST">
    <input type="hidden" name="xml" value="<?php echo htmlspecialchars( $xml_source ) ?>">
    <input type="hidden" name="sign" value="<?php echo $md5r ?>">
    <input class="shrtwidth" type="submit" name="submit" value="Import resources"/>
</FORM>
</p>

</div>
</div>
