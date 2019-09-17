<?php
if (isset($_POST['add'])){
	$exp_date = $_POST['debdyy'] . "-" . $_POST['debdmm'] . "-" . $_POST['debddd'];
	$insert = "insert into employee_license values (NULL,'" . $_GET['id'] . "','" . $_POST['name'] . "','" . $_POST['remarks'] . "', '" . $exp_date . "')";
	mysql_query($insert, connect());
	}

if (isset($_POST['del'])){
	$delete = "delete from employee_license where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}
	
function get_date($id,$date){
	$date = explode("-", $date);
	if ($id == 0){
		return $date[0];
		}
	if ($id == 1){
		return $date[1];
		}
	if ($id == 2){
		return $date[2];
		}			
	}
	
$dd = '';
for ($x=1; $x < 32; $x++){
	$dd = $dd . "<option>" . $x . "</option>";
	}
	
$yy = '';
for ($x=2010; $x < 2020; $x++){
	$yy = $yy . "<option>" . $x . "</option>";
	}

$mm = '';
for ($x=1; $x < 13; $x++){
	$mm = $mm . "<option>" . $x . "</option>";
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="lightyellow">
	<tr>
		<td colspan=3 align="left">LICENSE</td>
	</tr>
	<tr>
		<td>Title</td>
		<td>Expiration</td>
		<td colspan=2>Description</td>
	</tr>
	<tr>
		<td width=25% align="right">
			<select style="width:100%" name="name">
			<?php
			$select = "select * from licenses";
			$result = mysql_query($select, connect());
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			?>
			<option><?php echo $row['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td width=20% align="right">
			<select name="debdyy"><option><?php echo get_date(0,$row['date_employed']); ?></option><?php echo $yy; ?></select>
			<select name="debdmm"><option><?php echo get_date(1,$row['date_employed']); ?></option><?php echo $mm; ?></select>
			<select name="debddd"><option><?php echo get_date(2,$row['date_employed']); ?></option><?php echo $dd; ?></select>
		</td>
		<td width=50% align="right"><input type="text" name="remarks" style="width:100%"></td>
		<td width=5% align="right"><input type="submit" name="add" value="add" style="width:100%"></td>
	</tr>
	<?php
	$select = "select * from employee_license where em_id = '" . $_GET['id'] . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['exp_date']; ?></td>
		<td><?php echo $row['description']; ?></td>
		<td><input type="submit" name="del" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>