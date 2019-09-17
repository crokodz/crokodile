
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("acctn.xls");

function getsss($salary){
	$select = "select sser, ec from sss where `ssee` <= '" . $salary . "' order by `ssee` desc limit 1";
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
	
function getEC($y,$pid,$em_id){
	if($y==1){
		$select = "select `payday`, `sss` from `posted_summary` where `posted_id` = '" . $pid . "' and `em_id` = '" . $em_id . "'";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$s = explode("@",$row['payday']);
		$ec = getsss($row['sss']);
				
		if($s[0] == 'w1'){
			return array($ec[0],$ec[1]);
			}
		else{
			$pastpayid = 'w1@' . $s[1];
			$select1 = "select `payday`, `sss` from `posted_summary` where `payday` = '" . $pastpayid . "' and `em_id` = '" . $em_id . "'";
			$result1 = mysql_query($select1, connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
			$ec1 = getsss($row1['sss']+$row['sss']);
			$ec2 = getsss($row1['sss']);
			return array($ec1[0] - $ec2[0], $ec1[1] - $ec2[1]);
			}
		}
	}

$column_header = array('Payroll No.','Day','Company', 'Division', 'Paycode','Last Name', 'First Name','Middle Name', 'Basic Salary', 'Salary Based', 'Absent/halfday', 'Late','UnderTime','OverTime','Other Income','Total Earnings','Total Employee SSS','Total Employee MCR','Total Employee PagIbig','Gross Taxable','Tax','Gross After Tax','Non Taxable','Deduction', 'Net Income','Total Employer SSS','Total Employer EC','Total Employer Philhealth','Total Employer PagIbig');
$excel->writeLine($column_header);

$select = " select tb3.`name`, tb2.`division`, tb2.`pay_id`,tb2.`lname`,tb2.`fname`, tb2.`mname`, tb1.`salary`, tb2.`salary_based`, tb1.`absent`, tb1.`late`,
	tb1.`ut`, tb1.`ot`, tb1.`other_tax_inc`, tb1.`taxable_salary`, tb1.`sss`, tb1.`ph`, tb1.`pi`,
	tb1.`taxable_salary`, tb1.`tax`, tb1.`nontax`, tb1.`netpay`, tb1.`deduction`, tb1.`em_id`,tb1.`title`, tb1.`posted_id`
	from `posted_summary` tb1 left join employee tb2 using(`em_id`) left join company tb3 on(tb1.`company_id` = tb3.`id`) where tb1.em_id = '" . $_GET['emid'] . "' and tb1.`from` >= '" . $_GET['fdate'] . "' and tb1.`to` <= '" . $_GET['tdate'] . "' order by tb1.`to` asc";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$sssx = getsss($row[14]);
	$ph = getph($row[15]);
	$ecx = getEC($y,$pid,$row[22]);
	
	if($y == 1){
		$ec = $ecx[1];
		$sss = $ecx[0];
		}
	else{
		$ec = $sssx[1];
		$sss = $sssx[0];
		}
	
	$excel->writeLine(array($row[24],$row[23],$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],roundoff($row[6]),$row[7],roundoff($row[8]),roundoff($row[9]),
		roundoff($row[10]),roundoff($row[11]),roundoff($row[12]),roundoff($row[13]),roundoff($row[14]),roundoff($row[15]),roundoff($row[16]),roundoff($row[17]),
		roundoff($row[18]), roundoff($row[17] - $row[18]), roundoff($row[19]), roundoff($row[21]),roundoff($row[20]), roundoff($sss), roundoff($ec),roundoff($ph[1]),roundoff($row[16])
		));
	}


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=acctn.xls');
header("Location: acctn.xls");
exit;

?>