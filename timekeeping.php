<?php
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

if (isset($_POST['timed'])){
	$select = "select * from employee where em_id = '" . $_POST['em_id'] . "' limit 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	
	if($row['em_id']){
		$select = "select * from transaction where em_id = '" . $_POST['em_id'] . "' and trxn_date = curdate() limit 1";
		$result = mysql_query($select, connect());
		$row1 = mysql_fetch_array($result,MYSQL_ASSOC);
		
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
				CURDATE(), 
				CURTIME(), 
				'', 
				'" . $row['em_id'] . "', 
				'" . $row['salary_based'] . "', 
				'" . $row['salary'] . "', 
				'0', 
				'', 
				'', 
				NOW( ),
				'" . $row['status'] . "', 
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
			$update = "update transaction set trxn_time_out = CURTIME() where em_id = '" . $row['em_id'] . "' and 	trxn_date = CURDATE()";
			mysql_query($update, connect());
			}
		}
	else{
		echo 'id number ' . $_POST['em_id'] . ' cannot found';
		}
	}
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Time Keeping In/Out</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=15%>Employee Id</td>
		<td width=20%><input type="text" name="em_id" value="<?php echo $_POST['em_id']; ?>"></td>
		<td width=65%>&nbsp;</td>
	</tr>
	<tr>
		<td width=15%>Password</td>
		<td width=20%><input type="password" name="pin_number" value=""></td>
		<td width=65%><input type="submit" name="submit" value="submit"></td>
	</tr>
</table>
<br>
<?php
$select = "select * from employee where em_id = '" . $_POST['em_id'] . "' and `pin_number` = '" . $_POST['pin_number'] . "' limit 1";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
if($row['em_id']){
	?>
	<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=100%><input type="submit" name="timed" value="TIME IN/OUT" style="width:100%;"></td>
	</tr>
	</table>
	<br>
	<table width=100% border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td width=20%>Date</td>	
			<td width=20%>Time In</td>
			<td width=20%>Time Out</td>
			<td width=20%>Consumed Hour</td>
			<td width=20%>Over Time</td>
		</tr>
		<?php
		$select = "select * from transaction where em_id = '" . $row['em_id'] . "' and status != 'posted' order by trxn_id desc";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$ti = h2m($row['trxn_time_in']);
		$to = h2m($row['trxn_time_out']);
		$total = $to - $ti ;
		?>
		<tr>
			<td><?php echo $row['trxn_date']; ?></td>	
			<td><?php echo $row['trxn_time_in']; ?></td>
			<td><?php echo $row['trxn_time_out']; ?></td>
			<td><?php echo m2h($total); ?></td>
			<td><?php echo $row['ot']; ?></td>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
	?>
</form>