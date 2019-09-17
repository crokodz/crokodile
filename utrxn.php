<?php
if(isset($_POST['save'])){
	$select = "select `salary`, `salary_based`, `em_id` from employee";
	$result = mysql_query($select, connect());
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
		if($_POST['salary']){
			$update = "update transaction set `salary` = '" . $row['salary'] . "' where `em_id` = '" . $row['em_id'] . "'  and `trxn_date` between '" . $_POST['date1'] . "' and '" . $_POST['date2'] . "' ";
			mysql_query($update, connect());
			}
		if($_POST['salary_based']){
			$update = "update transaction set `salary_based` = '" . $row['salary_based'] . "' where `em_id` = '" . $row['em_id'] . "'  and `trxn_date` between '" . $_POST['date1'] . "' and '" . $_POST['date2'] . "' ";
			mysql_query($update, connect());
			}
		}
	echo "Successfully Updated";
	}

?>

<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />
<h3 class="wintitle">Update Employee from current employee information to Past Transaction</h3>
<form method="post" autocomplete="off">
<form method="post">

<input type="hidden" name="id">

<table width=100% border="0">
<tr>
	<td width=80px >From Date : </td>
	<td>
		<input type="text" id="date1" name="date1" style="width:80px" readonly> <input type="button" id="datebtn1" value="...">
	</td>
</tr>
<tr>
	<td>To Date : </td>
	<td>
		<input type="text" id="date2" name="date2" style="width:80px" readonly> <input type="button" id="datebtn2" value="...">
	</td>
</tr>
<tr>
	<td>Type</td>
	<td>
		<input type="checkbox" id="salary" name="salary"> Salary <br>
		<input type="checkbox" id="salary_based" name="salary_based"> Salary Based <br>
	</td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="save" value="save" onclick="return onSaveUTrxn();"></td>
</tr>
</table>
</form>

<script type="text/javascript">
	var cal = Calendar.setup({
		onSelect: function(cal) { cal.hide() },
		showTime: true
		});
	cal.manageFields("datebtn1", "date1", "%Y-%m-%d");
	cal.manageFields("datebtn2", "date2", "%Y-%m-%d");
</script>