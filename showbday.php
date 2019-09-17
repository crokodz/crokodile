<?php
include "config.php";
?>
<table>
<?php
$select = "select `name`, `birthdate`, `pay_id`, `employee_status` from employee where  `birthdate` like '%" . $_GET['date'] . "' and status = 'active' order by `birthdate` asc ";
$result = mysql_query($select, connect());
$x = 0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	if($x == 0){
		?>
		<tr>
			<td style="font-weight:bold;padding:4px;width:200px;">Name</td>
			<td style="font-weight:bold;padding:4px;">Pay Code</td>
			<td style="font-weight:bold;padding:4px;width:100px;">Status</td>
		</tr>
		<?php
		}
	?>
	<tr>
		<td style="padding:4px;"><?php echo $row['name']; ?></td>
		<td style="padding:4px;"><?php echo $row['pay_id']; ?></td>
		<td style="padding:4px;"><?php echo $row['employee_status']; ?></td>
	</tr>
	<?php
	$x++;
	}
if($x == 0){
	echo "<center><b>No Record Found</b></center>";
	}
?>
</table>
