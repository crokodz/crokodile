<?php
if(isset($_POST['updatea'])){
	$delete = "delete from employee_schedule where `em_id` = '" . $_POST['em_id'] . "' and `date` = '" . $_POST['date'] . "' ";
	mysql_query($delete, connect());
	$status = 'APPROVED';
	$insert = "insert into employee_schedule (`date`,`em_id`,`day_type`,`shift_code`, `status`,`approvedby`,`approveddate`,`time`) values ('" . $_POST['date'] . "','" . $_POST['em_id'] . "','" . $_POST['day_type'] . "','" . $_POST['shift_code'] . "', '" . $status . "','" . $_SESSION['user'] . "',curdate(),curtime())";
	mysql_query($insert, connect());
	}
	
if(isset($_POST['updated'])){
	$delete = "delete from employee_schedule where `em_id` = '" . $_POST['em_id'] . "' and `date` = '" . $_POST['date'] . "' ";
	mysql_query($delete, connect());
	$status = 'DIS-APPROVED';
	$insert = "insert into employee_schedule (`date`,`em_id`,`day_type`,`shift_code`, `status`,`approvedby`,`approveddate`,`time`) values ('" . $_POST['date'] . "','" . $_POST['em_id'] . "','" . $_POST['day_type'] . "','" . $_POST['shift_code'] . "', '" . $status . "','" . $_SESSION['user'] . "',curdate(),curtime())";
	mysql_query($insert, connect());
	}

$select = "select tb1.`day_type`,tb1.`date`,tb1.`status`, tb2.`name`, tb2.`em_id`, tb1.`shift_code`,tb1.`approvedby`,tb1.`approveddate` from employee_schedule tb1 left join employee tb2 using (`em_id`) where 
	(`day_type` = 'VACATION LEAVE' or `day_type` = 'SICK LEAVE' or 
	`day_type` = 'MATERNITY LEAVE' or `day_type` = 'PATERNITY LEAVE' or 
	`day_type` = 'BEREAVEMENT LEAVE'  or 
	`day_type` = 'BIRTHDAY LEAVE'   or 
	`day_type` = 'EMERGENCY LEAVE') order by tb1.date asc";
$result = mysql_query($select, connect());
$x = 0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$bgcolor="";
	if($row['status'] == 'APPROVED'){
		$bgcolor = "background-color:#e9e8a1;";
		}
	elseif($row['status'] == 'DIS-APPROVED'){
		$bgcolor = "background-color:#656565;color:#FFF";
		}
	?>
	<form method="POST">
	<div style="width:30s0px;border:1px solid #000;margin-top:2px;padding: 5px 5px 5px 5px;<?php echo $bgcolor; ?>">
	<span>
		<span style="width:100px;display:table-cell; "><?php echo $row['name']; ?></span>
		<span style="width:100px;display:table-cell; "><?php echo $row['date']; ?></span>
		<span style="width:150px;display:table-cell; "><?php echo $row['day_type']; ?></span>
		<span style="width:150px;display:table-cell; "><?php echo $row['status']; ?></span>
		<span style="width:150px;display:table-cell; "><?php echo $row['approvedby']; ?></span>
		<span style="width:100px;display:table-cell; "><?php echo $row['approveddate']; ?></span>
		<span style="width:200px;display:table-cell; ">
			<input type="submit" name="updatea" value="Approved" style="width:80px;">
			<input type="submit" name="updated" value="Dis-Appv." style="width:80px;">
		</span>
	</span>
	</div>
	<input type="hidden" name="em_id" value="<?php echo $row['em_id']; ?>">
	<input type="hidden" name="date" value="<?php echo $row['date']; ?>">
	<input type="hidden" name="day_type" value="<?php echo $row['day_type']; ?>">
	<input type="hidden" name="shift_code" value="<?php echo $row['shift_code']; ?>">
	</form>
	<?php
	$x++;
	}
	
if($x == 0){
	echo "No Leave/s Pending";
	}
?>