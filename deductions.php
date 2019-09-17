<?php
include "config.php";

if (isset($_POST['save'])){
	$insert = "insert into `deductions` (
		`id`,
		`name` ,
		`description` 
		)
		values (
		null , 
		'" . $_POST['name'] . "',
		'" . $_POST['description'] . "'
		)";
	mysql_query($insert, connect());
	}
	
if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update deductions set 
			name = '" . $_POST['name' . $x] . "',
			description = '" . $_POST['description' . $x] . "'
			where id = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from deductions where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Deduction</h3>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=30%>Name</td>
		<td width=65% colspan=2>Description</td>
	</tr>
	<tr>
		<td><input type="text" name="name" style="width:100%"></td>
		<td colspan=2><input type="text" name="description" style="width:100%"></td>
	</tr>
	<tr>
		<td colspan=3><input type="submit" name="save" value="save"></td>
	</tr>
	<tr>
		<td colspan=3>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from deductions order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><input type="text" name="name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['name']; ?>"></td>
		<td><input type="text" name="description<?php echo $x; ?>" style="width:100%" value="<?php echo $row['description']; ?>"></td>
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
</body>