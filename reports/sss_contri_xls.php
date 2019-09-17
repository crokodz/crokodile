
<?php
require ('../config.php');
require('excelwriter.inc.php');
$ymd = date("FjYgia");
$excel=new ExcelWriter($ymd."_sss_ph_pi_contri.xls");


$column_header = array('RADIO MINDANAO NETWORK INC.');
//$column_header = array('ACCOUNT SUMMARY - PAYROLL');
$column_header = array('FOR THE MONTH OF' . $_GET['payday']);

function getssss($salary,$cnfsss){
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

	if($cnfsss == 'YES'){
		$select = "select id, ssee, sser, ec from " . $tb . " where `to` >= '" . $salary . "' order by `from` asc limit 1";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
		}
	else{
		return array('0','0','0','0');
		}
	}

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



function getpi($salary,$cnfpi,$pdm=0){
	if($cnfpi == 'YES'){
		if ($salary <= 1500){
			return array($pdm+100,100);
			}
		else{
			return array($pdm+100,100);
			}
		}
	else{
		if($pdm > 0){
			return array($pdm,100);
		} else {
			return array(0,0);
		}
	}
}

$excel->writeLine($column_header);
$company_id = 1;
$company_array = array(1=>'RMN INC', 7=>'IBMI', 6=>'RMN INC - MANAGEMENT');
//~ if($_SESSION['user'] == 'raz'){
//~ 	$company_id = 7;
//~ 	$company_name = 'IBMI';
//~ }

//~ if($_SESSION['user'] == 'love'){
//~ 	$company_id = 1;
//~ 	$company_name = 'RMN INC';
//~ }

//~ if($_SESSION['user'] == 'mae'){
//~ 	$company_id = 6;
//~ 	$company_name = 'MANAGEMENT';
//~ }

$company_id = $_GET['compa'];
$company_name = $company_array[$company_id];

$excel->writeLine(array('MANAGEMENT'));


$excel->writeLine(array('', ''));
$excel->writeLine(array('', ''));

//$excel->writeLine(array('ACCOUNT', '', 'SL','AMOUNT',''));
$excel->writeLine(array('ID', 'FIRST NAME', 'LAST NAME','SSS EMPLOYEE','SSS EMPLOYER', 'EC', 'SSS DEDUCTED','SSS DIFF', 'SSS LOAN', '','PH EMPLOYEE', 'PH EMPLOYER', 'PH DEDUCTED', '', 'PAGIBIG EMPLOYEE', 'PAGIBIG EMPLOYER', 'PAGIBIG DEDUCTED','SSS EM Should be','SSS ER Should be', 'SSS EC Should be'));

$select = "select t1.pay_id, t2.fname, t2.mname, t2.lname, t1.em_id, sum(t1.sss) as sss, sum(t1.taxable_salary) as taxable_salary,
	group_concat(posted_id) as posted_id, sum(t1.ph) as ph, sum(t1.salary) as salary, t2.pdm, sum(t1.pi) as pi
	from posted_summary t1 left join employee t2 on (t1.em_id = t2.em_id) where t1.payday like '%" . $_GET['payday'] . "' and t1.company_id = " . $company_id . " group by t1.em_id"; 
$result = mysql_query($select, connect());

while($row = mysql_fetch_array($result)){
	$sssln = 0;

	 $select1 = "select amount from employee_deduction where name = 'SSS LOAN' and status = 'posted' and em_id = '" . $row['em_id'] . "' and posted_id in (" . $row['posted_id'] . ")";
	$result1 = mysql_query($select1, connect());
	while($row1 = mysql_fetch_array($result1)){
		$sssln = $sssln + $row1['amount'];
	}

	$cnfph = 'NO';
	if($row['ph'] > 0){
		$cnfph = 'YES';
	}
	$ph = getph($row['salary'],$cnfph);

	$cnfpi = 'NO';
	if($row['pi'] > 0){
		$cnfpi = 'YES';
	}
	$pi = getpi($row['salary'],$cnfpi,$row['pdm']);
	$pi_employee = $pi[0];
	$pi_employer = $pi[1];

	$cnfsss = 'NO';
	if($row['sss'] > 0){
		$cnfsss = 'YES';
	}

	$salary = $row['taxable_salary'];
	if($row['salary'] > 0){
		$salary  = $row['salary'];
	}

	$sss = getsss($row['sss'],$cnfsss);
	$d = $sss[1] - $row['sss'];
	if($d < 0){
		$sss[1] = $row['sss'];
	}
	$sss_employee = $sss[1];
	$sss_employer = $sss[2];
	$ec = $sss[3];


	if($row['ph'] != $ph[2]){
		$ph[1] = $row['ph'];
		$ph[2] = $row['ph'];
	}

	$sss_employeee = 0;
	$sss_employeer = 0;
	$sss_employeec = 0;
	if($sss_employee){
		$ssss = getssss($row['taxable_salary'],'YES');
		$sss_employeee = $ssss[1];
		$sss_employeer = $ssss[2];
		$sss_employeec = $ssss[3];;
	}

	//$sss_id = $ssss[0];

	//$sss_employer = $ssss[2] + $ssss[3];


	$excel->writeLine(array($row['em_id'],$row['fname'], $row['lname'],$sss_employee,$sss_employer, $ec, $row['sss'], $d, $sssln,'',$ph[1], $ph[2], $row['ph'], '', $pi_employee, $pi_employer, $row['pi'],$sss_employeee, $sss_employeer, $sss_employeec));




//~ 	$total = $row['netpay'] + $row['ot'] + $row['sss'] + $row['ph'] + $row['pi']+$ssst;
//~ 	$excel->writeLine(array('A01-3101', 'SALARIES AND WAGES' , '',$row['netpay'],''));
//~ 	$excel->writeLine(array('A01-3103', 'OVERTIME/LATE/UDERTIME' , '',$row['ot'],''));
//~ 	$excel->writeLine(array('A01-3104', 'SSS AND ECC CONTRIBUTIONS' , '',$row['sss']+$ssst,'',$row['sss'], $ssst));
//~ 	$excel->writeLine(array('A01-3105', 'MEDICARE CONTRIBUTIONS' , '',$row['ph'],''));
//~ 	$excel->writeLine(array('A01-3106', 'PAGIBIG CONTRIBUTIONS' , '',$row['pi'],''));
//~ 	$excel->writeLine(array('', $row['pay_id'] . " EXPENSE" , $total ,'',''));
//~
}

header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename='.$ymd."_sss_ph_pi_contri.xls");
header("Location: ".$ymd."_sss_ph_pi_contri.xls");
exit;

?>
