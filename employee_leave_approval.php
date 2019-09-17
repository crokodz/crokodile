<?php
$result = mysql_query("select *from users;", connect());

if (isset($_POST['app'])){
	$update = "update `employee_leave` set status = 'APPROVED' where id = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}
	
if (isset($_POST['del'])){
	$update = "update `employee_leave` set status = 'DISAPPROVED' where id = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}

?>
<h3 class="wintitle">Leave Approval</h3>
<form method="post">
<input type="hidden" name="id">
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=45% align="center">Leave Type</td>
		<td width=15% align="center">From</td>
		<td width=15% align="center">To</td>
		<td width=5% align="center">Days</td>
		<td width=10% align="center"></td>
		<td width=10% align="center"></td>
	</tr>
	
	<?php
	#$tree = GetTree($_SESSION['user']);
	#if ($tree[1] == 'Manager'){
		$select = "select tb1.`id`,tb1.`name`, tb1.`from`,tb1.`to`,tb1.`days` from employee_leave tb1 left join employee tb2 using (em_id) where tb1.`status` = 'FOR APPROVAL' and tb2.`manager` = '" . $_SESSION['user'] . "'";
		$result = mysql_query($select, connect());
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		?>
		<tr>
			<td><?php echo $row['name']; ?></td>
			<td><?php echo $row['from']; ?></td>
			<td><?php echo $row['to']; ?></td>
			<td><?php echo $row['days']; ?></td>
			<td><input type="submit" name="app" value="approved" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
			<td><input type="submit" name="del" value="delete" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
		</tr>	
		<?php
		}
	#}
	?>
</table>
</form>