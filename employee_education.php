<?php
if (isset($_POST['add'])){
	$insert = "insert into employee_education values (NULL,'" . $_GET['id'] . "','" . $_POST['name'] . "','" . $_POST['remarks'] . "')";
	mysql_query($insert, connect());
	}

if (isset($_POST['del'])){
	$delete = "delete from employee_education where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="pink">
	<tr>
		<td colspan=3 align="left">EDUCATION</td>
	</tr>
	<tr>
		<td>Title</td>
		<td colspan=2>Description</td>
	</tr>
	<tr>
		<td width=25% align="right">
			<select style="width:100%" name="name">
			<?php
			$select = "select * from educations";
			$result = mysql_query($select, connect());
			while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			?>
			<option><?php echo $row['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td width=70% align="right"><input type="text" name="remarks" style="width:100%"></td>
		<td width=5% align="right"><input type="submit" name="add" value="add" style="width:100%"></td>
	</tr>
	<?php
	$select = "select * from employee_education where em_id = '" . $_GET['id'] . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['description']; ?></td>
		<td><input type="submit" name="del" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>