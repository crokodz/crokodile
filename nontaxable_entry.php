<?php
include "config.php";

if (isset($_POST['save'])){
	$insert = "insert into `nontaxable_entry` (
		`id`,
		`name` ,
		`description`,
		acct_name,
		acct_code,
		acct_name1,
		acct_code1
		)
		values (
		null ,
		'" . $_POST['name'] . "',
		'" . $_POST['description'] . "',
		'" . $_POST['acct_name'] . "',
		'" . $_POST['acct_code'] . "',
		'" . $_POST['acct_namex'] . "',
		'" . $_POST['acct_codex'] . "'
		)";
	mysql_query($insert, connect());
	}

if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update nontaxable_entry set
			name = '" . $_POST['name' . $x] . "',
			description = '" . $_POST['description' . $x] . "',
			acct_name = '" . $_POST['acct_name' . $x] . "',
			acct_code = '" . $_POST['acct_code' . $x] . "',
			acct_name1 = '" . $_POST['acct_namex' . $x] . "',
			acct_code1 = '" . $_POST['acct_codex' . $x] . "'
			where id = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from nontaxable_entry where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Non-Taxable</h3>
<body id="innerframe">
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=200px>Name</td>
		<td width=100px>Acct Name (COS)</td>
		<td width=100px>Acct Code (COS)</td>
		<td width=100px>Acct Name (OPX)</td>
		<td width=100px>Acct Code (OPX)</td>
		<td colspan=2>Description</td>
	</tr>
	<tr>
		<td><input type="text" name="name" style="width:100%"></td>
		<td><input type="text" name="acct_name" style="width:100%"></td>
		<td><input type="text" name="acct_code" style="width:100%"></td>
		<td><input type="text" name="acct_namex" style="width:100%"></td>
		<td><input type="text" name="acct_codex" style="width:100%"></td>
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
	$select = "select * from nontaxable_entry order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><input type="text" name="name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['name']; ?>"></td>
		<td><input type="text" name="acct_name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_name']; ?>"></td>
		<td><input type="text" name="acct_code<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_code']; ?>"></td>
		<td><input type="text" name="acct_namex<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_name1']; ?>"></td>
		<td><input type="text" name="acct_codex<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_code1']; ?>"></td>
		<td><input type="text" name="description<?php echo $x; ?>" style="width:100%" value="<?php echo $row['description']; ?>"></td>
		<td width=5%><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=7 align="right"><input type="submit" name="update" value="update"> | <input type="submit" name="delete" value="delete checked"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>