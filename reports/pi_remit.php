
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("pagibig.xls");


$column_header = array('Pag-Ibig ID No.', '', 'Last Name', 'First Name', 'Name Ext', 'Middle Name', 'Employee','Employer','Total','Remarks');
$excel->writeLine($column_header);

$appendsql = " ( ";
$var = explode("@@",$_GET['vars']);
for($x=0;$x<count($var);$x++){
	if ($var[$x]){
		if ($x==count($var)-2){
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "') ";
			}
		else{
			$appendsql = $appendsql . " tb1.`posted_id` = '" . $var[$x] . "' or ";
			}
		}
	}

$select = " select tb2.`pin`,'', tb2.`lname`, tb2.`fname`,'' as ccc, tb2.`mname`, sum(tb1.`pi`) as aaa, '100' as bbb, sum(tb1.`pi`)+100,''
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) where " . $appendsql . " group by `em_id`  order by lname asc, fname asc, mname asc";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$excel->writeLine($row);
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=pagibig.xls');
header("Location: pagibig.xls");
exit;
?>
