<h3 class="wintitle">Employee Data Entry</h3>
<form method="post">

<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />

<input type="hidden" name="id">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC6699">
	<tr>
		<td align="left">LATES INFORMATION</td>
	</tr>
</table>
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all" bgcolor="#CC6699">
	<tr>
		<td width=100px align="center">Date</td>
		<td width=100px align="center">Shift Code</td>
		<td width=100px align="center">Time In</td>
		<td width=100px align="center">Time Out</td>
		<td width=60px align="center">Min.</td>
		<td align="center">Tagged By</td>
	</tr>
	
	<?php
	$select = "select * from transaction where em_id = '" . $_GET['id'] . "' and late > 0 order by trxn_date desc";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	?>
	<tr>
		<td><?php echo $row['trxn_date']; ?></td>
		<td><?php echo $row['shift_code']; ?></td>
		<td><?php echo $row['trxn_time_in']; ?></td>
		<td><?php echo $row['trxn_time_out']; ?></td>
		<td><?php echo $row['late']; ?></td>
		<td><?php echo $row['username']; ?></td>
	</tr>	
	<?php
	}
	?>
</table>
</form>