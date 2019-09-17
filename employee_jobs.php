<?php
if (isset($_POST['update'])){
	$delete = "delete from employee_jobs where em_id = '" . $_GET['id'] . "'";
	mysql_query($delete, connect());
	
	$desc = $_POST['description'] . "\n\nedited by " . $_SESSION['user'] . " - " . time();
	
	$insert = "insert into employee_jobs values (NULL,'" . $_GET['id'] . "','job description','" . $desc . "')";
	mysql_query($insert, connect());
	}
	
function get_description($id){
	$select = "select description from employee_jobs where em_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['description'];
	}
?>
<h3 class="wintitle">Employee Data Entry</h3>
<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="orange">
	<tr>
		<td align="left">JOB Description</td>
	</tr>
	<tr>
		<td align="left"><textarea name="description" style="width:100%;height:300px;"><?php echo get_description($_GET['id']); ?></textarea></td>
	</tr>
	<tr>
		<td align="right"><input type="submit" name="update" value="update"></td>
	</tr>
</table>
</form>