<?php
$result = mysql_query("select *from users;", connect());

$select = "select * from messages where id ='" . $_GET['id'] . "'";
$result = mysql_query($select, connect());
$mess = mysql_fetch_array($result,MYSQL_ASSOC);

$update = "update messages set status = 'read' where id = '" . $_GET['id'] . "'";
mysql_query($update, connect());

$space = "\n\n\n\n\nwrite by : " . $mess['from'] . "   datetime : " . $mess['datetime'] . "\n\n";

function get_user($name){
	$select = "select * from users where `username` = '" . $name . "'";
	$result_branch = mysql_query($select, connect());
	$row = mysql_fetch_array($result_branch,MYSQL_ASSOC);
	return $row['username'];
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
<h3 class="wintitle">Message Reply</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=5% align="right">To</td>
		<td width=95%><input type="text" name="to" style="width:30%;" value="<?php echo $mess['from']; ?>"></td>
	</tr>
	<tr>
		<td align="right">Subject</td>
		<td><input type="text" name="subject" style="width:60%;" value="<?php echo $mess['subject']; ?>"></td>
	</tr>
	<tr>
		<td align="right">Message</td>
		<td><textarea name="message" style="width:100%;height:150px"><?php echo  $space . $mess['message']; ?></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="send" value="send"></td>
	</tr>
</table>	
<br>
</form>