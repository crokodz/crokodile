<h3 class="wintitle">Employee Data Entry</h3>
<form method="post">

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC9933">
	<tr>
		<td align="left">ABSENCES INFORMATION</td>
	</tr>
</table>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC9933">
	<tr>
		<td width=20% align="center">Date</td>
		<td width=80% align="center">Tagged By</td>
	</tr>
	
	<?php
	$select = "select * from transaction where em_id = '" . $_GET['id'] . "' and status = 'ABSENT' order by trxn_date desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['username']; ?></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>