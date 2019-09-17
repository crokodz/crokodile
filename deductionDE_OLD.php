<?php
include "class.pager.php";

$_GET['id'] = $_GET['idd'];
	
function getdeduction($em_id){
	$select = "select `name`, sum(`amount`) as `amt`, `terms` from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status != 'deleted'  group by `name`
		order by name asc";
	return mysql_query($select, connect());
	}
	
function getdeductionposted($em_id,$name){
	$select = "select sum(`amount`) as `posted` from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status = 'posted' and
		name = '" . $name . "'
		group by `name` ";
	$result =  mysql_query($select, connect());
	return mysql_fetch_array($result,MYSQL_ASSOC);
	}
	
function getdeductionpending($em_id,$name){
	$select = "select sum(`amount`) as `pending`, count(*) as cnt from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status = 'pending'  and
		`name` = '" . $name . "'
		group by `name` ";
	$result =  mysql_query($select, connect());
	return mysql_fetch_array($result,MYSQL_ASSOC);
	}


if(isset($_POST['delete'])){
	$update = "update employee_deduction set status = 'deleted', deleted = '" . $_SESSION['user'] . "', deleted_date = curdate() where `name` = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}


if(isset($_POST['add'])){
	$amount = $_POST['amount']/$_POST['terms'];
	$balance = $_POST['amount'];
	for ($x=0;$x<$_POST['terms'];$x++){
		$balance = $balance - roundoff($amount,4);
		
		$insert = "insert into employee_deduction (
			`em_id`,
			`name`,
			`amount`,
			`balance`,
			`status`,
			`username`,
			`date`,
			`terms`,
			`datetime`) values (
			'" . $_GET['id'] . "',
			'" . $_POST['type'] . "',
			'" . $amount . "',
			'" . $balance . "',
			'pending',
			'" . $_SESSION['user'] . "',
			curdate(),
			'" . $_POST['terms'] . "',
			now()
			);";
		mysql_query($insert, connect());
		}
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
<h3 class="wintitle">Employee Deduction Entry</h3>
<form method="post">
<table width=100% border="0" cellpadding="4" cellspacing="0">

<tr>
	<td width=20%>
		Serach : <input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em.php");
		new Ajax.Autocompleter("keyword","hint","server_em.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			//$('name').value=myData[1];
			$('keyword').value=myData[1];
			self.location='index.php?menu=8DE&idd='+myData[0];
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
<h3 class="wintitle">Pending Deductions</h3>
<form method="post">

<!-- 
HIDDEN
-->
<input type="hidden" name="id">

<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<tr>
	<td width=37% align="center">Name</td>
	<td width=10% align="center">Amount</td>
	<td width=10% align="center">Terms</td>
	<td width=10% align="center">Terms Left</td>
	<td width=10% align="center">Paid</td>
	<td width=10% align="center">Balance</td>
	<td width=3% align="center"></td>
</tr>
<?php
$result = getdeduction($_GET['id']);
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){

$posted = getdeductionposted($_GET['id'],$row['name']);
$pending = getdeductionpending($_GET['id'],$row['name']);

?>
<tr <?php if($row['status'] == 'posted'){ echo 'bgcolor="lightblue"'; } ?>>
	<td><?php echo $row['name']; ?></td>
	<td><?php echo roundoff($row['amt'],2); ?></td>
	<td><?php echo $row['terms']; ?></td>
	<td><?php echo $pending['cnt']; ?></td>
	<td><?php echo roundoff($posted['posted'],2); ?></td>
	<td><?php echo roundoff($pending['pending'],2); ?></td>
	<td><input type="submit" name="delete" value="del" onClick="deleteID(this.form.id,'<?php echo $row['name']; ?>');" <?php if($row['status'] == 'posted'){ echo 'disabled'; } ?>></td>
</tr>
<?php
}
?>
</table>
<table border=0 width=100%>
<tr width=10%>
	<td colspan=4>&nbsp;</td>
</tr>
<tr>
	<td>
		<select style="width:100%" name="type" id="type">
		<option><?php echo $row['name']; ?></option>
		<?php
		$select = "select * from deductions";
		$result_data = mysql_query($select, connect());
		while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
		?>
		<option><?php echo $data['name']; ?></option>
		<?php
		}
		?>
		</select>
	</td>
	<td width=20%>
	<input type="text" name="terms" id="terms" style="width:100px;">
	<select style="width:100%;display:none;" name="termsz">
		<option value="1">1 Give</option>
		<option value="2">2 Gives</option>
		<option value="3">3 Gives</option>
		<option value="4">4 Gives</option>
		<option value="5">5 Gives</option>
		<option value="6">6 Gives</option>
		<option value="7">7 Gives</option>
		<option value="8">8 Gives</option>
		<option value="9">9 Gives</option>
		<option value="10">10 Gives</option>
		<option value="11">11 Gives</option>
		<option value="12">12 Gives</option>
		<option value="13">13 Gives</option>
		<option value="14">14 Gives</option>
		<option value="15">15 Gives</option>
		<option value="16">16 Gives</option>
		<option value="17">17 Gives</option>
		<option value="18">18 Gives</option>
		<option value="19">19 Gives</option>
		<option value="20">20 Gives</option>
		<option value="21">21 Gives</option>
		<option value="22">22 Gives</option>
		<option value="23">23 Gives</option>
		<option value="24">24 Gives</option>
		<option value="24">25 Gives</option>
		<option value="24">26 Gives</option>
		<option value="24">27 Gives</option>
		<option value="24">28 Gives</option>
		<option value="24">29 Gives</option>
		<option value="24">30 Gives</option>
		<option value="24">31 Gives</option>
		<option value="24">32 Gives</option>
		<option value="24">33 Gives</option>
	</select>
	</td>
	<td width=10%><input type="text" name="amount" id="amount" style="width:100%;"></td>
	<td width=10%><input type="text" name="balance" id="balance" style="width:100%;"></td>
</tr>
<tr>
	<td colspan=4 align="right"><input type="submit" name="add" value="add"></td>
</tr>
</table> 
</form>
