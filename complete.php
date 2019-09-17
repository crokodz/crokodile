<?php
function h2m($hours){
	$expl = explode(":", $hours); 
	return ($expl[0] * 60) + $expl[1];
	}
	
function GetShift($id){
	$select = "select `from`,`to` from shift where shift_code = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return array($row['from'],$row['to']);
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

if (isset($_POST['comp'])){
	$select = "select * from employee where company_id = '" . $_POST['company'] . "' and status = 'active'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$from = $_POST['from'];
		$to = $_POST['to'];
		$difference = (strtotime($to)-strtotime($from));
		$days = $difference/24;
		$days = $days/60;
		$days = $days/60;
		$days = ceil($days);
		$shift = GetShift($row['shift_code']);
		for($x=0;$x<=$days;$x++){
			$ya = explode("-",$from);
			$date =  date('Y-m-d',mktime(0, 0, 0, $ya[1], $ya[2], $ya[0]) + ($x * 24 * 60 * 60));
		
			$select = "select * from transaction where em_id = '" . $row['em_id'] . "' and trxn_date = '" . $date . "' limit 1";
			$result1 = mysql_query($select, connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
	
			if (!$row1['em_id']){
				$insert = "INSERT INTO `transaction` (
					`trxn_id` ,
					`trxn_date` ,
					`trxn_time_in` ,
					`trxn_time_out` ,
					`em_id` ,
					`salary_based` ,
					`salary` ,
					`ot` ,
					`holiday` ,
					`username` ,
					`datetime` ,
					`status` ,
					`shift_code` ,
					`allowed_late` ,
					`allowed_ot` ,
					`allowed_ut` ,
					`posted_id` ,
					`company_id` ,
					`pay_id` ,
					`start_ot` ,
					`end_ot`
					)
					VALUES (
					NULL , 
					'" . $date . "', 
					'" . $shift[0] . "', 
					'" . $shift[1] . "', 
					'" . $row['em_id'] . "', 
					'" . $row['salary_based'] . "', 
					'" . $row['salary'] . "', 
					'0', 
					'', 
					'', 
					NOW( ),
					'REGULAR', 
					'" . $row['shift_code'] . "', 
					'" . $row['allowed_late'] . "', 
					'" . $row['allowed_ot'] . "', 
					'" . $row['allowed_ut'] . "', 
					'0', 
					'" . $row['company_id'] . "', 
					'" . $row['pay_id'] . "', 
					'00:00:00', 
					'00:00:00'
					)";
				mysql_query($insert, connect());
				}
			else{
				$update = "update transaction set trxn_time_out = CURTIME() where em_id = '" . $row['em_id'] . "' and trxn_date = CURDATE()";
				#mysql_query($update, connect());
				}
			}
		}
	}

if (isset($_POST['timed'])){
	$select = "select * from employee where em_id = '" . $_POST['em_id'] . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$from = $_POST['from'];
		$to = $_POST['to'];
		$difference = (strtotime($to)-strtotime($from));
		$days = $difference/24;
		$days = $days/60;
		$days = $days/60;
		$days = ceil($days);
		$shift = GetShift($row['em_id']);
		for($x=0;$x<=$days;$x++){
			$ya = explode("-",$from);
			$date =  date('Y-m-d',mktime(0, 0, 0, $ya[1], $ya[2], $ya[0]) + ($x * 24 * 60 * 60));
		
			$select = "select * from transaction where em_id = '" . $row['em_id'] . "' and trxn_date = '" . $date . "' limit 1";
			$result1 = mysql_query($select, connect());
			$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
	
			if (!$row1['em_id']){
				$insert = "INSERT INTO `transaction` (
					`trxn_id` ,
					`trxn_date` ,
					`trxn_time_in` ,
					`trxn_time_out` ,
					`em_id` ,
					`salary_based` ,
					`salary` ,
					`ot` ,
					`holiday` ,
					`username` ,
					`datetime` ,
					`status` ,
					`shift_code` ,
					`allowed_late` ,
					`allowed_ot` ,
					`allowed_ut` ,
					`posted_id` ,
					`company_id` ,
					`pay_id` ,
					`start_ot` ,
					`end_ot`
					)
					VALUES (
					NULL , 
					'" . $date . "', 
					'" . $shift[0] . "', 
					'" . $shift[1] . "', 
					'" . $row['em_id'] . "', 
					'" . $row['salary_based'] . "', 
					'" . $row['salary'] . "', 
					'0', 
					'', 
					'', 
					NOW( ),
					'REGULAR', 
					'" . $row['shift_code'] . "', 
					'" . $row['allowed_late'] . "', 
					'" . $row['allowed_ot'] . "', 
					'" . $row['allowed_ut'] . "', 
					'0', 
					'" . $row['company_id'] . "', 
					'" . $row['pay_id'] . "', 
					'00:00:00', 
					'00:00:00'
					)";
				mysql_query($insert, connect());
				}
			else{
				$update = "update transaction set trxn_time_out = CURTIME() where em_id = '" . $row['em_id'] . "' and trxn_date = CURDATE()";
				#mysql_query($update, connect());
				}
			}
		}
	}
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Time Keeping Refill</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=15%>Form - To</td>
		<td width=20%><input type="text" name="from" value="<?php echo date("Y-m-d"); ?>"><input type="text" name="to" value="<?php echo date("Y-m-d"); ?>"></td>
	</tr>
	<tr>
		<td width=15%>Employee Id</td>
		<td width=20%><input type="text" name="em_id" value="<?php echo $_GET['id']; ?>"></td>
		<td width=65%><input type="submit" name="timed" value="submit"></td>
	</tr>
	<tr>
		<td width=15%>Company</td>
		<td width=20%>
		<select name="company" id="company">
			<?php
			$result_data = $result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
			<?php
			}
			?>
		</select>
		</td>
		<td width=65%><input type="submit" name="comp" value="submit"></td>
	</tr>
</table>
</form>