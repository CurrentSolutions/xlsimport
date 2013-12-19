<?php
	$xlsFilename = $_REQUEST['filename'];
	error_reporting(E_ALL ^ E_NOTICE);
	require_once 'excel_reader2.php';
	$data = new Spreadsheet_Excel_Reader($xlsFilename);
?>
<html>
<head>
<style>
table.excel {
        border-style:ridge;
        border-width:1;
        border-collapse:collapse;
        font-family:sans-serif;
        font-size:12px;
}
table.excel thead th, table.excel tbody th {
        background:#CCCCCC;
        border-style:ridge;
        border-width:1;
        text-align: center;
        vertical-align:bottom;
}
table.excel tbody th {
        text-align:center;
        width:20px;
}
table.excel tbody td {
        vertical-align:bottom;
}
table.excel tbody td {
    padding: 0 3px;
        border: 1px solid #EEEEEE;
}
</style>
</head>

<body>
<?php 
$row = 0;
$col = 0;
echo '<table>';
for($row = 0; $row < 5; $row++){
        echo '<tr>';
        for($col = 0; $col < 5; $col++){
                echo '<td>';
                echo $data->val($row,$col);
                echo '</td>';
        }
        echo '</tr>';
}
echo '</table>';
echo $data->dump(true,true); 
?>
</body>
</html>