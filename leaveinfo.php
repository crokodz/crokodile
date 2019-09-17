<?php
$select = "select `day_type`,`date`,`status` from employee_schedule where 
	`em_id` = '" . $_SESSION['user'] . "' and 
	(`day_type` = 'VACATION LEAVE' or `day_type` = 'SICK LEAVE' or 
	`day_type` = 'MATERNITY LEAVE' or `day_type` = 'PATERNITY LEAVE' or 
	`day_type` = 'BEREAVEMENT LEAVE'  or 
	`day_type` = 'BIRTHDAY LEAVE'   or 
	`day_type` = 'EMERGENCY LEAVE')";
$result = mysql_query($select, connect());
$x = 0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$bgcolor="";
	if($row['status'] == 'DIS-APPROVED'){
		$bgcolor = "background-color:#656565;color:#FFF";
		}
	elseif($row['status'] == 'APPROVED'){
		$bgcolor = "background-color:#e9e8a1;";
		}
	?>
	<div style="width:30s0px;border:1px solid #000;margin-top:2px;padding: 5px 5px 5px 5px;<?php echo $bgcolor;?>">
	<span>
		<span style="width:150px;display:table-cell; "><?php echo $row['date']; ?></span>
		<span style="width:150px;display:table-cell; "><?php echo $row['day_type']; ?></span>
		<span style="width:150px;display:table-cell; "><?php echo $row['status']; ?></span>
	</span>
	</div>
	<?php
	$x++;
	}
	
if($x == 0){
	echo "You dont have any Leave/s";
	}
?>