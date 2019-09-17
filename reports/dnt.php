
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("dnt.xls");

function getsss($salary){
	$select = "select sser, ec from sss where `ssee` = '" . $salary . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['sser'],$row['ec']);
	}

function getph($salary){
	$select = " select `msb`, `ers`  from `ph` where `ees` =  '" . $salary . "' ";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['msb'],$row['ers']);
	}

$column_header = array('DEDUCTION');
$column_header = array('Payroll #', 'Employee ID','Last Name', 'First Name','Middle Name','Type','Date','Amount','Salary Based','Pay Code', 'Balance');
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
$select = " select tb1.`posted_id`, tb1.`em_id`, tb2.`lname`,tb2.`fname`, tb2.`mname`, tb1.`name`, tb1.`date`,
	tb1.`amount`, tb2.`salary_based`, tb2.`pay_id`, (select sum(tb3.amount) from employee_deduction tb3 where tb3.sub_id = tb1.sub_id and status = 'pending') as balance
	from `employee_deduction` tb1 left join employee tb2 using(`em_id`)
	where " . $appendsql . "
	order by tb2.`lname` asc, tb2.`fname` asc";

$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$excel->writeLine(array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10]));
	}


$column_header = array('OTHER TAXABLE INCOME');
$excel->writeLine($column_header);

$select = " select tb1.`posted_id`, tb1.`em_id`, tb2.`lname`,tb2.`fname`, tb2.`mname`, tb1.`name`, tb1.`datetime`,
	tb1.`amount`, tb2.`salary_based`, tb2.`pay_id`
	from `employee_taxable` tb1 left join employee tb2 using(`em_id`)
	where " . $appendsql . "
	order by tb2.`lname` asc, tb2.`fname` asc";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$excel->writeLine(array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]));
	}

$column_header = array('OTHER NON-TAXABLE INCOME');
$excel->writeLine($column_header);

$select = " select tb1.`posted_id`, tb1.`em_id`, tb2.`lname`,tb2.`fname`, tb2.`mname`, tb1.`name`, tb1.`datetime`,
	tb1.`amount`, tb2.`salary_based`, tb2.`pay_id`
	from `employee_non_taxable` tb1 left join employee tb2 using(`em_id`)
	where " . $appendsql . "
	order by tb2.`lname` asc, tb2.`fname` asc";

$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$excel->writeLine(array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]));
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=dnt.xls');
header("Location: dnt.xls");
exit;

?>
