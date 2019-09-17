<?php
include "config.php";
?>
<table width=100% border="0" cellpadding="4" cellspacing="0">
<?php
$select = "select other_tax_inc,em_id,name from posted_summary join employee using(em_id) where posted_id = '725' ";
$result = mysql_query($select, connect());
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$select1 = "select sum(`amount`) as amt from employee_taxable where posted_id = '725' and em_id = '" . $row['em_id'] . "' and status = 'posted' ";
	$result1 = mysql_query($select1, connect());
	$row1 = mysql_fetch_array($result1,MYSQL_ASSOC);
	$ss = $row1['amt'] - $row['other_tax_inc'];
	//~ if($row1['amt'] != $row['other_tax_inc']){
		//~ $
		//~ }
	?>
	<tr>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['em_id']; ?></td>
		<td><?php echo $ss; ?></td>
	</tr>
	<?php
	$x++;
	}
	?>
</table>
