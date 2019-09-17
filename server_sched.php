<?php
include "config.php";
$em_id = $_GET['em_id'];
$date = $_GET['date'];
$select = "select shift_code, day_type from employee_schedule where `em_id` = '" . $em_id . "' and `date` = '" . $date . "' ";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
?>
<form method="POST">
<table style="width:100%;">
<tr>
<td>
	Date : <?php echo $_GET['date']; ?>
</td>
</tr>
<tr>
<td>
<select name="day_type" style="width:100%;">
	<?php
	$select = "select * from `ot_rate`";
	$result_data = mysql_query($select, connect());
	while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
	?>
	<option <?php if ($row['day_type'] == $data['name']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
	<?php
	}
	?>
</select>
</td>
</tr>
<tr>
<td>
	<select name="shift_code" style="width:100%;">
		<?php
		$select_shift = "select * from shift";
		$result_shift = mysql_query($select_shift, connect());
		while ($shift = mysql_fetch_array($result_shift,MYSQL_ASSOC)){
		?>
		<option <?php if ($row['shift_code'] == $shift['shift_code']){ echo 'selected'; } ?>><?php echo $shift['shift_code']; ?></option>
		<?php	
		}
		?>
		<option>0000</option>
	</select>
</td>
</tr>
<tr>
<td align="right">
	<input type="hidden" name="em_id" value="<?php echo $_GET['em_id']?>">
	<input type="hidden" name="date" value="<?php echo $_GET['date']?>">
	<input type="submit" value="Save" name="save">
</td>
</tr>
</table>
</form>