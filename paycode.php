<?php
include "config.php";

function GetRate($id){
	$select = "select `name` from ot_rate where id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}

$select = "select * from pay where `name` = '" . $_GET['name'] . "' group by `name`";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$select1 = "select * from ot_rate";
$result1 = mysql_query($select1, connect());
while ($row1 = mysql_fetch_array($result1,MYSQL_ASSOC)){
	$select2 = "select `ot` from `pay` where `name` = '" . $_GET['name'] . "' and `ot` = '" . $row1['id'] . "'";
	$result2 = mysql_query($select2, connect());
	$row2 = mysql_fetch_array($result2,MYSQL_ASSOC);


	if (empty($row2['ot'])){
		$insert = "insert into `pay` (
			`id`,
			`name` ,
			`description`,
			`ot`,
			`reg_rate`,
			`ot_rate`,
			`ndl`,
			`ndl_ot`,
			`misc`
			)
			values (
			null ,
			'" . $row['name'] . "',
			'" . $row['description'] . "',
			'" . $row1['id'] . "',
			'0.00',
			'0.00',
			'0.00',
			'0.00',
			''
			)";
		mysql_query($insert, connect());
		}
	}

if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if($_POST['misc' . $x]){
			$misc = 'checked';
			}
		else{
			$misc = '';
			}
		$update = "update pay set
			reg_rate = '" . $_POST['reg_rate' . $x] . "',
			ot_rate = '" . $_POST['ot_rate' . $x] . "',
			ndl = '" . $_POST['ndl' . $x] . "',
			ndl_ot = '" . $_POST['ndl_ot' . $x] . "',
			misc = '" . $misc . "'
			where id = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<body id="innerframe">
<h3 class="wintitle">Pay Code</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10%>Name</td>
		<td width=90%><input type="text" name="name" style="width:50%" readonly="true" value="<?php echo $row['name']; ?>"></td>
	</tr>
	<tr>
		<td>Description</td>
		<td><input type="text" name="description" style="width:50%" readonly="true" value="<?php echo $row['description']; ?>"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=25%><b>Name</b></td>
		<td width=10%><b>Regular OT Rate</b></td>
		<td width=10%><b>Ot Rate</b></td>
		<td width=10%><b>NDL Rate</b></td>
		<td width=10%><b>NDL OT Rate</b></td>
		<td width=2%>Misc</td>
		<td>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from pay where `name` = '" . $_GET['name'] . "' order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" id="<?php echo $row['ot']; ?>" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><?php echo GetRate($row['ot']); ?></td>
		<td><input type="text" name="reg_rate<?php echo $x; ?>" style="width:50px" value="<?php echo $row['reg_rate']; ?>"></td>
		<td><input type="text" name="ot_rate<?php echo $x; ?>" style="width:50px" value="<?php echo $row['ot_rate']; ?>"></td>
		<td><input type="text" name="ndl<?php echo $x; ?>" style="width:50px" value="<?php echo $row['ndl']; ?>"></td>
		<td><input type="text" name="ndl_ot<?php echo $x; ?>" style="width:50px" value="<?php echo $row['ndl_ot']; ?>"></td>
		<td><input type="checkbox" name="misc<?php echo $x; ?>" <?php echo $row['misc']; ?>></td>
		<td>&nbsp;<input type="hidden" name="ndlot<?php echo $x; ?>" style="width:50px" value="<?php echo $row['ndlot']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=2 align="left"><input type="submit" name="update" value="update"></td>
	</tr>
</table>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>
</body>
