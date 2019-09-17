
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("late.xls");
$column_header = array('employee id','name','pay code','january','february','march','april','may','june','july','august','september','october','november','december','total');
$excel->writeLine($column_header);


$select = "select tb1.late, tb2.name, tb2.em_id, tb2.pay_id from transaction tb1 join employee tb2 using(em_id) where tb1.late>0 and tb2.trxn_date like '%" . date('y') . "'";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){	
	$excel->writeLine(array($row['em_id'],$row['name'],$row['pay_id']));
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=late.xls');
header("Location: late.xls");
exit;

?>