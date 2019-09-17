<?php
if (isset($_POST['submit'])) {
	if (md5($_POST['oldpasswd']) == $_SESSION['pass']){
		if ($_POST['newpasswd'] == $_POST['confirmpasswd']){
			$update = "update users set `password` = md5('" . $_POST['newpasswd'] . "') where `username` = '" . $_SESSION['user'] . "'";
			mysql_query($update, connect());
			include "logout.php";
			}
		else{
			$message = "<center>New Password doesnt Match</center>";
			}
		}
	else{
		$message = "<center>Invalid Old Password</center>";
		}
	echo $message;
	}
?>
<h3 class="wintitle">Change Password</h3>
<form method="post">
<table width=100% border="0">
	<tr>
		<td width=15%>Old Password</td>
		<td width=20%><input type="password" name="oldpasswd" size="30" style="widht:100%;"></td>
		<td width=65%>&nbsp;</td>
	</tr>
	<tr>
		<td>New Password</td>
		<td><input type="password" name="newpasswd" size="30" style="widht:100%;"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Confirm Password</td>
		<td><input type="password" name="confirmpasswd" size="30" style="widht:100%;"></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right"><input type="submit" name="submit" value="Update"></td>
		<td>&nbsp;</td>
	</tr>
</table>	
</form>