<?php
include "class.pager.php";

if(!$_GET['id']){
	$_GET['id'] = $_GET['idd'];
	}
	
function getdeduction($em_id){
	$select = "select `name`, sum(`amount`) as `amt`, `terms` from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status != 'deleted'  group by `name`
		order by name asc";
	return mysql_query($select, connect());
	}
	
function getdeductionposted($em_id,$subid){
	$select = "select sum(`amount`) as `posted` from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status = 'posted' and
		sub_id = '" . $subid . "'
		group by `sub_id` ";
	$result =  mysql_query($select, connect());
	return mysql_fetch_array($result,MYSQL_ASSOC);
	}
	
function getdeductionpending($em_id,$subid){
	$select = "select sum(`amount`) as `pending`, count(*) as cnt from employee_deduction where 
		em_id = '" . $em_id . "' and 
		status = 'pending'  and
		sub_id = '" . $subid . "'
		group by `sub_id` ";
	$result =  mysql_query($select, connect());
	return mysql_fetch_array($result,MYSQL_ASSOC);
	}


if(isset($_POST['delete'])){
	$update = "update employee_deduction set status = 'deleted', deleted = '" . $_SESSION['user'] . "', deleted_date = curdate() where `sub_id` = '" . $_POST['id'] . "' and `status` != 'posted' ";
	mysql_query($update, connect());
	}


if(isset($_POST['add'])){
	$select = "select max(sub_id) as subid from employee_deduction";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$subid = $row['subid'] + 1;
	
	$x = $_POST['gamount'];
	
	$balance = 0;
	
	while($x!=0){
		if($_POST['damount'] > $x){
			$amount =  $x;
			}
		else{
			$amount =  $_POST['damount'];
			}
		
		$insert = "insert into employee_deduction (
			`sub_id`,
			`em_id`,
			`name`,
			`amount`,
			`balance`,
			`status`,
			`username`,
			`date`,
			`terms`,
			`datetime`,
			`principal_amount`,
			`gross_amount`,
			`date_granted`,
			`date_effectivity`,
			`ded_amount`,
			`granted_amount`
			) values (
			'" . $subid . "',
			'" . $_GET['id'] . "',
			'" . $_POST['type'] . "',
			'" . $amount . "',
			'" . $balance . "',
			'pending',
			'" . $_SESSION['user'] . "',
			curdate(),
			'0',
			now(),
			'" . $_POST['pamount'] . "',
			'" . $_POST['gamount'] . "',
			'" . $_POST['gdate'] . "',
			'" . $_POST['edate'] . "',
			'" . $_POST['damount'] . "',
			'" . $_POST['gamount'] . "'
			);";
		mysql_query($insert, connect());
		$x = $x - $_POST['damount'];
		if($x < 0){
			$x = 0;
			}
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
<?php
if($_GET['id']){
?>
<table width=100% border="0" cellpadding="4" cellspacing="0">

<tr>
	<td width=20%>
		Search : <input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
		<script type="text/javascript">
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

<input type="hidden" name="id">

<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<tr>
	<td align="left">Name</td>
	<td width=10% align="right">Principal Amount</td>
	<td width=10% align="right">Gross Amount</td>
	<td width=10% align="right">Amount Per Cutoff</td>
	<td width=10% align="center">Date Granted</td>
	<td width=10% align="center">Effectivity Date</td>
	<td width=10% align="right">Paid Amount</td>
	<td width=10% align="right">Balance Amount</td>
	<td width=7% align="center">Payroll Number</td>
</tr>
<tr>
	<td>
		<select style="width:100%" name="type" id="type">
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
	<td><input type="text" name="pamount" id="pamount" style="width:100%;"></td>
	<td><input type="text" name="gamount" id="gamount" style="width:100%;"></td>
	<td><input type="text" name="damount" id="damount" style="width:100%;"></td>
	<td><input type="text" name="gdate" id="dgate" style="width:100%;" value="<?php echo date('Y-m-d'); ?>"></td>
	<td><input type="text" name="edate" id="edate" style="width:100%;" value="<?php echo date('Y-m-d'); ?>"></td>
	<td><input type="text" name="paid" id="paid" style="width:100%;" readonly></td>
	<td><input type="text" name="balance" id="balance" style="width:100%;" readonly></td>
	<td align="left"><input type="submit" name="add" value="add"></td>
</tr>
</table> 

<br>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<?php 
$select = "select * from employee_deduction where em_id = '" . $_GET['id'] . "' and status != 'deleted' group by sub_id, posted_id ";
$result = mysql_query($select, connect());
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){

$posted = getdeductionposted($_GET['id'],$row['sub_id']);
$pending = getdeductionpending($_GET['id'],$row['sub_id']);
?>
<tr>
	<td align="left"><?php echo $row['name']; ?></td>
	<td width=10% align="right"><?php echo roundoff($row['principal_amount'],2); ?></td>
	<td width=10% align="right"><?php echo roundoff($row['gross_amount'],2); ?></td>
	<td width=10% align="right"><?php echo roundoff($row['ded_amount'],2); ?></td>
	<td width=10% align="center"><?php echo $row['date_granted']; ?></td>
	<td width=10% align="center"><?php echo $row['date_effectivity']; ?></td>
	<td width=10% align="right">
		<?php
		if($row['status'] == 'posted'){
		echo roundoff($row['ded_amount'],2);
		}
		?>
		</td>
	<td width=10% align="right">
		<?php
		if($row['status'] != 'posted'){
		echo roundoff($pending['pending'],2);
		}
		?>
		</td>
	<td width=7%>
		<?php
		if($row['status'] != 'posted'){
		?>
		<input type="submit" name="delete" value="del" onClick="deleteID(this.form.id,'<?php echo $row['sub_id']; ?>');"></td>
		<?php
		}
		else{
		echo $row['posted_id'];
		}
		?>
		</td>
</tr>
<?php
}
?>
</table>
</form>
<?php
}
else{
function getcompany($id){
	$select = "select * from company where id = '" . $id . "'";
	$result_data = mysql_query($select, connect());
	$data = mysql_fetch_array($result_data,MYSQL_ASSOC);
	return $data['name'];
	}

if (isset($_POST['delete'])){
	for ($x=0; $x < $_POST['count']; $x++){
		if ($_POST['id' . $x]){
			$update = "update employee set status='deleted', username='' where em_id = '" . $_POST['id' . $x] . "'";
			mysql_query($update, connect());
			}
		}
	}
	
if (isset($_POST['search'])){
	$company = $_POST['company'];
	$keyword = $_POST['keyword'];
	if($_POST['dep']){
		$_SESSION['dep'] = $_POST['dep'];
		}
	else{
		$_SESSION['dep'] = "%" . $_POST['dep'] . "%";
		}
	echo '<script>';
	if (empty( $_GET['page'])){
		echo 'self.location = "index.php?menu=8DE&page=1&keyword=' . $keyword . '&company=' . $company . '";';
		}
	else{
		echo 'self.location = "index.php?menu=8DE&page=' . $_GET['page'] . '&keyword=' . $keyword . '&company=' . $company . '";';
		}
	echo '</script>';
	}
$select = "select count(*) as total from employee where status='active' and name like '%" .  $_GET['keyword'] . "%'  and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "';";
$result = mysql_query($select, connect());
$count = mysql_fetch_array($result,MYSQL_ASSOC);
$total = $count['total'];

#pager
$p = new Pager;
$limit = 50;
$start = $p->findStart($limit);
$pages = $p->findPages($total, $limit);
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=8DE&keyword=' . $_GET['keyword'] . '&company=' . $_GET['company']);
##

if ($_SESSION['company'] == '0'){
	$select = "select * from employee where status='active' and name like '%" .  $_GET['keyword'] . "%'  and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "'  order by em_id asc  LIMIT " . $start . " , " . $limit;
	$result = mysql_query($select, connect());
	}
else{
	if (empty($_GET['company'])){
		echo "<script>";
		echo 'self.location = "index.php?menu=1&page=1&keyword=&company=' . $_SESSION['company'] . '";';
		echo "</script>";
		}
	else{
		if ($_GET['company'] == $_SESSION['company']){
			$select = "select * from employee where status='active' and name like '%" .  $_GET['keyword'] . "%' and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "' order by em_id asc LIMIT " . $start . " , " . $limit;
			$result = mysql_query($select, connect());
			}
		else{
			echo "<script>";
			echo "window.location='index.php?menu=19';";
			echo "</script>";
			}
		}
	}
?>

<form method="POST">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=80% align="left" ><input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
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
		<select style="width:150px" name="company" id="company">
			<option value="">ALL</option>
			<?php
			$result_data = $result_data = get_mycompany();
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['id']; ?>" <?php if ($_GET['company'] == $data['id']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
		</select>
		<select style="width:150px" name="dep" id="dep">
			<option value="">ALL</option>
			<?php
			$select = "select `name` from `pay` group by `name`";
			$result_data = mysql_query($select, connect());
			while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
			?>
			<option value="<?php echo $data['name']; ?>" <?php if ($_SESSION['dep'] == $data['name']){ echo 'selected'; } ?>><?php echo $data['name']; ?></option>
			<?php
			}
			?>
		</select>
		| <input type="submit" name="search" value="<?php echo getword("search"); ?>"></td>
	</tr>
	<tr>
		<td colspan=2 align="center"><?php echo $pagelist; ?></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=100px align="center" ><b><?php echo getword("id"); ?></b></td>
		<td align="center"><b><?php echo getword("name"); ?></b></td>
		<td width=150px align="center"><b><?php echo getword("department"); ?></b></td>
		<td width=150px align="center"><b><?php echo getword("contact"); ?></b></td>
	</tr>
<?php
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;" id="emlist">
		<td align="left" onclick="self.location='index.php?menu=8DE&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['department']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=8DE&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_number']; ?></td>
	</tr>
	<?php
	$x++;
	}
?>
<tr>
	<td align="center" colspan="5"><?php echo $pagelist; ?></td>
</tr>
<input type="hidden" name="count" value="<?php echo $x; ?>">
</table>
</form>
<?php
}
?>
