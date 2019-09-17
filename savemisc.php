<?php
include "config.php";

if($_GET['type'] == 1){
	$amount = $_GET['var'] * $_GET['perday'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Extra Day (" . $_GET['var'] . ")', 
		'" . $_GET['emid'] . "', 
		'" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
if($_GET['type'] == 2){
	$amount = $_GET['var'] * $_GET['permin'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Extra Time (" . roundoffNoComma($_GET['var']) . ")', 
		'" . $_GET['emid'] . "', 
		'" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
if($_GET['type'] == 3){
	$amount = $_GET['var'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Taxable Income Adjustment', 
		'" . $_GET['emid'] . "', 
		'" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
if($_GET['type'] == 4){
	$amount = $_GET['var'] * $_GET['permin'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Tardy (-" . roundoffNoComma($_GET['var']) . ")', 
		'" . $_GET['emid'] . "', 
		'-" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
if($_GET['type'] == 5){
	$amount = $_GET['var'] * $_GET['perday'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Extra Day (-" . $_GET['var'] . ")', 
		'" . $_GET['emid'] . "', 
		'-" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
if($_GET['type'] == 6){
	$amount = $_GET['var'] * $_GET['permin'];

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`
		)
		VALUES (
		NULL , 
		'Extra Time (-" . roundoffNoComma($_GET['var']) . ")', 
		'" . $_GET['emid'] . "', 
		'-" . $amount . "', 
		'0', 
		'Pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_GET['rem'] . "'
		)";
	}
	
mysql_query($insert, connect());
?>