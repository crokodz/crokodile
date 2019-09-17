
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("pay_register_details.xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
$column_header = array('FOR THE MONTH OF' . $_GET['payday']);




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
$post_type = urldecode($_GET['type']);
$company_name = $company_array[$company_id];

$excel->writeLine(array('MANAGEMENT'));


$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

$header = array(
			'Payroll #',
			'Paycode',
			'Id',
			'First Name',
			'Last Name',
			'Division',
			'File Status',
			'Basic Salary',
			'Absent/halfday',
			'Late',
			'UnderTime',
			'OverTime',
			'Other Income',
			'Total Employee SSS',
			'Total Employee MCR',
			'Total Employee PagIbig',
			'Gross Taxable',
			'Tax',
			'Non Taxable',
			'Deduction',
			'Net Income',
			'Bank Account'
);
$selectx = "select * from deductions";
$resultx = mysql_query($selectx, connect());
while($rowx = mysql_fetch_array($resultx)){
	array_push($header, $rowx['name']);
}
array_push($header, 'TOTAL Deduction');

$selectx = "select * from nontaxable_entry";
$resultx = mysql_query($selectx, connect());
while($rowx = mysql_fetch_array($resultx)){
	array_push($header, $rowx['name']);
}
array_push($header, 'TOTAL Non Taxable');

$selectx = "select * from taxable_entry";
$resultx = mysql_query($selectx, connect());
while($rowx = mysql_fetch_array($resultx)){
	array_push($header, $rowx['name']);
}
array_push($header, 'TOTAL Taxable');

$excel->writeLine($header);

//die($_GET['id']);
if($_GET['id'] == 0){

$select = "select
		t1.pay_id,
		t2.fname,
		t2.mname,
		t2.lname,
		t1.em_id,
		t2.file_status,
		sum(t1.sss) as sss,
		sum(t1.taxable_salary) as taxable_salary,
		group_concat(posted_id) as posted_id,
		sum(t1.ph) as ph,
		sum(t1.salary) as salary,
		sum(t1.`absent`) as absent,
		t2.division,
		sum(t1.`late`) as late,
		sum(t1.`ut`)as ut,
		sum(t1.`ot`) as ot,
		sum(t1.`other_tax_inc`) as other_taxable,
		sum(t1.`taxable_salary`) as taxable,
		sum(t1.`sss`) as sss,
		sum(t1.`ph`)as ph,
		sum(t1.`pi`) as pi,
		sum(t1.`tax`) as tax,
		sum(t1.`nontax`) as non,
		sum(t1.`netpay`) as netpay,
		sum(t1.`deduction`) as deduction,
		t2.`bank_account`
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and post_type = '" . $post_type . "' group by t1.em_id";
} else {

	$yearc = explode("-", $_GET['payday']);

	$select = "select
		concat(t1.pay_id, '-', post_type) as pay_id,
		t2.fname,
		t2.mname,
		t2.lname,
		t1.em_id,
		t2.file_status,
		sum(t1.sss) as sss,
		sum(t1.taxable_salary) as taxable_salary,
		group_concat(posted_id) as posted_id,
		sum(t1.ph) as ph,
		sum(t1.salary) as salary,
		sum(t1.`absent`) as absent,
		t2.division,
		sum(t1.`late`) as late,
		sum(t1.`ut`)as ut,
		sum(t1.`ot`) as ot,
		sum(t1.`other_tax_inc`) as other_taxable,
		sum(t1.`taxable_salary`) as taxable,
		sum(t1.`sss`) as sss,
		sum(t1.`ph`)as ph,
		sum(t1.`pi`) as pi,
		sum(t1.`tax`) as tax,
		sum(t1.`nontax`) as non,
		sum(t1.`netpay`) as netpay,
		sum(t1.`deduction`) as deduction,
		t2.`bank_account`
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $yearc[0] . "%' and t1.em_id = '" . $_GET['id'] . "' group by t1.posted_id";
}

$result = mysql_query($select, connect());
while($row = mysql_fetch_array($result)){
	$selectx = "select * from deductions";
	$resultx = mysql_query($selectx, connect());
	$body = array(
				$row['posted_id'],
				$row['pay_id'],
				$row['em_id'],
				$row['fname'],
				$row['lname'],
				$row['division'],
				$row['file_status'],
				$row['salary'],
				$row['absent'],
				$row['late'],
				$row['ut'],
				$row['ot'],
				$row['other_taxable'],
				$row['sss'],
				$row['ph'],
				$row['pi'],
				$row['taxable'],
				$row['tax'],
				$row['non'],
				$row['deduction'],
				$row['netpay'],
				$row['bank_account']
			);
	// print_r($body);
	// echo '<br>';

	if($post_type == 'REGULAR'){
		$total = 0;
		while($rowx = mysql_fetch_array($resultx)){
			$amount  = 0;
			$select1 = "select amount from employee_deduction where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
			$result1 = mysql_query($select1, connect());
			while($row1 = mysql_fetch_array($result1)){
				$amount  = $amount + $row1['amount'];
			}
			array_push($body, $amount);
			$total = $total + $amount;
		}
		#if ($total > 0){
			array_push($body, $total);
			//$excel->writeLine($body);

		#}


		//
		$total = 0;
		$selectx = "select * from nontaxable_entry";
		$resultx = mysql_query($selectx, connect());
		while($rowx = mysql_fetch_array($resultx)){
			$amount  = 0;
			$select1 = "select amount from employee_non_taxable where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
			$result1 = mysql_query($select1, connect());
			while($row1 = mysql_fetch_array($result1)){
				$amount  = $amount + $row1['amount'];
			}
			array_push($body, $amount);
			$total = $total + $amount;
		}
		#if ($total > 0){
			array_push($body, $total);
			//$excel->writeLine($body);

		#}

		//
		$total = 0;
		$selectx = "select * from taxable_entry";
		$resultx = mysql_query($selectx, connect());
		while($rowx = mysql_fetch_array($resultx)){
			$amount  = 0;
			$select1 = "select amount from employee_taxable where name = '" . $rowx['name'] . "' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
			$result1 = mysql_query($select1, connect());
			while($row1 = mysql_fetch_array($result1)){
				$amount  = $amount + $row1['amount'];
			}
			array_push($body, $amount);
			$total = $total + $amount;
		}
	}
		#if ($total > 0){
			array_push($body, $total);
			$excel->writeLine($body);

		#}
}



header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=pay_register_details.xls');
header("Location: pay_register_details.xls");
exit;

?>
