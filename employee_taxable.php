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
	$select = "select * from employee_taxable where 
		em_id = '" . $em_id . "' and 
		status != 'deleted' 
		order by id";
	return mysql_query($select, connect());
	}

function getOnhold($em_id){
	$select = "select sum(`amount`) as `onhold` from employee_taxable where 
		em_id = '" . $em_id . "' and 
		(status = 'onhold' or status = 'onholddeminimis') ";
	$result =  mysql_query($select, connect());
	$row=mysql_fetch_array($result,MYSQL_ASSOC);
	return $row['onhold'];
	}


if(isset($_POST['delete'])){
	#$delete = "delete from employee_deduction where deduct_id = '" . $_POST['id'] . "'";
	$update = "update employee_taxable set status = 'deleted', deleted = '" . $_SESSION['user'] . "', 		deleted_date = curdate() where id = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}
	
if(isset($_POST['onhold'])){
	$update = "update employee_taxable set status = 'onhold', deleted = '" . $_SESSION['user'] . "', deleted_date = curdate() where  em_id = '" . $_GET['id'] . "' and `status` = 'pending'  ";
	mysql_query($update, connect());
	
	$update = "update employee_taxable set status = 'onholddeminimis', deleted = '" . $_SESSION['user'] . "', deleted_date = curdate() where  em_id = '" . $_GET['id'] . "' and `status` = 'Deminimis'  ";
	mysql_query($update, connect());
	}
	
if(isset($_POST['pending'])){
	$update = "update employee_taxable set status = 'pending', deleted = '', deleted_date = '' where  em_id = '" . $_GET['id'] . "' and `status` = 'onhold'  ";
	mysql_query($update, connect());
	
	$update = "update employee_taxable set status = 'Deminimis', deleted = '', deleted_date = '' where  em_id = '" . $_GET['id'] . "' and `status` = 'onholddeminimis'  ";
	mysql_query($update, connect());
	}


if(isset($_POST['add'])){
	if($_POST['deminimis']){
		$stat = "Deminimis";
		}
	else{
		$stat = "Pending";
		}

	$insert = "INSERT INTO `employee_taxable` (
		`id` ,
		`name` ,
		`em_id` ,
		`amount` ,
		`posted_id` ,
		`status` ,
		`username` ,
		`datetime`
		)
		VALUES (
		NULL , 
		'" . $_POST['name'] . "', 
		'" . $_GET['id'] . "', 
		'" . $_POST['amount'] . "', 
		'0', 
		'" . $stat . "', 
		'" . $_SESSION['user'] . "', 
		now()
		)";
	mysql_query($insert, connect());
	}

$select = "select * from employee where em_id = '" . $_GET['id'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$onhold = getOnhold($_GET['id']);
?>
<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<h3 class="wintitle">Employee Others Taxable Income</h3>
<form method="post">

<?php
if($row['em_id']){
?>
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
			self.location='index.php?menu=eotaxable&idd='+myData[0];
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

<h3 class="wintitle">Employee Other Taxable Income</h3>
<form method="post">

<!-- 
HIDDEN
-->
<input type="hidden" name="id">

<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<?php
if($onhold>0){
?>
<tr>
	<td align="right" colspan=6><input value="Put all ON-HOLD Status to Pending Status" type="submit" name="pending"></td>
</tr>
<?php
$diabled = "disabled";
$name = "On Hold";
}
else{
?>
<tr>
	<td align="right" colspan=6><input value="Put all Pending Status to ON-HOLD Status" type="submit" name="onhold"></td>
</tr>
<?php
$name = "del";
}
?>
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
	<td><input type="submit" name="delete" value="<?php echo $name; ?>" onClick="deleteID(this.form.id,<?php echo $row['id']; ?>);" <?php if($row['status'] == 'posted'){ echo 'disabled'; } ?> <?php echo $diabled; ?>></td>
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
	<td width=80%>
		<select style="width:550px" name="name" id="name">
		<?php
		$select = "select * from taxable_entry where name!='LHRD' and name!= 'ADDITIONAL DAY' order by name asc";
		$result_data = mysql_query($select, connect());
		while ($data = mysql_fetch_array($result_data,MYSQL_ASSOC)){
		?>
		<option><?php echo $data['name']; ?></option>
		<?php
		}
		?>
		</select>
		|
		Permanent <input type="checkbox" name="deminimis">
	</td>
	<td width=20%><input type="text" name="amount" style="width:100%;"></td>
</tr>
<tr>
	<td colspan=4 align="right"><input type="submit" name="add" value="add"></td>
</tr>
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
		echo 'self.location = "index.php?menu=eotaxable&page=1&keyword=' . $keyword . '&company=' . $company . '";';
		}
	else{
		echo 'self.location = "index.php?menu=eotaxable&page=' . $_GET['page'] . '&keyword=' . $keyword . '&company=' . $company . '";';
		}
	echo '</script>';
	}
$select = "select count(*) as total from employee where name like '%" .  $_GET['keyword'] . "%'  and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "';";
$result = mysql_query($select, connect());
$count = mysql_fetch_array($result,MYSQL_ASSOC);
$total = $count['total'];

#pager
$p = new Pager;
$limit = 50;
$start = $p->findStart($limit);
$pages = $p->findPages($total, $limit);
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=eotaxable&keyword=' . $_GET['keyword'] . '&company=' . $_GET['company']);
##

if ($_SESSION['company'] == '0'){
	$select = "select * from employee where name like '%" .  $_GET['keyword'] . "%'  and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "'  order by em_id asc  LIMIT " . $start . " , " . $limit;
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
			$select = "select * from employee where name like '%" .  $_GET['keyword'] . "%' and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "' order by em_id asc LIMIT " . $start . " , " . $limit;
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
			self.location='index.php?menu=eotaxable&idd='+myData[0];
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
		<td align="left" onclick="self.location='index.php?menu=eotaxable&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=eotaxable&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=eotaxable&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['department']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=eotaxable&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_number']; ?></td>
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