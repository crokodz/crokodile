<?php
$result = mysql_query("select *from users;", connect());

if (isset($_POST['add'])){
	$insert = "INSERT INTO `employee_leave` (
		`id` ,
		`em_id` ,
		`name` ,
		`from` ,
		`to` ,
		`days` ,
		`status`
		)
		VALUES (
		NULL ,
		'" . $_GET['id'] . "',
		'" . $_POST['type'] . "',
		'" . $_POST['from'] . "',
		'" . $_POST['to'] . "',
		'" . $_POST['days'] . "',
		'FOR APPROVAL'
		)";
	mysql_query($insert, connect());
	}

if (isset($_POST['del'])){
	$update = "update `employee_leave` set status = 'DELETED' where id = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}

$select = "select count(*) as count from employee where `em_id` = '" . $_GET['id'] . "' and `manager` = '" . $_SESSION['user'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$count = $row['count'];
if ($count == 0){
	$disable = 'disabled';
	}
else{
	$disable = '';
	}
?>
<h3 class="wintitle">Employee Leave</h3>
<form method="post">

<input type="hidden" name="id">
<!-- <table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#33FF33">
	<tr>
		<td align="left" colspan=5>LEAVE INFORMATION <b>(STATUS FOR APPROVAL)</b></td>
	</tr>
	<tr>
		<td width=60% align="center">Leave Type</td>
		<td width=15% align="center">From</td>
		<td width=15% align="center">To</td>
		<td width=5% align="center">Days</td>
		<td width=5% align="center"></td>
	</tr>

	<?php
	$select = "select * from employee_leave where em_id = '" . $_GET['id'] . "' and status = 'FOR APPROVAL'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['from']; ?></td>
		<td><?php echo $row['to']; ?></td>
		<td><?php echo $row['days']; ?></td>
		<td><input type="submit" name="del" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>
	<?php
	}
	?>
</table>
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#33FF33">
	<tr bgcolor="#33F3FF">
		<td align="left" colspan=5>ONWARD LEAVE INFORMATION</td>
	</tr>
	<tr>
		<td width=55% align="center">Leave Type</td>
		<td width=15% align="center">From</td>
		<td width=15% align="center">To</td>
		<td width=5% align="center">Days</td>
		<td width=10% align="center">Status</td>
	</tr>

	<?php
	$select = "select * from employee_leave where em_id = '" . $_GET['id'] . "' and `from` >= '" . $now . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['from']; ?></td>
		<td><?php echo $row['to']; ?></td>
		<td><?php echo $row['days']; ?></td>
		<td><?php echo $row['status']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
<br> -->
<!--
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#33FF33">
	<tr>
		<td align="left" colspan=5>LEAVE REQUISITION FORM</td>
	</tr>
	<tr>
		<td width=60% align="center">Leave Type</td>
		<td width=15% align="center">From</td>
		<td width=15% align="center">To</td>
		<td width=5% align="center">Days</td>
		<td width=5% align="center"></td>
	</tr>
	<tr>
		<td>
			<select style="width:100%" name="type" id="type">
			<option><?php echo $row['name']; ?></option>
			<?php
			$select = "select * from `ot_rate`";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td width=10%><input type="text" name="from" id="from" style="width:100%;" value="<?php echo date('Y-m-d'); ?>"></td>
		<td width=10%><input type="text" name="to" id="to" style="width:100%;" value="<?php echo date('Y-m-d'); ?>"></td>
		<td width=10%><input type="text" name="days" id="days" style="width:100%;"></td>
		<td align="right"><input type="submit" value="for approval" name="add"></td>
</table> -->
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#33FF33">
	<tr>
		<td align="left" colspan=5>LEAVE HISTORY</td>
	</tr>
	<tr>
		<td width=60% align="center">Leave Type</td>
		<td width=15% align="center">Date</td>
		<td width=15% align="center">Remarks</td>
	</tr>

	<?php
	$select = "select * from transaction where em_id = '" . $_GET['id'] . "' and status like '%leave%' order by trxn_date desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['status']; ?></td>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['otremarks']; ?></td>
	</tr>
	<?php
	}
	?>
</table>
</form>
