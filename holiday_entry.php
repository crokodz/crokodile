<?php
if (isset($_POST['save'])){
	$insert = "insert into `holiday_entry` (
		`id`,
		`name` ,
		`date` ,
		`type`,
		`username`,
		`datetime`
		)
		values (
		null , 
		'" . $_POST['name'] . "',
		'" . $_POST['date'] . "',
		'" . $_POST['type'] . "',
		'" . $_SESSION['user'] . "',
		now()
		)";
	mysql_query($insert, connect());
	}
	
if (isset($_POST['savez'])){
	$update = " update transaction set `status` = '" . $_POST['typez'] . "' where `trxn_date` = '" . $_POST['datez'] . "' ";
	mysql_query($update, connect());
	}
	
if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update holiday_entry set 
			name = '" . $_POST['name' . $x] . "',
			date = '" . $_POST['date' . $x] . "',
			type = '" . $_POST['type' . $x] . "'
			where id = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from holiday_entry where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Holiday Entry</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">

	<tr>
		<td width=32%>
			<select name="typez" style="width:100%">
			<?php
			$select = "select * from holidays";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td width=10%><input type="text" name="datez" style="width:100%"></td>
		<td><input type="submit" name="savez" value="Update Transaction"></td>
	</tr>
</table>
<br>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=30%>Name</td>
		<td width=10%>Date</td>
		<td width=55% colspan=2>Type</td>
	</tr>
	<tr>
		<td><input type="text" name="name" style="width:100%"></td>
		<td><input type="text" name="date" style="width:100%"></td>
		<td colspan=2>
			<select name="type" style="width:100%">
			<?php
			$select = "select * from holidays";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=3><input type="submit" name="save" value="save"></td>
	</tr>
	<tr>
		<td colspan=3>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from holiday_entry order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><input type="text" name="name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['name']; ?>"></td>
		<td><input type="text" name="date<?php echo $x; ?>" style="width:100%" value="<?php echo $row['date']; ?>"></td>
		<td>
			<select name="type<?php echo $x; ?>" style="width:100%">
			<option><?php echo $row['type']; ?></option>
			<?php
			$select = "select * from holidays";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td width=5%><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=3 align="right"><input type="submit" name="update" value="update"> | <input type="submit" name="delete" value="delete checked"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>