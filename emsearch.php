<?php
include "config.php";
if ($_SESSION['company'] == '0'){
	$select = "select * from employee where status='active' and file_status != 'SEPARATED' and (em_id = '" . $_GET['search'] . "' or name like '%" . $_GET['search'] . "%') limit 50;";
	$result = mysql_query($select, connect());
	}
else{
	$select = "select * from employee where status='active' and company_id = '" . $_SESSION['company'] . "' and (em_id = '" . $_GET['search'] . "' or name like '%" . $_GET['search'] . "%') limit 50;";
	$result = mysql_query($select, connect());
	}
?>
<link rel="stylesheet" type="text/css" href="style/v31.css">
<script type="text/javascript" src="js/main.js"></script>
<form method="post">
<table width=100% border="1" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=10% align="center" ><b>id</b></td>
		<td width=50% align="center"><b>name</b></td>
		<td width=17% align="center"><b>department</b></td>
		<td width=20% align="center"><b>contact</b></td>
		<td width=3% align="center"><b>token</b></td>
	</tr>
<?php
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;" onclick="PutID('<?php echo $row['em_id']; ?>')">
		<td align="left"><?php echo $row['em_id']; ?></td>
		<td align="left"><?php echo $row['name']; ?></td>
		<td align="left"><?php echo $row['department']; ?></td>
		<td align="left"><?php echo $row['em_number']; ?></td>
		<td align="left"><?php echo $row['company_id']; ?></td>
	</tr>
	<?php
	}
?>
</table>
</form>