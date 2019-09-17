<?php
if (isset($_POST['vadd'])){
	$insert = "insert into employee_violations values (NULL,'" . $_GET['id'] . "','" . $_POST['name'] . "','" . $_POST['description'] . "','" . $_POST['date'] . "')";
	mysql_query($insert, connect());
	}

if (isset($_POST['vdel'])){
	$delete = "delete from employee_violations where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="lightgreen">
	<tr>
		<td colspan=4 align="left">VIOLATIONS</td>
	</tr>
	<tr>
		<td width=25% align="left">Title</td>
		<td width=40% align="left">Description</td>
		<td width=20% align="left" colspan=2>Date Awarded/Issued</td>
	</tr>
	<tr>
		<td align="right"><input type="text" name="name" style="width:100%"></td>
		<td align="right"><input type="text" name="description" style="width:100%"></td>
		<td align="right"><input type="text" name="date" style="width:100%"></td>
		<td width=5% align="right"><input type="submit" name="vadd" value="add" style="width:100%"></td>
	</tr>
	<?php
	$select = "select * from employee_violations where em_id = '" . $_GET['id'] . "' order by `date`  desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['description']; ?></td>
		<td><?php echo $row['date']; ?></td>
		<td><input type="submit" name="vdel" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
<br>
<br>
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC6699">
	<tr>
		<td align="left">LATES INFORMATION</td>
	</tr>
</table>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC6699">
	<tr>
		<td width=100px align="center">Date</td>
		<td width=100px align="center">Shift Code</td>
		<td width=100px align="center">Time In</td>
		<td width=100px align="center">Time Out</td>
		<td width=60px align="center">Min.</td>
		<td align="center">Tagged By</td>
	</tr>
	
	<?php
	$select = "select * from transaction where em_id = '" . $_GET['id'] . "' and late > 0 order by trxn_date desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['shift_code']; ?></td>
		<td><?php echo $row['trxn_time_in']; ?></td>
		<td><?php echo $row['trxn_time_out']; ?></td>
		<td><?php echo $row['late']; ?></td>
		<td><?php echo $row['username']; ?></td>
	</tr>	
	<?php
	}
	?>
</table>
<br>
<br>
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC9933">
	<tr>
		<td align="left">ABSENCES INFORMATION</td>
	</tr>
</table>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC9933">
	<tr>
		<td width=20% align="center">Date</td>
		<td width=80% align="center">Tagged By</td>
	</tr>
	
	<?php
	$select = "select * from transaction where em_id = '" . $_GET['id'] . "' and status = 'ABSENT' order by trxn_date desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['username']; ?></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>