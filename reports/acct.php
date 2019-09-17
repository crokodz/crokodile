
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("acctn.xls");

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

$column_header = array('Company', 'Division', 'Paycode','Last Name', 'First Name','Middle Name', 'Basic Salary', 'Salary Based', 'Absent/halfday', 'Late','UnderTime','OverTime','Other Income','Total Earnings','Total Employee SSS','Total Employee MCR','Total Employee PagIbig','Gross Taxable','Tax','Gross After Tax','Non Taxable','Deduction', 'Net Income','Total Employer SSS','Total Employer EC','Total Employer Philhealth','Total Employer PagIbig','Bank');
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
$select = " select tb3.`name`, tb2.`division`, tb2.`pay_id`,tb2.`lname`,tb2.`fname`, tb2.`mname`, tb1.`salary`, tb2.`salary_based`, sum(tb1.`absent`), sum(tb1.`late`),
	sum(tb1.`ut`), sum(tb1.`ot`), sum(tb1.`other_tax_inc`), sum(tb1.`taxable_salary`), sum(tb1.`sss`), sum(tb1.`ph`), sum(tb1.`pi`),
	sum(tb1.`taxable_salary`), sum(tb1.`tax`), sum(tb1.`nontax`), sum(tb1.`netpay`), sum(tb1.`deduction`), tb2.bank_account
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) left join company tb3 on(tb1.`company_id` = tb3.`id`) where " . $appendsql . "  and tb2.`status` != 'deleted'  group by `em_id`  order by tb3.`name` asc, tb2.`division` asc, mname asc";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$sss = getsss($row[14]);
	$ph = getph($row[15]);
	
	
	
	$excel->writeLine(array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],roundoff($row[6]),$row[7],roundoff($row[8]),roundoff($row[9]),
		roundoff($row[10]),roundoff($row[11]),roundoff($row[12]),roundoff($row[13]),roundoff($row[14]),roundoff($row[15]),roundoff($row[16]),roundoff($row[17]),
		roundoff($row[18]), roundoff($row[17] - $row[18]), roundoff($row[19]), roundoff($row[21]),roundoff($row[20]), roundoff($sss[0]), roundoff($sss[1]),roundoff($ph[1]),roundoff($row[16]), str($row[22])
		));
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=acctn.xls');
header("Location: acctn.xls");
exit;

?>