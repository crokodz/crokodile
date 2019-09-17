
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("deductions.xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
//$column_header = array('ACCOUNT SUMMARY - PAYROLL');
$column_header = array('FOR THE MONTH OF' . $_GET['payday']);

//~ function getsss($salary){
//~ 	$select = "select sser, ec from sss where `ssee` >= '" . $salary . "' order by `ssee` asc limit 1";
//~ 	$result = mysql_query($select, connect());
//~ 	$row = mysql_fetch_array($result,MYSQL_ASSOC);
//~ 	return array($row['sser'],$row['ec']);
//~ 	}
//~
function getsss($salary,$cnfsss){
	if($cnfsss == 'YES'){
		$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
		}
	else{
		return array('0','0','0','0');
		}
	}

function getph($salary,$cnfph){
	if($cnfph == 'YES'){
		$select = "select `id`,`ees`,`ers` from ph where `from` <= '" . $salary . "' order by `from` desc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ees'],$row['ers']);
		}
	else{
		return array('0','0','0');
		}
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


$excel->writeLine($column_header);
$company_id = 1;
$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');

$company_id = $_GET['compa'];
$company_name = $company_array[$company_id];

$excel->writeLine(array('MANAGEMENT'));


$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

$header = array('ID', 'FIRST NAME', 'LAST NAME');
$selectx = "select * from deductions";
$resultx = mysql_query($selectx, connect());
while($rowx = mysql_fetch_array($resultx)){
	array_push($header, $rowx['name']);
	array_push($header, 'Balance');
}
array_push($header, 'TOTAL');

$excel->writeLine($header);

$select = "select t1.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary,
	group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " group by t1.em_id";
$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$selectx = "select * from deductions";
	$resultx = mysql_query($selectx, connect());
	$body = array($row['em_id'], $row['fname'], $row['lname']);
	$total = 0;
	while($rowx = mysql_fetch_array($resultx)){
		$amount  = 0;
		$select1 = "select amount from employee_deduction where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
		$result1 = mysql_query($select1, connect());
		while($row1 = mysql_fetch_array($result1)){
			$amount  = $amount + $row1['amount'];
		}


		$amountb  = 0;
		$select1b = "select amount from employee_deduction where name = '" . $rowx['name'] . "' and status = 'pending' and em_id = '" . $row['em_id'] . "'";
		$result1b = mysql_query($select1b, connect());
		while($row1b = mysql_fetch_array($result1b)){
			$amountb  = $amountb + $row1b['amount'];
		}


		array_push($body, $amount);
		array_push($body, $amountb);
		$total = $total + $amount;
	}
	if ($total > 0){
		array_push($body, $total);
		$excel->writeLine($body);
	}
}

header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=deductions.xls');
header("Location: deductions.xls");
exit;

?>
