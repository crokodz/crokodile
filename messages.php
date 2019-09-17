<?php
$result = mysql_query("select *from users;", connect()); 

function get_user($name){
	$select = "select * from users where `username` = '" . $name . "'";
	$result_branch = mysql_query($select, connect());
	$row = mysql_fetch_array($result_branch,MYSQL_ASSOC);
	return $row['username'];
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['id' . $x]){
			$update = "update messages set status='deleted' where id = '" . $_POST['id' . $x] . "'";
			mysql_query($update, connect());
			}
		}
	}

if (isset($_POST['send'])){
	$name = get_user($_POST['to']);
	if ($name){
		if ($_POST['subject']){
			$insert = "insert into `messages` (
				`id` ,
				`datetime` ,
				`from` ,
				`to` ,
				`message` ,
				`subject` ,
				`status`
				)
				values (
				null,
				now(),
				'" . $_SESSION['user'] . "', 
				'" . $_POST['to'] . "',
				'" . $_POST['message'] . "',
				'" . $_POST['subject'] . "',
				'sent'
				);
				";
			mysql_query($insert, connect());
			}
		else{
			echo '<center><b>invalid subject...<b></center><br>';
			}
		}
	else{
		echo '<center><b>invalid recipient...<b></center><br>';
		}
	}

?>
<h3 class="wintitle">Messages</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=5% align="right">To</td>
		<td width=95%><input type="text" name="to" style="width:30%;"></td>
	</tr>
	<tr>
		<td align="right">Subject</td>
		<td><input type="text" name="subject" style="width:60%;"></td>
	</tr>
	<tr>
		<td align="right">Message</td>
		<td><textarea name="message" style="width:100%;height:150px"></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="send" value="send"></td>
	</tr>
</table>	
<br>
<table width=100% border="1" cellpadding="4" cellspacing="0">
	<tr>
		<td width=2%></td>
		<td width=68%>Subject/Preview</td>
		<td width=15%>From</td>
		<td width=15%>Received</td>
	</tr>
	<?php
	$select = "select * from messages where `to` = '" . $_SESSION['user'] . "' and status != 'deleted' order by id desc";
	$result = mysql_query($select, connect());
	$x = 0;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr <?php if ($row['status'] == 'sent'){ echo 'bgcolor="white"';} ?>>
		<td><input type="checkbox" name="id<?php echo $x; ?>" id="id" value="<?php echo $row['id']; ?>"></td>
		<td style="cursor:pointer;"  onclick="self.location='index.php?menu=20&id=<?php echo $row['id']; ?>';">&nbsp;&nbsp;<font color="orange"><b><?php echo $row['subject']; ?></b></font><br>&nbsp;&nbsp;<font color="gray"><?php echo $row['message']; ?></font></td>
		<td style="cursor:pointer;"  onclick="self.location='index.php?menu=20&id=<?php echo $row['id']; ?>';"><?php echo $row['from']; ?></td>
		<td style="cursor:pointer;"  onclick="self.location='index.php?menu=20&id=<?php echo $row['id']; ?>';"><?php echo $row['datetime']; ?></td>
	</tr>
	<?php	
	$x++;
	}
	?>
	<tr>
		<input type="hidden" name="count" value=<?php echo $x; ?>>
		<td colspan=4><input type="submit" name="delete" value="delete"></td>
	</tr>
</table>
</form>