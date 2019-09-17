<?php
if ($_SESSION['level'] == 'Super Administrator'){
	$admincompany = "<option>ALL</option>";
	}

if (isset($_POST['save'])){
	$insert = "insert into `users` (
		`id`,
		`username` ,
		`password`, 
		`realname`,
		`pay_id`,
		`company`
		)
		values (
		null , 
		'" . $_POST['username'] . "',
		md5('" . $_POST['password'] . "'),
		'" . $_POST['realname'] . "',
		'" . $_POST['pay_id'] . "',
		'" . $_POST['company'] . "'
		)";
	mysql_query($insert, connect());
	}
	
if (isset($_POST['update'])){
	for ($x=0; $x < $_POST['count']; $x++){
		$update = "update `users` set 
			`username` = '" . $_POST['username' . $x] . "',
			`realname` = '" . $_POST['realname' . $x] . "',
			`pay_id` = '" . $_POST['pay_id' . $x] . "',
			`company` = '" . $_POST['company' . $x] . "'
			where `id` = '" . $_POST['id' . $x] . "'
			";
		mysql_query($update, connect());
		}
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['idbox' . $x]){
			$delete = "delete from `users` where id = '" . $_POST['idbox' . $x] . "'";
			mysql_query($delete, connect());
			}
		}
	}
$pwd = "";
if (isset($_POST['reset'])){
	$pwd = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 10);
	$update = "update users set `password` = md5('" . $pwd . "') where `id` = '" . $_POST['idselected'] . "'";
	mysql_query($update, connect());
}

?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>

<script>
function conx(id) {
  var txt;
  var r = confirm("Are you sure want to continue?");
  if (r == true) {
  	document.getElementById("idselected").value = id;
    return true;
  } else {
    return false;
  }
}
</script>




<h3 class="wintitle">Users</h3>
<form method="post" autocomplete="off">
<table width=100%  border=0>
	<tr>
		<td width=100px>User Name</td>
		<td><input type="text" name="username" style="width:100px:"></td>
	</tr>
	<tr>
		<td width=100px>Password</td>
		<td><input type="password" name="password" style="width:100px;"></td>
	</tr>
	<tr>
		<td width=100px>Real Name</td>
		<td><input type="text" name="realname" style="width:200px;"></td>
	</tr>
	<tr>
		<td width=100px>Company</td>
		<td>
			<select style="width:250px;" name="company">
			<?php
			echo $admincompany;
			$select = "select * from company where status = 'active'";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>" <?php if($data['id'] == $row['company']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td width=100px>Pay Code</td>
		<td>
			<select style="width:250px;" name="pay_id">
			<option value="">ALL</option>
			<?php
			$select = "select * from pay group by name";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=2><input type="submit" name="save" value="save"></td>
	</tr>
</table>
<br>
<table width=100% border=0>
	<tr>
		<td width=100px>Name</td>
		<td width=100px>Real Name</td>
		<td width=100px>Company</td>
		<td width=100px>Pay Code</td>
		<td width=10px>&nbsp;</td>
		<td width=100px>&nbsp;</td>
	</tr>
	<?php
	$x = 0;
	$select = "select * from users where username != 'root' order by id desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['id']; ?>">
		<td><input readonly type="text" name="username<?php echo $x; ?>" style="width:100%" value="<?php echo $row['username']; ?>"></td>
		<td><input type="text" name="realname<?php echo $x; ?>" style="width:100%" value="<?php echo $row['realname']; ?>"></td>
		<td>
			<select style="width:100%" name="company<?php echo $x; ?>">
			<option value="" <?php if($row['company'] == '0'){ echo 'selected'; } ?>>ALL</option>
			<?php
			echo $admincompany;
			$select = "select * from company where status = 'active'";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>" <?php if($data['id'] == $row['company']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td>
			<select style="width:100%;" name="pay_id<?php echo $x; ?>">
			<option value="" <?php if($row['pay_id'] == ''){ echo 'selected'; } ?>>ALL</option>
			<?php
			$select = "select * from pay group by name";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option <?php if($row['pay_id'] == $data['name']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
		<td><input type="checkbox" name="idbox<?php echo $x; ?>" value="<?php echo $row['id']; ?>"></td>
		<td><input type="submit" name="reset" onclick="return conx(<?php echo $row['id']; ?>)" value="Reset Password"></td>
	</tr>
	<?php
	$x++;
	}
	?>
	<input type="hidden" name="count" value ="<?php echo $x; ?>">
	<tr>
		<td colspan=4 align="right"><input type="submit" name="update" value="update"> | <input type="submit" name="delete" value="delete checked"></td>
	</tr>
</table>
<input type="hidden" name="idselected" id="idselected">
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
</form>
<?php
if($pwd){
	?>
	<script>
	alert("Password: <?php echo $pwd; ?>");
	</script>
	<?php
}
?>