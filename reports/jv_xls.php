
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter("jv_" . $ymd . ".xls");


$column_header1 = array('<b>RADIO MINDANAO NETWORK INC.</b>');
$column_header2 = array('<b>ACCOUNT SUMMARY - PAYROLL</b>');
$column_header = array('<b>FOR THE MONTH OF' . $_GET['payday'].'</b>');

function getsss($salary,$cnfsss){
	$s = explode("@",$_GET['payday']);

	if(count($s) > 1){
		$pays = str_replace('-', '', $s[1]);
	} else {
		$pays = str_replace('-', '', $s[0]);
	}



	$tb = 'sss_04_2019';
	if($pays+0 > 201903){
		$tb = 'sss';
	}

	$select = "select id, ssee, sser, ec from " . $tb . " where `ssee` >= '" . $salary . "' order by `ssee` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	if($salary > 0){
		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
		}
	else{
		return array('0','0','0','0');
		}

	}

// function getsss($salary,$cnfsss){
// 	if($cnfsss == 'YES'){
// 		$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
// 		$result = mysql_query($select, connect());
// 		$row = mysql_fetch_array($result,MYSQL_ASSOC);
// 		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
// 		}
// 	else{
// 		return array('0','0','0','0');
// 		}
// 	}

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
			return array($pdm+100,100);
			}
		else{
			if ($pdm > 0){
				return array($pdm+100,100);
			} else {
				return array(100,100);
			}

			}
		}
	else{
		if ($pdm > 0){
			return array(0,0);
		} else {
			return array(0,0);
		}
	}
}

$excel->writeLine($column_header1);
$excel->writeLine($column_header);
$company_id = 1;
$company_name = 'RMN INC';



$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');
$company_id = $_GET['compa'];
$company_name = $company_array[$company_id];
$excel->writeLine(array($company_name));

$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

$excel->writeLine(array('<b>ACCOUNT</b>', '', '<b>SL</b>','<b>AMOUNT</b>',''));
$excel->writeLine(array('<b>CODE</b>', '<b>ACCOUNT NAME</b>', '<b>DR(CR)</b>','<b>DEBIT</b>','<b>CREDIT</b>'));

// $select = "select pay_id,
// 	sum(netpay-(sss+ph+pi+deduction+(ot - (ut+late)))) as netpay,
// 	sum(ot - (ut+late)) as ot,
// 	sum(sss) as sss,
// 	sum(ph) as ph,
// 	sum(pi) as pi,
// 	sum(tax) as tax,
// 	sum(deduction) as deduction,
// 	sum(taxable_salary) as taxable_salary,
// 	sum(nontax) as nontax,
// 	group_concat(posted_id) as posted_id
// 	from posted_summary where payday like '%" . $_GET['payday'] . "' and company_id = " . $company_id . " group by pay_id";


$talentfee = 0;

$select = "select
 	t2.pay_id,
 	sum(netpay-(t1.sss+t1.ph+t1.pi+t1.deduction+(t1.ot - (t1.ut+t1.late)))) as netpay,
	sum(t1.ot - (t1.ut+t1.late)) as ot,
	sum(t1.sss) as sss,
	sum(t1.ph) as ph,
	sum(t1.pi) as pi,
	sum(t1.tax) as tax,
	sum(t1.deduction) as deduction,
	sum(t1.taxable_salary) as taxable_salary,
	sum(t1.nontax) as nontax,
	group_concat(DISTINCT(t1.posted_id) SEPARATOR ',') as posted_id
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t2.pay_id";

if($company_id == 6 || $company_id == 1){
	$select = "select
 	t2.pay_id,
 	t1.department,
 	sum(netpay-(t1.sss+t1.ph+t1.pi+t1.deduction+(t1.ot - (t1.ut+t1.late)))) as netpay,
	sum(t1.ot - (t1.ut+t1.late)) as ot,
	sum(t1.sss) as sss,
	sum(t1.ph) as ph,
	sum(t1.pi) as pi,
	sum(t1.tax) as tax,
	sum(t1.deduction) as deduction,
	sum(t1.taxable_salary) as taxable_salary,
	sum(t1.nontax) as nontax,
	group_concat(DISTINCT(t1.posted_id) SEPARATOR ',') as posted_id
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t1.department";
}

$result = mysql_query($select, connect());

$ssstx = 0;
$phtx = 0;
$pitx = 0;
$taxx = 0;
$totalx = 0;
$basictax = 0;
$talenttax = 0;
$ded = 0;
$totalx1 = 0;

while($row = mysql_fetch_array($result)){
	$ssst = 0;
	$pht = 0;
	$pit = 0;
	$dwopt = 0;
	$nontaxt = 0;
	$taxt = 0;

	// echo $select1 = "select sss, tax, pi, ph, taxable_salary, salary, pdm from posted_summary where payday like '%" . $_GET['payday'] . "' and company_id = " . $company_id . " and pay_id = '" . $row['pay_id'] . "'";
  $select1 = "select t2.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary, sum(tax) as tax, sum(t1.salary) as salary, sum(t1.nontax) as nontax,
	group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary, t2.pdm, sum(t1.pi) as pi
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and t2.pay_id = '" . $row['pay_id']  . "'  group by t1.em_id";
if($company_id == 6 || $company_id == 1){
   	$select1 = "select t2.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary, sum(tax) as tax, sum(t1.salary) as salary, sum(t1.nontax) as nontax,
	group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary, t2.pdm, sum(t1.pi) as pi
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " and t1.department = '" . $row['department']  . "'  group by t1.em_id";
}
	$result1 = mysql_query($select1, connect());
	while($row1 = mysql_fetch_array($result1)){
		$salary = $row1['taxable_salary'];
		$em_talents = ['A01-313', 'A01-315', 'A01-314'];
		if($row1['salary'] > 0 && !in_array($row1['em_id'], $em_talents) ){
			$basictax = $basictax + $row1['tax'];
			$salary  = $row1['salary'];
		} else {
			$talenttax = $talenttax + $row1['tax'];
			$talentfee = $talentfee + $row1['nontax'] + $row1['taxable_salary'];
		}


		$cnfph = 'NO';
		if($row1['ph'] > 0){
			$cnfph = 'YES';
		}

		$ph = getph($salary,$cnfph);

		if($row1['ph'] != $ph[2]){
			$ph[1] = $row['ph'];
			$ph[2] = $row['ph'];
		}

		$pht =  $pht + $row1['ph'];

		// $cnfpi = 'NO';
		// if($row1['pi'] > 0){
		// 	$cnfpi = 'YES';
		// }
		// $pi = getpi($salary,$cnfpi,$row1['pdm']);
		// $pi_employee = $pi[0];
		// $pi_employer = $pi[1];

		// $pit = $pit + $pi_employer;
		// if($row1['pi'] > 100){
		// 	$pit = $pit + 100;
		// }

		$cnfpi = 'NO';
		if($row1['pi'] > 0){
			$cnfpi = 'YES';
		}
		$pi = getpi($salary,$cnfpi,$row1['pdm']);
		$pi_employee = $pi[0];
		$pi_employer = $pi[1];

		$pit = $pit + ($pi_employer);


		$cnfsss = 'NO';
		if($row1['sss'] > 0){
			$cnfsss = 'YES';
			// echo $select1;
			// die();
		}



		// $salary = $row1['taxable_salary'];
		// if($row1['salary'] > 0){
		// 	$basictax = $basictax + $row1['tax'];
		// 	$salary  = $row1['salary'];
		// } else {
		// 	$talenttax = $talenttax + $row1['tax'];
		// }

		// $sss = getsss($salary,$cnfsss);
		// // $d = $sss[1] - $row1['sss'];
		// // if($d < 0){
		// // 	$sss[1] = $row['sss'];
		// // }
		// $sss_employee = $sss[1];
		// $sss_employer = $sss[2];
		// $ec = $sss[3];


		$sss = getsss($row1['sss'],$cnfsss);
		$d = $sss[1] - $row1['sss'];
		if($d < 0){
			$sss[1] = $row1['sss'];
		}
		$sss_employee = $sss[1];
		$sss_employer = $sss[2];
		$ec = $sss[3];



		//$sss = getsss($row1['sss']);
		$ssst = $ssst + $sss_employer + $ec;
		$taxx = $tax + $row['tax'];


		$select1x = "select sum(amount) as amount, name from employee_deduction where status = 'posted' and posted_id in (" . $row['posted_id'] . ") and name = 'DWOP' and em_id = '" . $row1['em_id'] . "' group by name";
		$result1x = mysql_query($select1x, connect());
		$row1x = mysql_fetch_array($result1x);
		$dwop = $row1x['amount'];
		$dwopt = $dwopt + $dwop;
	}

	$nontaxtx = 0;
	$taxtx = 0;
	$total_row = 0;


	if($company_id == 6 || $company_id == 1){
		$select2x = "select  sum(t1.amount) as amount, t1.name from employee_non_taxable t1 left join posted_summary t2 on (t1.em_id = t2.em_id and t1.posted_id = t2.posted_id) where t2.department = '" . $row['department']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
		$result2x = mysql_query($select2x, connect());
		while($row2x = mysql_fetch_array($result2x)){
			$excel->writeLine(array('', $row2x['name'] , '',roundoff($row2x['amount']),''));
			$nontax2x = $row2x['amount'];
			$nontaxt = $nontaxt + $nontax2x;
			$nontaxtx = $nontaxtx + $nontax2x;
			$total_row = $total_row + $row2x['amount'];
		}
	} else {
		$select2x = "select  sum(t1.amount) as amount, t1.name from employee_non_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where t2.pay_id = '" . $row['pay_id']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
		$result2x = mysql_query($select2x, connect());
		while($row2x = mysql_fetch_array($result2x)){
			// if($row2x['name'] == 'TAX REFUND'){
			// 	$excel->writeLine(array('', $row2x['name'] , '','','','',roundoff($row2x['amount']),''));
			// 	$taxref = $taxref + $row2x['amount'];
			// } else {
				$excel->writeLine(array('', $row2x['name'] , '',roundoff($row2x['amount']),''));
				$nontax2x = $row2x['amount'];
				$nontaxt = $nontaxt + $nontax2x;
				$nontaxtx = $nontaxtx + $nontax2x;
				$total_row = $total_row + $row2x['amount'];
			// }
		}
	}

	$select2x = "select  sum(t1.amount) as amount, t1.name from employee_taxable t1 left join posted_summary t2 on (t1.em_id = t2.em_id and t1.posted_id = t2.posted_id) where t2.department = '" . $row['department']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
	$result2x = mysql_query($select2x, connect());
	while($row2x = mysql_fetch_array($result2x)){
		$excel->writeLine(array('', $row2x['name'] , '',roundoff($row2x['amount']),''));
		$tax2x = $row2x['amount'];
		$taxt = $taxt + $tax2x;
		$taxtx = $taxtx + $tax2x;
		$total_row = $total_row + $row2x['amount'];
	}

	//$row['nontax'] = 0;



	$ssstx = $ssstx + ($row['sss']+$ssst);
	$phtx = $phtx + ($row['ph']+$pht);
	$pitx = $pitx + ($row['pi']+$pit);

	//$total = $row['netpay'] + $row['ot'] + $row['sss'] + $row['ph'] + $row['pi']+$ssst+$pit+$pht+$row['tax'];
	$total = $ssst+$pit+$pht+(($row['taxable_salary'] + $row['nontax']));
	$total1 = (($row['taxable_salary'] + $row['nontax'])) - ($row['ph']+$row['pi']+$row['sss'] + $row['deduction']+$row['tax']);


	$selectc = "select id from pay where name = '" . $row['pay_id'] . "' limit 1";
	$resultc = mysql_query($selectc, connect());
	$rowc = mysql_fetch_array($resultc,MYSQL_ASSOC);
	$idc = $rowc['id'];

	$totalx = $totalx + $total1;
	$excel->writeLine(array($idc.'-3101', 'SALARIES AND WAGES' , '',roundoff(($row['taxable_salary'] - $taxtx) - ($row['ot']+$dwopt)),''));
	$excel->writeLine(array($idc.'-3103', 'OVERTIME/LATE/UDERTIME' , '',roundoff($row['ot']),''));
	$excel->writeLine(array($idc.'-3104', 'SSS AND ECC CONTRIBUTIONS' , '',roundoff($ssst)));
	$excel->writeLine(array($idc.'-3105', 'MEDICARE CONTRIBUTIONS' , '',roundoff($pht)));
	$excel->writeLine(array($idc.'-3106', 'PAGIBIG CONTRIBUTIONS' , '',roundoff($pit)));
	$total_row = $total_row + (($row['taxable_salary'] - $taxtx) - ($row['ot']+$dwopt)) + $row['ot'] + $ssst + $pht + $pit;

	if($total > $total_row){
		$excel->writeLine(array($idc.'', '13Th Month' , '',roundoff($total - $total_row)));
	}

	// if($company_id == 6){

	// 	$select2x = "select  sum(t1.amount) as amount, t1.name from employee_non_taxable t1 left join employee t2 on (t1.em_id = t2.em_id) where t2.department = '" . $row['department']  . "' and  t1.posted_id in (" . $row['posted_id'] . ") group by t1.name";
	// 	$result2x = mysql_query($select2x, connect());
	// 	while($row2x = mysql_fetch_array($result2x)){
	// 		$excel->writeLine(array('', $row2x['name'] , '',roundoff($row2x['amount']),''));
	// 		$nontax2x = $row2x['amount'];
	// 		$nontaxt = $nontaxt + $nontax2x;
	// 	}
	// }



	if($company_id == 6 || $company_id == 1){
		if(trim($row['department']) == ''){
			$row['department'] = '--NO DEPARTMENT--';
		}
		$excel->writeLine(array('', '<b>'.$row['department'].'</b>', roundoff($total) ,'',''));
		$totalx1 = $totalx1 + $total;
	} else {
		$excel->writeLine(array('', '<b>'.$row['pay_id'] . '</b>', roundoff($total) ,'',''));
		$totalx1 = $totalx1 + $total;
	}
	$excel->writeLine(array('','','','',''));
}

$excel->writeLine(array('','','','',''));

$select = "select posted_id
	from posted_summary where post_type='" . $_GET['type'] . "' AND  payday like '%" . $_GET['payday'] . "' and company_id = " . $company_id . " group by posted_id";
$result = mysql_query($select, connect());
$sssln = 0;
$piln = 0;

$posted_id = '';

while($row = mysql_fetch_array($result)){
	$posted_id = $posted_id . $row['posted_id'] . ', ';
}

$posted_id = substr($posted_id, 0, -2);

$select1 = "select sum(amount) as amount, name from employee_deduction where status = 'posted' and posted_id in (" . $posted_id . ") and name != 'DWOP' group by name";
$result1 = mysql_query($select1, connect());
$ttl1 = 0;
while($row1 = mysql_fetch_array($result1)){
	//$sssln = $sssln + $row1['amount'];
	$excel->writeLine(array(substr($row1['name'], 0, 3) . '-01', $row1['name'] , '','',roundoff($row1['amount'])));
	$ttl1 = $ttl1 + $row1['amount'];
}


$totalx2 = $pitx + $ssstx + $phtx + $talenttax + $basictax + $totalx + $ttl1;


$xxss = $totalx1 - $totalx2;
$totalx = $totalx + $xxss;


$excel->writeLine(array('-1430', 'ACCRUED SALARIES AND WAGES' , '','',roundoff($totalx)));
$excel->writeLine(array('-1433', 'EMPLOYEES W/HOLDING TAXES PAYABLE' , '','',roundoff($basictax)));
$excel->writeLine(array('-1434', 'EXPANDED TAXES PAYABLE' , '','',roundoff($talenttax)));
$excel->writeLine(array('1436', 'PHILHEALTH CONTRIBUTIONS PAYABLE' , '','',roundoff($phtx)));
$excel->writeLine(array('-1436', 'SSS CONTRIBUTIONS PAYABLE' , '','',roundoff($ssstx)));
$excel->writeLine(array('-1432', 'PAGIBIG CONTRIBUTION PAYABLE' , '','',roundoff($pitx)));



$totalx2 = $pitx + $ssstx + $phtx + $talenttax + $basictax + $totalx + $ttl1;



$excel->writeLine(array('', 'TOTAL' , '',ronc($totalx1, 1),ronc($totalx2, 1)));

// $excel->writeLine(array('-1082', 'ADVANCES TO OFFICERS AND EMPLOYEES CALAMITY LOAN' , '','',''));
// $excel->writeLine(array('-1423', 'ACCOUNTS PAYABLE - RMNECA' , '','',''));
// $excel->writeLine(array('-1425', 'PAGIBIG LOAN PAYABLE' , '','',roundoff($piln)));
// $excel->writeLine(array('-1429', 'SSS LOAN PAYABLE' , '','',roundoff($sssln)));
// $excel->writeLine(array('-1430', 'ACCRUED SALARIES AND WAGES' , '','',roundoff($totalx)));
// $excel->writeLine(array('-1432', 'PAGIBIG CONTRIBUTION PAYABLE' , '','',roundoff($pitx)));
// $excel->writeLine(array('-1433', 'EMPLOYEES W/HOLDING TAXES PAYABLE' , '','',roundoff($basictax)));
// $excel->writeLine(array('-1434', 'EXPANDED TAXES PAYABLE' , '','',roundoff($talenttax)));
// $excel->writeLine(array('1436', 'PHILHEALTH' , '','',roundoff($phtx)));
// $excel->writeLine(array('-1436', 'SSS CONTRIBUTIONS PAYABLE' , '','',roundoff($ssstx)));


// $tx = $ssstx+$phtx+$talenttax+$basictax+$pitx+$totalx+$sssln+$piln;
//$excel->writeLine(array('-1436', '' , '','',roundoff($tx)));


$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));







$excel->writeLine(array('DEPARTMENT', 'NETPAY'));

$select = "select
	t2.department,
 	sum(netpay) as netpay
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.post_type='" . $_GET['type'] . "' AND t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . "  group by t1.department";
$result = mysql_query($select, connect());

$total = 0;

while($row1 = mysql_fetch_array($result)){
	$excel->writeLine(array($row1['department'], roundoff($row1['netpay'])));
	$total += $row1['netpay'];
}
$excel->writeLine(array('TOTAL', roundoff($total)));


$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));
$excel->writeLine(array(''));



$select = "select
	sum(t1.salary) as salary,
	sum(t1.taxable_salary) as taxable_salary,
	sum(t1.other_tax_inc) as other_tax_inc
	from posted_summary t1 where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id;
$result = mysql_query($select, connect());

$total = 0;

while($row1 = mysql_fetch_array($result)){
	$excel->writeLine(array('BASIC',roundoff($row1['salary'])));
	$excel->writeLine(array('COLA',0));
	$excel->writeLine(array('TALENT FEE',roundoff($talentfee)));
	$excel->writeLine(array('BUNOS',0));
	$excel->writeLine(array('OTHER EARNINGS',roundoff($row1['other_tax_inc'])));
	$excel->writeLine(array('GROSS PAY', roundoff($row1['taxable_salary'])));
	//$excel->writeLine(array('ADJUSTMENT',''));
}


$excel->writeLine(array('WITHHOLDING TAX',roundoff($basictax)));
$excel->writeLine(array('SSS CONTRIBUTION',roundoff($ssstx)));
$excel->writeLine(array('MEDICARE',roundoff($phtx)));
$excel->writeLine(array('DWOP',0));


$select1 = "select sum(t2.amount) as amount, t2.name from employee_deduction t2 left join posted_summary t1 on (t1.posted_id = t2.posted_id) where t1.post_type='" . $_GET['type'] . "' AND  t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " group by t2.name";
$result1 = mysql_query($select1, connect());
$ttl1 = 0;
while($row1 = mysql_fetch_array($result1)){
	$excel->writeLine(array($row1['name'] , roundoff($row1['amount'])));
	$ttl1 = $ttl1 + $row1['amount'];
}

$excel->writeLine(array('TOTAL DEDUCTIONS',$ttl1));
$excel->writeLine(array('ADJUSTMENT ON DEDUCTIONS',''));


header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename='.'jv_' . $ymd . '.xls');
header("Location: " ."jv_" . $ymd . ".xls");
exit;

?>
