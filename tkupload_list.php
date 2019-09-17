<?php
function get_company($id){
	$select = "select name from company where id = '" . $id . "'";
	$result = mysql_query($select,connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['name'];
	}
	
if (isset($_POST['cancel'])){
	$update = "update upload_timekeeping set 
		status = 'uploaded'
		where id = '" . $_POST['id'] . "'
		";
	mysql_query($update, connect());
	}	
	
if (isset($_POST['denied'])){
	$update = "update upload_timekeeping set 
		status = 'denied'
		where id = '" . $_POST['id'] . "'
		";
	mysql_query($update, connect());
	}

if (isset($_POST['accept'])){
	$update = "update upload_timekeeping set 
		status = 'accept'
		where id = '" . $_POST['id'] . "'
		";
	mysql_query($update, connect());
	#$fp = fopen('timecards/' . $_POST['id'] . 'export.blu','r');
	#$file = fread($fp,999999);
	#fclose($fp);
	#mysql_query($file, connect());
	}	
?>

<h3 class="wintitle">Uploaded Time Keeping</h3>
<form method="post">
<input type="hidden" name="id">

<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="lightblue">
	<tr>
		<td width=20% align="left">Company</td>
		<td width=10% align="left">Date Uploaded</td>
		<td width=10% align="left">Pay Date</td>
		<td width=25% align="left">Filename</td>
		<td width=10% align="left">From</td>
		<td width=25% align="left" colspan=3>To</td>
	</tr>
	<?php
	$select = "select * from upload_timekeeping order by `date` desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><b><?php echo get_company($row['company_id']); ?></b></td>
		<td><?php echo $row['date']; ?></td>
		<td><?php echo $row['paydate']; ?></td>
		<td><?php echo $row['filename']; ?></td>
		<td><?php echo $row['from']; ?></td>
		<td><?php echo $row['to']; ?></td>
		<?php
		if ($row['status'] == "uploaded"){
		?>
		<td width=8%><input type="submit" name="denied" value="denied" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
		<td width=8%><input type="button" name="view" value="view" style="width:100%" onclick="openwindow('view_timekeeping.php?id=<?php echo $row['id']; ?>',400,400)"></td>
		<?php
		}
		else{
		?>
		<td width=8% align="center"><b><?php echo $row['status']; ?></b></td>
		<td width=8%><input type="submit" name="cancel" value="cancel" style="width:100%" onclick="this.form.id.value=<?php echo $row['id']; ?>"></td>
		<?php
		}
		?>
	</tr>	
	<?php
	}
	?>
</table>	
</form>