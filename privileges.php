<?php
function get_username($username){
	$select = "select count(*) as total from users where username = '" . $username . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['total'];
	}

if (isset($_POST['search'])){
	$_SESSION['keyword'] = $_POST['keyword'];
	}

if (isset($_POST['save'])){
	$delete = "delete from user_privileges where username = '" . $_POST['username'] . "'";
	mysql_query($delete, connect());
	
	for ($x=0;$x < $_POST['count'];$x++){
		if ($_POST['id'.$x]){
			$insert = "insert into user_privileges values (null,'" . $_POST['username'] . "','" . $_POST['menu'.$x] . "','','','')";
			mysql_query($insert, connect());
			}
		}
	}
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<h3 class="wintitle">Privileges</h3>
<form method="post" name="privileges">
<table width=100% border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan=2><input type="text" name="keyword" id="keyword" size=27 value="<?php echo $_SESSION['keyword']; ?>"> | <input type="submit" name="search" value="search"></td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<?php
	if ($_SESSION['keyword']){
		
	if (get_username($_SESSION['keyword']) > 0){
	
	$select = "select * from `privileges` order by `cat` asc";
	$result = mysql_query($select, connect());
	$x=0;
	$cat = "";
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$menu=get_privileges($_SESSION['keyword'],$row['menu']);
	if($row['cat'] != $cat){
	?>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2><b><?php echo $row['cat']; ?></b></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<input type="hidden" name="menu<?php echo $x; ?>" value="<?php echo $row['menu']; ?>">
		<td width=1%><input type="checkbox" name="id<?php echo $x; ?>" <?php if ($menu > 0) { echo 'checked'; }?>></td>
		<td width=99% onclick="privileges.id<?php echo $x; ?>.click();" style="cursor:pointer;"><?php echo $row['description']; ?></td>
	</tr>
	<?php
	$x++;
	$cat = $row['cat'];
	}
	?>
	<tr>
		<input type="hidden" name="username" value="<?php echo $_SESSION['keyword']; ?>">
		<input type="hidden" name="count" value="<?php echo $x; ?>">
		<td colspan=2><input type="submit" name="save" value="save"></td>
	</tr>
	<?php
	}
	}
	?>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>
</form>