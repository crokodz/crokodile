<?php
include "config.php";

if (isset($_POST['save'])){
	$insert = "insert into `branch` (
		`id`,
		`branch` ,
		`address` ,
		`telephone` ,
		`manager`,
		`company`
		)
		values (
		null , 
		'" . $_POST['branch'] . "',
		'" . $_POST['address'] . "',
		'" . $_POST['telephone'] . "',
		'" . $_POST['manager'] . "',
		'" . $_POST['company'] . "'
		)";
	mysql_query($insert, connect());
	}
	
if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update branch set 
			branch = '" . $_POST['branch' . $x] . "',
			address = '" . $_POST['address' . $x] . "',
			telephone = '" . $_POST['telephone' . $x] . "',
			company = '" . $_POST['company' . $x] . "',
			manager = '" . $_POST['manager' . $x] . "'
			where id = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from branch where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Branches</h3>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10%>Company</td>
		<td width=20%>Branch Name</td>
		<td width=35%>Address</td>
		<td width=15%>Telephone</td>
		<td width=20% colspan=2>Manager</td>
	</tr>
	<tr>
		<td>
			<select style="width:100%" name="company">
			<?php
			$select = "select * from company where status='active'";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td><input type="text" name="branch" style="width:100%"></td>
		<td><input type="text" name="address" style="width:100%"></td>
		<td><input type="text" name="telephone" style="width:100%"></td>
		<td colspan=2><input type="text" name="manager" style="width:100%"></td>
	</tr>
	<tr>
		<td colspan=5><input type="submit" name="save" value="save"></td>
	</tr>
	<tr>
		<td colspan=5>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from branch order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td>
			<select style="width:100%" name="company<?php echo $x; ?>">
			<option><?php echo $row['company']; ?></option>
			<?php
			$select = "select * from company where status='active'";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td><input type="text" name="branch<?php echo $x; ?>" style="width:100%" value="<?php echo $row['branch']; ?>"></td>
		<td><input type="text" name="address<?php echo $x; ?>" style="width:100%" value="<?php echo $row['address']; ?>"></td>
		<td><input type="text" name="telephone<?php echo $x; ?>" style="width:100%" value="<?php echo $row['telephone']; ?>"></td>
		<td><input type="text" name="manager<?php echo $x; ?>" style="width:100%" value="<?php echo $row['manager']; ?>"></td>
		<td width=5%><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['id']; ?>"></td>
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