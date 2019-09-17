<h3 class="wintitle">Lates Summary Detailed</h3>


<?php
$select = "select * from employee where em_id = '" . $_GET['em_id'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$name = $row['lname'] . ", " . $row['fname'] . " " . $row['mname'];
?>


<form method="post">

<input type="hidden" name="id">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td align="left" width=80>Name</td>
		<td align="left"><b><?php echo $name; ?></b></td>
	</tr>
	<tr>
		<td align="left">ID</td>
		<td align="left"><?php echo $row['em_id']; ?></td>
	</tr>
	<tr>
		<td align="left">Pay Code</td>
		<td align="left"><?php echo $row['pay_id']; ?></td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="left">&nbsp;</td>
	</tr>
</table>
<br>
<br>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=100px align="center">Date</td>
		<td width=400px align="center">Remarks</td>
		<td align="center">&nbsp;</td>
	</tr>
	
	<?php
	$select = "select trxn_date, otremarks from transaction left join employee using (`em_id`) where trxn_date between '" . $_GET['fdate'] . "' and '" . $_GET['tdate'] . "' and (`transaction`.status = 'ABSENT' or `transaction`.status = 'UNFILED' or `transaction`.status = 'LWOP' or `transaction`.status = 'SUS' or `transaction`.status = 'AWOL' or `transaction`.status = 'NO WORK') and employee.status = 'active' and transaction.em_id = '" . $_GET['em_id'] . "' group by trxn_date order by trxn_date desc" ;
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['otremarks']; ?></td>
		<td>&nbsp;</td>
	</tr>	
	<?php
	}
	?>
</table>
</form>