<?php
if (isset($_POST['export'])){
	$select = "select * from employee where company_id = '" . $_POST['company_id'] . "'";
	$result = mysql_query($select, connect()); 
	
	
	$myFile = $_POST['company_id'] . "_" . $now . ".zip";
	$fh = fopen($myFile, 'w') or die("can't open file");
	
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		$insert = "INSERT INTO `employee` (`em_id` ,`name` ,`em_address` ,`em_number` ,`birthdate` ,`gender` ,`civil_status` ,`citizenship` ,`contact_person` ,`cp_number` ,`cp_address` ,`position` ,`salary` ,`salary_based`,`department` ,`description`,`username`,`datetime`,`sss`,`pi`,`ph`,`tin`,`sssn`,`pin`,`phn`,`tinn`,`ts`,`race`,`employee_status`,`branch`,`status`,`allowed_late`,`date_employed`,`allowed_ot`,`allowed_ut`,`shift_code`,`company_id`,`pay_id`,`pin_number`)VALUES ('" . $row['em_id'] . "','" . $row['name'] . "','" . $row['em_address'] . "','" . $row['em_number'] . "','" . $row['birthdate'] . "','" . $row['gender'] . "','" . $row['civil_status'] . "','" . $row['citizenship'] . "','" . $row['contact_person'] . "','" . $row['cp_number'] . "','" . $row['cp_address'] . "','" . $row['position'] . "','" . $row['salary'] . "','" . $row['salary_based'] . "','" . $row['department'] . "','" . $row['description'] . "','" . $row['username'] . "','" . $row['datetime'] . "','" . $row['sss'] . "','" . $row['pi'] . "','" . $row['ph'] . "','" . $row['tin'] . "','" . $row['sssn'] . "','" . $row['pin'] . "','" . $row['phn'] . "','" . $row['tinn'] . "','" . $row['ts'] . "','" . $row['race'] . "','" . $row['employee_status'] . "','" . $row['branch'] . "','" . $row['status'] . "','" . $row['allowed_late'] . "','" . $row['date_employed'] . "','" . $row['allowed_ot'] . "','" . $row['allowed_ut'] . "','" . $row['shift_code'] . "','" . $row['company_id'] . "','" . $row['pay_id'] . "','" . $row['pin_number'] . "'); \n ";
		fwrite($fh, $insert);
		}
	fclose($fh);
	
	echo "<script>";
	echo "window.open('" . $myFile . "');";
	echo "</script>";
	}

?>
<h3 class="wintitle">Export Employee Information</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width="20%">Company</td>
		<td width="80%">
			<select style="width:30%" name="company_id" id="company_id">
			<?php
			$result_data = $result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>
			<?php
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=2><input type="submit" name="export" value="export"></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
</table>
</form>