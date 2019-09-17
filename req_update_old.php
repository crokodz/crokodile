<?php
include "config.php";

if($_GET['type']=='basic'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
		
	$update = "update transaction set req_basic = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
	
if($_GET['type']=='abs'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_abs = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` = '" . $_GET['date1'] . "' ";
	mysql_query($update, connect());
	}
	
if($_GET['type']=='late'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_late = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` = '" . $_GET['date1'] . "' ";
	mysql_query($update, connect());
	}
	
if($_GET['type']=='ot'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_ot = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` = '" . $_GET['date1'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='ut'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_ut = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` = '" . $_GET['date1'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='0nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_rice = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='1nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	echo $update = "update transaction set req_laun = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='2nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_med = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='3nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_trans = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
if($_GET['type']=='4nt'){
	$ch = '';
	if($_GET['chk']=='true'){
		$ch = 'checked';
		}
	$update = "update transaction set req_meal = '" . $ch . "' where em_id = '" . $_GET['emid'] . "' and `trxn_date` between '" . $_GET['date1'] . "' and '" . $_GET['date2'] . "' ";
	mysql_query($update, connect());
	}
?>