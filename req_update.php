<?php
include "config.php";

if($_GET['type']=='emp'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$delete = "delete from view_payroll_hist where typ = 'emp' and date1 = '" . $_GET['date1'] . "' and date2 = '" . $_GET['date2'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('emp','" . $ch . "', '" . $_GET['date1'] . "', '" . $_GET['date2'] . "', '" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}

if($_GET['type']=='basic'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$delete = "delete from view_payroll_hist where typ = 'basic' and date1 = '" . $_GET['date1'] . "' and date2 = '" . $_GET['date2'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('basic','" . $ch . "', '" . $_GET['date1'] . "', '" . $_GET['date2'] . "', '" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}
	
if($_GET['type']=='abs'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
		
	echo $delete = "delete from view_payroll_hist where typ = 'abs' and date1 = '" . $_GET['date1'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	echo $insert = "insert into view_payroll_hist value('abs','" . $ch . "', '" . $_GET['date1'] . "', '0000-00-00','" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}
	
if($_GET['type']=='late'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
		
	$delete = "delete from view_payroll_hist where typ = 'late' and date1 = '" . $_GET['date1'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('late','" . $ch . "', '" . $_GET['date1'] . "', '0000-00-00','" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}
	
if($_GET['type']=='ot'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	
	$delete = "delete from view_payroll_hist where typ = 'ot' and date1 = '" . $_GET['date1'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('ot','" . $ch . "', '" . $_GET['date1'] . "', '0000-00-00','" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}
if($_GET['type']=='ut'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
		
	$delete = "delete from view_payroll_hist where typ = 'ut' and date1 = '" . $_GET['date1'] . "' and emid = '" . $_GET['emid'] . "' ";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('ut','" . $ch . "', '" . $_GET['date1'] . "', '0000-00-00','" . $_GET['emid'] . "', '') ";
	mysql_query($insert, connect());
	}
	
if($_GET['type']=='oth' or $_GET['type']=='ded' or $_GET['type']=='nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
		
	$delete = "delete from view_payroll_hist where typ = '" . $_GET['type'] . "' and date1 = '" . $_GET['date1'] . "' and date2 = '" . $_GET['date2'] . "' and emid = '" . $_GET['emid'] . "' and misc = '" . $_GET['msc'] . "'";
	mysql_query($delete, connect());
	
	$insert = "insert into view_payroll_hist value('" . $_GET['type'] . "','" . $ch . "', '" . $_GET['date1'] . "', '" . $_GET['date2'] . "', '" . $_GET['emid'] . "', '" . $_GET['msc'] . "') ";
	mysql_query($insert, connect());
	}
?>