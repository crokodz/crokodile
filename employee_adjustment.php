<?php
include "class.pager.php";

if(!$_GET['id']){
	$_GET['id'] = $_GET['idd'];
	}

function getpayment($id){
	$select = "select count(*) as total from posted where deduction_id = '" . $id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['total'];
	}
	
function getadjustments($em_id){
	$select = "select * from employee_adjustments where 
		em_id = '" . $em_id . "' and 
		status != 'deleted' 
		order by id";
	return mysql_query($select, connect());
	}


if(isset($_POST['delete'])){
	#$delete = "delete from employee_deduction where deduct_id = '" . $_POST['id'] . "'";
	$update = "update employee_adjustments set status = 'deleted', deleted = '" . $_SESSION['user'] . "', 		deleted_date = curdate() where id = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}


if(isset($_POST['save'])){
	$insert = "INSERT INTO `employee_adjustments` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`,
		`mins`,
		`remarks`,
		`date`,
		taxnontax
		)
		VALUES (
		NULL , 
		'" . $_POST['name'] . "', 
		'" . $_GET['id'] . "', 
		'" . $_POST['type'].$_POST['amount'] . "', 
		'0', 
		'pending', 
		'" . $_SESSION['user'] . "', 
		now(),
		'" . $_POST['mins'] . "', 
		'" . $_POST['remarks'] . "',
		'" . $_POST['date'] . "',
		'" . $_POST['taxnontax'] . "'
		)";
	mysql_query($insert, connect());
	}

$select = "select * from employee where status='active' and em_id = '" . $_GET['id'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
?>
<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />
<h3 class="wintitle">Employee Adjustments</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=20%>
		Serach : <input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			//$('name').value=myData[1];
			$('keyword').value=myData[1];
			self.location='index.php?menu=eadjustments&idd='+myData[0];
			}
		</script>
	</td>
</tr>
<tr>
	<td width=20%>Id Number : <b><?php echo $row['em_id']; ?></b></td>
</tr>
<tr>
	<td width=20%>Name : <b><?php echo $row['name']; ?></td>
</tr>
</table>
<br>
<h3 class="wintitle">Employee Adjustments</h3>
<?php
if($row['em_id']){
?>
<form method="post">

<input type="hidden" name="id">

<table width=100% border="0">
<tr>
	<td width="100px">Type : </td>
	<td>
		<select name="name" id="name" onchange="getSalary('<?php echo $row['em_id'];?>');">
		<option>UNDER TIME</option>
		<option>LATE</option>
		<option>ABSENT</option>
		<option>NIGHT DIFF</option>
		<option>EXTRA DAY</option>
		<option>OTHERS</option>
		</select> <select name="taxnontax" id="taxnontax">
		<option></option>
		<option>TAXABLE</option>
		<option>NON-TAXABLE</option>
		</select> 
	</td>
</tr>
<tr>
	<td>Date : </td>
	<td>
		<input type="text" id="date" name="date" style="width:80px" readonly> <input type="button" id="datebtn" value="...">
	</td>
</tr>
<tr>
	<td>Hours</td>
	<td>
		<input type="text" id="mins" name="mins" style="width:70px" maxlength="5" onkeydown="javascript:return maskTime(this,event.keyCode);" onkeyup="getSalary('<?php echo $row['em_id'];?>');"> <b>hh:mm</b>
	</td>
</tr>
<tr>
	<td>Amount : </td>
	<td>
		<select name="type">
			<option>+</option>
			<option>-</option>
		</select> | <input type="text" name="amount" id="amount">
	</td>
</tr>
<tr>
	<td>Remarks : </td>
	<td>
		<textarea name="remarks" id="remarks" style="width:300px;height:60px;"></textarea>
	</td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="save" value="save" onclick="return onSaveAdj();"></td>
</tr>
</table>
</form>


<!-- 
HIDDEN
-->
<form method="post">
<input type="hidden" name="id">

<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<tr>
	<td width=20% align="center">DateTime</td>
	<td width=47% align="center">Name</td>
	<td width=10% align="center">Amount</td>
	<td width=10% align="center">Posted ID</td>
	<td width=10% align="center">Status</td>
	<td width=3% align="center"></td>
</tr>
<?php
$result = getadjustments($_GET['id']);
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
<tr <?php if($row['status'] == 'posted'){ echo 'bgcolor="lightblue"'; } ?>>
	<td><?php echo $row['datetime']; ?></td>
	<td><?php echo $row['name']; ?></td>
	<td><?php echo roundoff($row['amount'],2); ?></td>
	<td><?php echo $row['posted_id']; ?></td>
	<td align="center"><b><?php echo $row['status']; ?></b></td>
	<td><input type="submit" name="delete" value="del" onClick="deleteID(this.form.id,<?php echo $row['id']; ?>);" <?php if($row['status'] == 'posted'){ echo 'disabled'; } ?>></td>
</tr>
<?php
}
?>
</table>
<?php
}
?>
</form>
<script type="text/javascript">
	var cal = Calendar.setup({
		onSelect: function(cal) { cal.hide() },
		showTime: true
		});
	cal.manageFields("datebtn", "date", "%Y-%m-%d");
</script>