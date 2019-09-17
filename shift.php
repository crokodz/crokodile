<?php
include "config.php";

if (isset($_POST['save'])){
	$insert = "insert into `shift` (
		`id`,
		`shift_code` ,
		`from` ,
		`to` ,
		`start_ndiff`,
		`end_ndiff`,
		`break`
		)
		values (
		null , 
		'" . $_POST['shift_code'] . "',
		'" . $_POST['from'] . "',
		'" . $_POST['to'] . "',
		'" . $_POST['start_ndiff'] . "',
		'" . $_POST['end_ndiff'] . "',
		'" . $_POST['break'] . "'
		)";
	mysql_query($insert, connect());
	}
	
if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update shift set 
			`shift_code` = '" . $_POST['shift_code' . $x] . "',
			`from` = '" . $_POST['from' . $x] . "',
			`to` = '" . $_POST['to' . $x] . "',
			`start_ndiff` = '" . $_POST['start_ndiff' . $x] . "',
			`end_ndiff` = '" . $_POST['end_ndiff' . $x] . "',
			`break` = '" . $_POST['break' . $x] . "'
			where `id` = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from shift where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Employee Status</h3>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>Shift Code</td>
		<td width=100px>From</td>
		<td width=100px>To</td>
		<td width=100px>Start Night Diff</td>
		<td width=100px>End Night Diff</td>
		<td width=100px>Break Time (min.)</td>
		<td width=30px>&nbsp;</td>
	</tr>
	<tr>
		<td><input type="text" name="shift_code" style="width:100%"></td>
		<td><input type="text" name="from" style="width:100%"></td>
		<td><input type="text" name="to" style="width:100%"></td>
		<td><input type="text" name="start_ndiff" style="width:100%"></td>
		<td><input type="text" name="end_ndiff" style="width:100%"></td>
		<td><input type="text" name="break" style="width:100%"></td>
	</tr>
	<tr>
		<td colspan=4><input type="submit" name="save" value="save"></td>
	</tr>
	<tr>
		<td colspan=4>&nbsp;</td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td>Shift Code</td>
		<td width=100px>From</td>
		<td width=100px>To</td>
		<td width=100px>Start Night Diff</td>
		<td width=100px>End Night Diff</td>
		<td width=100px>Break Time (min.)</td>
		<td width=30px>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from shift order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><input type="text" name="shift_code<?php echo $x; ?>" style="width:100%" value="<?php echo $row['shift_code']; ?>"></td>
		<td><input type="text" name="from<?php echo $x; ?>" style="width:100%" value="<?php echo $row['from']; ?>"></td>
		<td><input type="text" name="to<?php echo $x; ?>" style="width:100%" value="<?php echo $row['to']; ?>"></td>
		<td><input type="text" name="start_ndiff<?php echo $x; ?>" style="width:100%" value="<?php echo $row['start_ndiff']; ?>"></td>
		<td><input type="text" name="end_ndiff<?php echo $x; ?>" style="width:100%" value="<?php echo $row['end_ndiff']; ?>"></td>
		<td><input type="text" name="break<?php echo $x; ?>" style="width:100%" value="<?php echo $row['break']; ?>"></td>
		<td><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=5 align="right"><input type="submit" name="update" value="update"> | <input type="submit" name="delete" value="delete checked"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>