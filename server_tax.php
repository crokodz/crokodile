<?php
include "config.php";

function gettin($salary,$type,$status){
	$select = "select * from tin where status = '" . $status . "' and type = '" . $type . "' and salary <= '" . $salary . "' order by salary desc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	$a = $salary - $row['salary'];
	$b = $a * $row['percent'];
	$c = $b + $row['exception'];
	return $c;
	}
	

function getsss($salary){
	$select = "select id, ssee, sser, ec from sss where `to` >= '" . $salary . "' order by `from` asc limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['id'],$row['ssee'],$row['sser'],$row['ec']);
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
	
function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}
	
function GetInfo($id){
	$select = "select `ts`,`salary_based`,`pay_id`,`company_id`,`wtax`,`tin`,`sss`,`ph`,`pi`  from employee where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['salary_based'],$row['ts'],$row['pay_id'],$row['company_id'],$row['tin'],$row['sss'],$row['ph'],$row['pi']);
	}

$info = GetInfo($_GET['em_id']);
$cnftin = $info[4];
$cnfsss = $info[5];
$cnfph = $info[6];
$cnfpi = $info[7];

$earnings = $_GET['salary'];

$varpd = explode("@", $_GET['payday']);
$svar = $varpd[0];

$company = getcompany($info[3]);

$var_sss = $company['sss'];
$var_tin = $company['tin'];
$var_ph = $company['ph'];
$var_pi = $company['pi'];


#social security system
if($var_sss == $svar){
	$sss = getsss($earnings,$cnfsss);
	$sss_id = $sss[0];
	$sss_employee = $sss[1];
	$sss_employer = $sss[2] + $sss[3];
	}
elseif($var_sss == 'h'){
	$sss = getsss($earnings,$cnfsss);
	$sss_id = $sss[0];
	$sss_employee = $sss[1];    
	$sss_employer = $sss[2] + $sss[3];
	}
elseif($var_sss == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$sss = getsss($earnings,$cnfsss);
		$sss_id = $sss[0];
		$sss_employee = $sss[1];    
		$sss_employer = $sss[2] + $sss[3];
		}
	else{
		
		$pd = 'w1@' . $pp[1];
		$select = " select sss, taxable_salary from posted_summary  where em_id = '" . $_GET['em_id'] . "' and payday = '" . $pd . "' ";
		$pdresult = mysql_query($select, connect());
		$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
		$pdearnings = $pdrow['taxable_salary'] + $earnings;
		
		$sss = getsss($pdearnings,$cnfsss);
		$sss_id = $sss[0];
		$sss_employee = $sss[1] - $pdrow['sss'];    
		$sss_employer = $sss[2] + $sss[3];
		}
	}

#pag-ibig
if($var_pi == $svar){
	$pi = getpi($earnings,$cnfpi);
	$pi_employee = $pi;
	$pi_employer = $pi;
	}
elseif($var_pi == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$pi = getpi($earnings,$cnfpi);
		$pi_employee = $pi;
		$pi_employer = $pi;
		}
	else{
		$pi_employee = 0;
		$pi_employer = 0;
		}
	}
elseif($var_pi == 'h'){
	$pi = getpi($earnings,$cnfpi);
	$pi_employee = $pi/2;
	$pi_employer = $pi/2;
	}
	
#phil health
if($var_ph == $svar){
	$ph = getph($earnings,$cnfph);
	$ph_1d = $ph[0];
	$ph_employee = $ph[1];
	$ph_employer = $ph[2];
	}
elseif($var_ph == 'h'){
	$ph = getph($earnings,$cnfph);
	$ph_1d = $ph[0];
	$ph_employee = $ph[1];
	$ph_employer = $ph[2];
	}	
elseif($var_ph == 'hh'){
	$pp = explode("@", $_GET['payday']);
	if($pp[0] == 'w1'){
		$ph = getph($earnings,$cnfph);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1];
		$ph_employer = $ph[2];
		}
	else{
		$pd = 'w1@' . $pp[1];
		$select = " select ph, taxable_salary from posted_summary  where em_id = '" . $_GET['em_id'] . "' and payday = '" . $pd . "' ";
		$pdresult = mysql_query($select, connect());
		$pdrow = mysql_fetch_array($pdresult,MYSQL_ASSOC);
		$pdearnings = $pdrow['taxable_salary'] + $earnings;
		
		$ph = getph($pdearnings,$cnfph);
		$ph_1d = $ph[0];
		$ph_employee = $ph[1] - $pdrow['ph'];
		$ph_employer = $ph[2];
		}
	}
	
echo gettin($_GET['salary'] - ($pi_employee - $ph_employee - $sss_employee), $_GET['type'],$_GET['status']) . "@@" . $pi_employee . "@@" . $ph_employee . "@@" . $sss_employee;
?>