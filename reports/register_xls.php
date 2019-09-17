
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("pay_register.xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
$excel->writeLine($column_header);

$w1 = str_replace("w1", "First Payday of ", $_GET['payday']);
if($w1 == $_GET['payday']){
	$w1 = str_replace("w2", "Second Payday of ", $_GET['payday']);
}

$company_id = 1;
$company_name = 'RMN INC';
if($_SESSION['user'] == 'raz'){
	$company_id = 7;
	$company_name = 'IBMI';
}

if($_SESSION['user'] == 'love'){
	$company_id = 1;
	$company_name = 'RMN INC';
}

if($_SESSION['user'] == 'mae'){
	$company_id = 6;
	$company_name = 'MANAGEMENT';
}

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

function getpi($salary,$cnfpi,$pdm){
	$pdm = $pdm * 2;
	if($cnfpi == 'YES'){
		if ($salary <= 1500){
			return $salary * .02;
			}
		else{
			if ($pdm > 0){
				return array($pdm+100,100);
			} else {
				return array(0,0);
			}
			
			}
		}
	else{
		if ($pdm > 0){
			return array($pdm,100);
		} else {
			return array(0,0);
		}
	}
}


$column_header = array($company_name . ' - Payroll Register for the period ' . $w1);
$excel->writeLine($column_header);

$column_header = array('');
$excel->writeLine($column_header);

$column_header = array('Network Totals');
$excel->writeLine($column_header);

$column_header = array('');
$excel->writeLine($column_header);




$select = "select
salary as basic,
nontax as nontax,
other_tax_inc as other_tax_inc,
deduction as deduction,
tax as tax,
sss as sss,
pi as pi,
ph as ph,
netpay as netpay,
ot as ot,
ut + late as adjustment
from posted_summary where payday = '" . $_GET['payday'] . "' and company_id = " . $company_id;
$result = mysql_query($select, connect());

$basic = 0;
$nontax = 0;
$talent = 0;
$ot = 0;
$other = 0; 
$adjustment = 0;
$tax = 0;
$sss = 0;
$ph = 0;
$pi = 0;
$gross = 0;

while($row = mysql_fetch_array($result)){
	
	if ($row['basic'] > 0){
		$basic = $basic + $row['basic'];
		$other = $other + $row['other_tax_inc'];
	} else {
		$talent = $talent + $row['other_tax_inc'];
	}

	$nontax = $nontax + $row['nontax'];
	$ot = $ot + $row['ot'];
	$adjustment = $adjustment + $row['adjustment'];
	$tax = $tax + $row['tax'];
	$sss = $sss + $row['sss'];
	$ph = $ph + $row['ph'];
	$pi = $pi + $row['pi'];

	$netpay = $netpay + $row['netpay'];

	// $grs = $row['basic'] + $row['talent'] + $row['ot'] - $row['adjustment'];
	// $gross  = $gross + $grs;

	//----

	// $cnfph = 'NO';
	// if($row['ph'] > 0){
	// 	$cnfph = 'YES';
	// }
	// $ph = getph($row['salary'],$cnfph);
	
	// $cnfpi = 'NO';
	// if($row['pi'] > 0){
	// 	$cnfpi = 'YES';
	// }
	// $pi = getpi($row['salary'],$cnfpi,$row['pdm']);
	// $pi_employee = $pi[0];
	// $pi_employer = $pi[1];
	
	// $cnfsss = 'NO';
	// if($row['sss'] > 0){
	// 	$cnfsss = 'YES';
	// }

	// $salary = $row['taxable_salary'];
	// if($row['salary'] > 0){
	// 	$salary  = $row['salary'];
	// }

	// $sss = getsss($salary,$cnfsss);
	// $d = $sss[1] - $row['sss'];
	// if($d < 0){
	// 	$sss[1] = $row['sss'];
	// }
	// $sss_employee = $sss[1];
	// $sss_employer = $sss[2];
	// $ec = $sss[3]; 

	// $sss = $sss + $row['sss'];
	// $ph = $ph + $row['ph'];
	// $pi = $pi + $row['pi'];
}

$gross  = $basic + $nontax + $talent + ($ot - $adjustment) + $other;

$excel->writeLine(array('BASIC', $basic));
$excel->writeLine(array('COLA', $nontax));
$excel->writeLine(array('TALENT FEE', $talent));
$excel->writeLine(array('OVERTIME PAY', $ot));
$excel->writeLine(array('OTHER EARNINGS', $other));
$excel->writeLine(array('ADJUSTMENT ON EARNINGS', $adjustment));
$excel->writeLine(array('GROSS',  $gross));
$excel->writeLine(array('WITHHOLDING TAX',  $tax));
$excel->writeLine(array('SSS CONTRIBUTION',  $sss));
$excel->writeLine(array('MEDICARE',  $ph));
$excel->writeLine(array('PAGIBIG CONTRIBUTION',  $pi));
$excel->writeLine(array('', ''));

$select1 = "select distinct posted_id from posted_summary where payday = '" . $_GET['payday'] . "' and company_id = " . $company_id;
$result1 = mysql_query($select1, connect());
$where = " where ";
while($row1 = mysql_fetch_array($result1)){
	$where .= "posted_id = " . $row1['posted_id'] . ' or ';

}

$select2 = "select name, sum(amount) as amount from employee_deduction " . substr($where, 0, -3) . ' group by name';
$result2 = mysql_query($select2, connect());
$ded_total = 0;
while($row2 = mysql_fetch_array($result2)){
	$excel->writeLine(array($row2['name'], $row2['amount']));
	$ded_total = $ded_total + $row2['amount'];
}


$excel->writeLine(array('TOTAL DEDUCTION', $ded_total));
$excel->writeLine(array('', ''));
$excel->writeLine(array('NET PAY', $netpay));



header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=pay_register.xls');
header("Location: pay_register.xls");
exit;

?>
