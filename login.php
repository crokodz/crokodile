<?php
if (isset($_POST['nlogin'])){
	if ($_POST['user'] && $_POST['pass']) {
		$select = "select `id`, `username`, `password`, em_id, level,company, pay_id from users where `username`='" . $_POST['user'] . "' and `password`=md5('" . $_POST['pass'] . "')";
		$result = mysql_query($select, connect());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		if ($row['id'] > 0) {
			$_SESSION['admin'] = true;
			$_SESSION['login'] = true;
			$_SESSION['user'] = $row['username'];
			$_SESSION['pass'] = $row['password'];
			$_SESSION['em_id'] = $row['em_id'];
			$_SESSION['level'] = $row['level'];
			$_SESSION['company'] = $row['company'];
			$_SESSION['pay_id'] = $row['pay_id'];
			$_SESSION['language'] = "";
			refresh();
			}
		else{
			$select = "select `em_id`, `pin_number`, `name`, `company_id`, pay_id from `employee` where `em_id`='" . $_POST['user'] . "' and `pin_number`= '" . $_POST['pass'] . "'";
			$result = mysql_query($select, connect());
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			if ($row['em_id']) {
				$_SESSION['admin'] = false;
				$_SESSION['login'] = true;
				$_SESSION['user'] = $row['em_id'];
				$_SESSION['pass'] = $row['pin_number'];
				$_SESSION['em_id'] = $row['em_id'];
				$_SESSION['level'] = 'employee';
				$_SESSION['company'] = $row['company_id'];
				$_SESSION['language'] = "";
				$_SESSION['name'] = $row['name'];
				$_SESSION['pay_id'] = $row['pay_id'];
				refresh();
				}
			else{
				$_SESSION['login'] = false;
				$_SESSION['key'] = substr(md5(time()), 0, 6);
				$_SESSION['attemp']++;
				echo "<script>";
				//echo "window.location='index.php?menu=29';";
				echo "</script>";
				}
			}
		}
	}
		
if (isset($_POST['alogin'])){
	if ($_POST['user'] && $_POST['pass'] && $_POST['key']) {
		if($_SESSION['key'] == $_POST['key']){
			$select = "select `id`, `username`, `password`, em_id, level,company, pay_id from users where `username`='" . $_POST['user'] . "' and `password`=md5('" . $_POST['pass'] . "')";
			$result = mysql_query($select, connect());
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			if ($row['id'] > 0) {
				$_SESSION['admin'] = true;
				$_SESSION['login'] = true;
				$_SESSION['user'] = $row['username'];
				$_SESSION['pass'] = $row['password'];
				$_SESSION['em_id'] = $row['em_id'];
				$_SESSION['level'] = $row['level'];
				$_SESSION['company'] = $row['company'];
				$_SESSION['language'] = "";
				$_SESSION['pay_id'] = $row['pay_id'];
				refresh();
				}
			else{
				$_SESSION['login'] = false;
				$_SESSION['attemp']++;
				echo "<script>";
				echo "window.location='index.php?menu=29';";
				echo "</script>";
				}
			}
		else{
			$_SESSION['key'] = substr(md5(time()), 0, 6);
			$_SESSION['attemp']++;
			echo "<script>";
			echo "window.location='index.php?menu=29';";
			echo "</script>";
			}
		}
	else{
		$_SESSION['key'] = substr(md5(time()), 0, 6);
		$_SESSION['attemp']++;
		echo "<script>";
		echo "window.location='index.php?menu=29';";
		echo "</script>";
		}
	}
?>
<h3 class="wintitle">Login</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=8% align="left">ID No.: </td>
		<td width=92% align="left"><input type="text" name="user" size=15 autocomplete="off"></td>
	</tr>
	<tr>
		<td align="left">Pin: </td>
		<td align="left"><input type="password" name="pass" size=15></td>
	</tr>
	<?php
	if ($_SESSION['attemp'] > 3){
		?>
		<tr>
			<td>Cnfr Code:</td>
			<td><input type="text" name="key" size=15></td>
		</tr>
		<tr>
			<td align="left" colspan=2><input type="submit" value="Login" name="alogin"></td>
		</tr>
		<tr>
			<td colspan=2 align="left"><img src="img.php"></td>
		</tr>
		<?php
		}
	else{
		?>
		<tr>
			<td align="left" colspan=2><input type="submit" value="Login" name="nlogin"></td>
		</tr>
		<?php
		}
	?>
 </table>	
</form>
