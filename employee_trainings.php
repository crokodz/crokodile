<?php
if (isset($_POST['madd'])){
	$insert = "insert into employee_trainings values (NULL,'" . $_GET['id'] . "','" . $_POST['name'] . "','" . $_POST['description'] . "','" . $_POST['date'] . "')";
	mysql_query($insert, connect());
	}

if (isset($_POST['mdel'])){
	$delete = "delete from employee_trainings where id = '" . $_POST['id'] . "'";
	mysql_query($delete, connect());
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="yellow">
	<tr>
		<td colspan=4 align="left">Trainings</td>
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
		<td width=5% align="right"><input type="submit" name="madd" value="add" style="width:100%"></td>
	</tr>
	<?php
	$select = "select * from employee_trainings where em_id = '" . $_GET['id'] . "'";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['description']; ?></td>
		<td><?php echo $row['date']; ?></td>
		<td><input type="submit" name="mdel" value="del" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>