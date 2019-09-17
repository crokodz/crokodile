<?php
include "config.php";

function getperday($salary,$company,$id,$payid){
	$select = "select factor, days from pay where name = '" . $payid . "' limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	$factor = $row['factor'];
	$days = $row['days'];
	
	if ($id == 1){
		return ($salary * 12) / $factor;
		}
	elseif ($id == 2){
		return ($salary / $days);
		}
	}

$select = "select `salary`, `salary_based`,`company_id`,`pay_id` from employee where em_id = '" . $_GET['emid'] . "' ";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

if ($row['salary_based'] == 'SEMI-MONTHLY'){
	$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
	}
elseif ($row['salary_based'] == 'MONTHLY'){
	$perday = getperday($row['salary'],$row['company_id'],1,$row['pay_id']);
	}
elseif ($row['salary_based'] == 'DAILY'){
	$perday = $row['salary'];
	}
elseif ($row['salary_based'] == 'WEEKLY'){
	$perday = getperday($row['salary'],$row['company_id'],2,$row['pay_id']);
	}
elseif ($row['salary_based'] == 'HOURLY'){
	$perday = ($row['salary'] / 60) * $cinfo['min'];
	}

$perhour = $perday / 8;
$permin = $perhour / 60;
echo $perday;
?>
