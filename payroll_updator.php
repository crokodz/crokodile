<?php
require ('config.php');

if ($_SESSION['company'] != '0'){
	$select = "SELECT * FROM posted_summary where company_id = '" . $_SESSION['company'] . "' and posted_id = '" . $_GET['pid'] . "' GROUP BY posted_id";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	if (empty($row['posted_id'])){
		echo "<script>";
		echo "window.close();";
		echo "alert('Unauthorized access not allowed!!! Alert has been send to administrator!!! Administrator has the authority to block your IP w/o any warning...')";
		echo "</script>";
		}
	}
	
	
function GetPayCode($id,$status){
	$select = "select name from pay where `name` = '" . $id . "' LIMIT 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$name = $row['name'];
	
	$select = "select id from ot_rate where name = '" . $status . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$ot_id = $row['id'];
	
	$select = "select * from pay where name = '" . $name . "' and ot = '" . $ot_id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function getDeduction($id, $pid){
	$select = "select name, amount, balance from employee_deduction where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getDeductionBal($id, $pid){
	$select = "select name, sum(amount) as `bal` from employee_deduction where em_id = '" .  $id. "' and posted_id='0' group by `name`";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getNon_Taxable($id, $pid){
	$select = "select name, amount from employee_non_taxable where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getTaxable($id, $pid){
	$select = "select name, amount from employee_taxable where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	return $result;
	}
	
function getsss($salary){
	$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
	}
	
function getOt($id, $pid){
	$select = "select 
		sum(ot) as ot, 
		sum(nd) as nd, 
		sum(otx) as otx,
		`status`,
		`pay_id`
		from posted where em_id = '" . $id . "' and posted_id = '" . $pid . "' group by `status`";
	$result = mysql_query($select, connect());
	return $result;
	}	

function getpi($salary){
	if ($salary <= 1500){
		return $salary * .02;
		}
	else{
		return 100;
		}
	}
	
function getph($salary){
	$select = "select `id`,`ees`,`ers` from ph where `from` <= '" . $salary . "' order by `from` desc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ees'],$row['ers']);
	}
	
function gettin($salary,$type,$status){
	$select = "select * from tin where status = '" . $status . "' and type = '" . $type . "' and salary <= '" . $salary . "' order by salary desc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	$a = $salary - $row['salary'];
	$b = $a * $row['percent'];
	$c = $b + $row['exception'];
	return $c;
	}

function h2m($hours){
	$expl = explode(":", $hours); 
	return ($expl[0] * 60) + $expl[1];
	}
	
function m2h($mins) { 
	if ($mins < 0) { 
		$min = Abs($mins); 
		} 
	else { 
                $min = $mins; 
		}	 
	$H = Floor($min / 60); 
	$M = ($min - ($H * 60)) / 100; 
	$hours = $H +  $M; 
	
	if ($mins < 0) { 
                $hours = $hours * (-1); 
		} 
	$expl = explode(".", $hours); 
	$H = $expl[0]; 
	if (empty($expl[1])) { 
                $expl[1] = 00; 
		} 
	$M = $expl[1]; 
            if (strlen($M) < 2) { 
                $M = $M . 0; 
		} 
	$hours = $H . ":" . $M; 
	return $hours; 
	} 
	
function getOtRate($id){
	$select = "select rate from holidays where name = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['rate'];
	}

function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

function GetInfo($id){
	$select = "select `name`,`ts`,`department`,`company_id`, `salary_based`, `salary` from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['name'],$row['ts'],$row['department'],$row['company_id'],$row['salary_based'],$row['salary']);
	}
	
function GetSummary($id,$pid){
	$select = "select * from posted_summary where em_id = '" . $id . "' and posted_id = '" . $pid . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
	
function GetTYDTax($id){
	$select = "select sum(`taxable_salary`) as `taxable_salary` ,sum(`tax`) as `tax` from posted_summary where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['taxable_salary'],$row['tax']);
	}
	
function gogogogo($id, $pid,$pdf){
	$_GET['id'] = $id;
	$_GET['pid'] = $pid;
	$info = GetInfo($_GET['id']);
	$summary = GetSummary($_GET['id'],$_GET['pid']);
	$company = getcompany($info[3]);

	$var_sss = $company['sss'];
	$var_tin = $company['tin'];
	$var_ph = $company['ph'];
	$var_pi = $company['pi'];
	
	$varpd = explode("@", $summary['payday']);
	
	$svar = $varpd[0];
	$payday = $varpd[1];
	
	$salary = $summary['salary'];
	$perday = $summary['perday_salary'];
	$absent = $summary['absent'];
	$permin = (($perday / 8) / 60);
	
	$salary_ot = $summary['ot'];
	$salary_ut = $permin * $summary['ut'];
	$salary_late = $permin * $summary['late'];
	$nd = $summary['nd'];
	$halfday = 0;
	
	$totaladjustment = ($absent * $perday) + ($salary_late) + ($salary_ut) + ($halfday);
	
	#taxable
	$result = getTaxable($_GET['id'],$_GET['pid']);
	$otherstaxable = 0;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$otherstaxable = $otherstaxable + $row['amount'];
		}
	
	
	#non-taxable
	$result = getNon_Taxable($_GET['id'],$_GET['pid']);
	$othersnontaxable = 0;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$othersnontaxable = $othersnontaxable + $row['amount'];
		}
		
	#deduction
	$result = getDeduction($_GET['id'],$_GET['pid']);
	$otherdeduction = 0;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$otherdeduction = $otherdeduction + $row['amount'];
		}
		
	$earnings = ($salary-$totaladjustment)+$summary['ot']+$summary['otx']+$summary['nd']+$summary['adjustment']+$otherstaxable;
		
	#social security system
	if($var_sss == $svar){
		$sss = getsss($earnings);
		$sss_id = $sss[0];
		$sss_employee = $sss[1];
		$sss_employer = $sss[2] + $sss[3];
		}
	elseif($var_sss == 'h'){
		$sss = getsss($earnings);
		$sss_id = $sss[0];
		$sss_employee = $sss[1];    
		$sss_employer = $sss[2] + $sss[3];
		}
	elseif($var_sss == 'hh'){
		$pp = explode("@", $summary['payday']);
		if($pp[0] == 'w1'){
			$sss = getsss($earnings);
			$sss_id = $sss[0];
			$sss_employee = $sss[1];    
			$sss_employer = $sss[2] + $sss[3];
			$update = "update posted_summary set `sss` = '" . $sss_employee . "', earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		else{
			$pd = 'w1@' . $pp[1];
			$select = " select sss, earnings from posted_summary  where posted_id < '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "' and payday = '" . $pd . "' ";
			$pdresult = mysql_query($select, connect());
			$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
			$pdearnings = $pdrow['earnings'] + $earnings;
			
			$sss = getsss($pdearnings);
			$sss_id = $sss[0];
			$sss_employee = $sss[1] - $pdrow['sss'];    
			$sss_employer = $sss[2] + $sss[3];
			
			$update = "update posted_summary set `sss` = '" . $sss_employee . "' , earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		}
		
	#pag-ibig
	if($var_pi == $svar){
		$pi = getpi($earnings);
		$pi_employee = $pi;
		$pi_employer = $pi;
		}
	elseif($var_pi == 'hh'){
		$pp = explode("@", $summary['payday']);
		if($pp[0] == 'w1'){
			$pi = getpi($earnings);
			$pi_employee = $pi;
			$pi_employer = $pi;
			$update = "update posted_summary set `pi` = '" . $pi_employee . "', earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		else{
			$pi_employee = 0;
			$pi_employer = 0;
			$update = "update posted_summary set `pi` = '0', earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		}
	elseif($var_pi == 'h'){
		$pi = getpi($earnings);
		$pi_employee = $pi/2;
		$pi_employer = $pi/2;
		}
		
	#phil health
	if($var_ph == $svar){
		$ph = getph($earnings);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1];
		$ph_employer = $ph[2];
		}
	elseif($var_ph == 'h'){
		$ph = getph($earnings);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1];
		$ph_employer = $ph[2];
		}	
	elseif($var_ph == 'hh'){
		$pp = explode("@", $summary['payday']);
		if($pp[0] == 'w1'){
			$ph = getph($earnings);
			$ph_1d = $ph[0];
			$ph_employee = $ph[1];
			$ph_employer = $ph[2];
			$update = "update posted_summary set `ph` = '" . $ph_employee . "', earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		else{
			$pd = 'w1@' . $pp[1];
			$select = " select ph, earnings from posted_summary  where posted_id < '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "' and payday = '" . $pd . "' ";
			$pdresult = mysql_query($select, connect());
			$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
			$pdearnings = $pdrow['earnings'] + $earnings;
			
			$ph = getph($pdearnings);
			$ph_1d = $ph[0];
			$ph_employee = $ph[1] - $pdrow['ph'];
			$ph_employer = $ph[2];
			
			$update = "update posted_summary set `ph` = '" . $ph_employee . "', earnings = '" . $earnings . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
			mysql_query($update, connect());
			}
		}
		
			
	#tax
	$taxable = ($salary - $totaladjustment) + $summary['adjustment']+$summary['ot']+$summary['otx']+$summary['nd'] + $otherstaxable; #- ($sss_employee + $ph_employee + $pi_employee);
	$tin = gettin($taxable,$info[4],$info[1]);
	$gross = $taxable - $tin;
	$netpay = $taxable - $tin + ($othersnontaxable - $otherdeduction) - ($sss_employee + $ph_employee + $pi_employee);
	$update = "update posted_summary set `tax` = '" . $tin . "', `netpay` = '" . $netpay . "', `taxable_salary` = '" . $taxable . "' where posted_id = '" . $_GET['pid'] . "' and em_id = '" . $_GET['id'] . "'";
	mysql_query($update, connect());
	}

function make10($amount){
	$amount = roundoff($amount,2);
	
	if (strlen($amount) == 4){
		return "      " . $amount;
		}
	elseif (strlen($amount) == 5){
		return "     " . $amount;
		}
	elseif (strlen($amount) == 6){
		return "    " . $amount;
		}
	elseif (strlen($amount) == 7){
		return "   " . $amount;
		}
	elseif (strlen($amount) == 8){
		return "  " . $amount;
		}
	elseif (strlen($amount) == 9){
		return " " . $amount;
		}
	elseif (strlen($amount) == 10){
		return $amount;
		}
	}

#ALLhttp://192.168.1.98/hris/reports/payslip.php?id=107474&pid=3&idd=3
if ($_GET['id'] == 'ALL'){
	$select = "select posted_summary.* from posted_summary join employee using(em_id) where posted_id = '" . $_GET['pid'] . "' and employee.status = 'active' group by em_id";
	$result_summary = mysql_query($select, connect());
	while($row_summary = mysql_fetch_array($result_summary,MYSQL_ASSOC)){
		$_GET['id'] = $row_summary['em_id'];
		$pdf = gogogogo($_GET['id'], $_GET['pid'],$pdf);
		}
	}
?>
<script>
window.opener.location = 'index.php?menu=5';
self.window.close();
</script>