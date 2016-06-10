<?php

function HookXlsimportAllToptoolbaradder() {
	if (!checkperm("a")) return;
	global $baseurl, $lang;
	?>
	<li><a href="<?php echo $baseurl?>/plugins/xlsimport/pages/index.php"><?php echo $lang['xlsimport_nav_title']?></a></li>
<?php
}?>