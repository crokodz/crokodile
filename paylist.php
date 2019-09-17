<?php
include "config.php";

if (isset($_POST['save'])){
	$select = "select * from ot_rate";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$insert = "insert into `pay` (
			`id`,
			`name` ,
			`description`,
			`ot`,
			`reg_rate`,
			`ot_rate`,
			`factor`,
			`min`,
			`days`,
			acct_name,
			acct_code
			)
			values (
			null ,
			'" . $_POST['name'] . "',
			'" . $_POST['description'] . "',
			'" . $row['id'] . "',
			'0.00',
			'0.00',
			'" . $_POST['factor'] . "',
			'" . $_POST['min'] . "',
			'" . $_POST['days'] . "',
			'" . $_POST['acct_name'] . "',
			'" . $_POST['acct_code'] . "'
			)";
		mysql_query($insert, connect());
		}
	}

if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update pay set
			name = '" . $_POST['name' . $x] . "',
			description = '" . $_POST['description' . $x] . "',
			factor = '" . $_POST['factor' . $x] . "',
			min = '" . $_POST['min' . $x] . "',
			days = '" . $_POST['days' . $x] . "',
			acct_name = '" . $_POST['acct_name' . $x] . "',
			acct_code = '" . $_POST['acct_code' . $x] . "'
			where `name` = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){

	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from pay where `name` = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Pay Code</h3>
<body id="innerframe">
<form method="post">
<table>
	<tr>
		<td width=100px>Name</td>
		<td><input type="text" name="name" style="width:200px;"></td>
	</tr>
	<tr>
		<td width=100px>Accounting Name</td>
		<td><input type="text" name="acct_name" style="width:200px;"></td>
	</tr>
	<tr>
		<td width=100px>Accounting Code</td>
		<td><input type="text" name="acct_code" style="width:200px;"></td>
	</tr>
	<tr>
		<td width=100px>Days per Year</td>
		<td><input type="text" name="factor" style="width:80px;"></td>
	</tr>
	<tr>
		<td width=100px>Days per Week</td>
		<td><input type="text" name="days" style="width:80px;"></td>
	</tr>
	<tr>
		<td>Mins per Day</td>
		<td><input type="text" name="min" style="width:80px;"></td>
	</tr>
	<tr>
		<td colspan=2><input type="submit" name="save" value="save"></td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<input type="hidden" name="description" style="width:100%">
</table>
<table>
	<tr>
		<td style="width:200px">Name</td>
		<td style="width:200px">Acct Name</td>
		<td style="width:100px">Acct Code</td>
		<td style="width:40px">Days per Year</td>
		<td style="width:40px">Days per Week</td>
		<td style="width:40px">Mins per Day</td>
		<td style="width:5%">&nbsp;</td>
		<td style="width:5%">&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from pay group by `name` order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><input type="text" name="name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['name']; ?>" readonly="true"></td>
		<td><input type="text" name="acct_name<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_name']; ?>"></td>
		<td><input type="text" name="acct_code<?php echo $x; ?>" style="width:100%" value="<?php echo $row['acct_code']; ?>"></td>
		<td><input type="text" name="factor<?php echo $x; ?>" style="width:100%" value="<?php echo $row['factor']; ?>"></td>
		<td><input type="text" name="days<?php echo $x; ?>" style="width:100%" value="<?php echo $row['days']; ?>"></td>
		<td><input type="text" name="min<?php echo $x; ?>" style="width:100%" value="<?php echo $row['min']; ?>"></td>
		<td><input type="button" value="..." OnClick="self.location='paycode.php?name=<?php echo $row['name']; ?>'"></td>
		<td><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['name']; ?>"></td>
	</tr>
	<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['name']; ?>">
	<input type="hidden" name="description<?php echo $x; ?>" value="<?php echo $row['description']; ?>">
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=6 align="left"><input type="submit" name="update" value="update"> | <input type="submit" name="delete" value="delete checked"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>