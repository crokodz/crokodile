<?php
include "class.pager.php";

if(!$_GET['id']){
	$_GET['id'] = $_GET['idd'];
	}


if(isset($_POST['del'])){
	$update = "update employee_ot set status = 'deleted' where `id` = '" . $_POST['id'] . "'";
	mysql_query($update, connect());
	}
	
function h2m($hours){
	$expl = explode(":", $hours); 
	return ($expl[0] * 60) + $expl[1];
	}
	
function GetPayCode($id,$status){
	$select = "select name from pay where `name` = '" . $id . "' LIMIT 1";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$name = $row['name'];
	
	$select = "select id from ot_rate where name = '" . $status . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	$ot_id = $row['id'];
	
	$select = "select * from pay where name = '" . $name . "' and ot = '" . $ot_id . "'";
	$result = mysql_query($select, connect());
	$row = mysql_fetch_array($result,MYSQL_ASSOC);
	return $row;
	}

if(isset($_POST['save'])){
	$s = h2m("22:00");
	$mme = h2m($_POST['ote']);
	$mms = h2m($_POST['ots']);
	
	
	
	if($mme>$s){
		$nd = $mme - $s;
		}
	else{
		$nd = 0;
		}
	
	if($_POST['mins'] > 480){
		$otx = $_POST['mins'] - 480;
		$mins = 480;
		}
	else{
		$mins = $_POST['mins'];
		$otx = 0;
		}
		
	if($_POST['type']=='LEGAL HOL IN EXCESS OF 8 HOURS'){
		$otx = $mins;
		$_POST['type']='LEGAL HOLIDAY';
		$mins = 0;
		}
	

	$insert = "INSERT INTO `employee_ot` (
		`id` ,
		`em_id` ,
		`date` ,
		`timef` ,
		`timet` ,
		`status` ,
		`username` ,
		`datetime`,
		`remarks`,
		`mins`,
		`hours`,
		`type`,
		`minsx`,
		`minsnd`
		)
		VALUES (
		NULL , 
		'" . $_GET['id'] . "', 
		'" . $_POST['date'] . "', 
		'" . $_POST['ots'] . "', 
		'" . $_POST['ote'] . "', 
		'pending', 
		'" . $_SESSION['user'] . "',
		now(),
		'" . $_POST['remarks'] . "',
		'" . $mins . "',
		'" . $_POST['hours'] . "',
		'" . $_POST['type'] . "',
		'" . $otx . "',
		'" . $nd . "'
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
<h3 class="wintitle">Employee Unpaid Overtime Entry</h3>
<form method="post" autocomplete="off">
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
			self.location='index.php?menu=otx&idd='+myData[0];
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
</form>

<form method="post">

<input type="hidden" name="id">

<table width=100% border="0">
<tr>
	<td width="100px">Type : </td>
	<td>
		<select name="type">
		<option>REGULAR</option>
		<option>RESTDAY</option>
		<option>LEGAL HOLIDAY</option>
		<option>SPECIAL HOLIDAY</option>
		<option>LEGAL HOLIDAY RESTDAY</option>
		<option>SPECIAL HOLIDAY RESTDAY</option>
		<option>LEGAL HOL IN EXCESS OF 8 HOURS</option>
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
	<td>Ot Start : </td>
	<td>
		<input type="text" id="ots" name="ots" style="width:70px" maxlength="5" onkeydown="javascript:return maskTime(this,event.keyCode);" onkeyup="GetHours()"> <b>hh:mm</b>
	</td>
</tr>
<tr>
	<td>Ot End : </td>
	<td>
		<input type="text" id="ote" name="ote" style="width:70px" maxlength="5" onkeydown="javascript:return maskTime(this,event.keyCode,event.Code);" onkeyup="GetHours()"> <b>hh:mm</b>
	</td>
</tr>
<tr>
	<td>Hours : </td>
	<td>
		<input type="text" id="hours" name="hours" style="width:40px" readonly>
		<input type="hidden" id="mins" name="mins">
	</td>
</tr>
<tr>
	<td>Remarks : </td>
	<td>
		<textarea name="remarks" id="remarks" style="width:300px;height:60px;"></textarea>
	</td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="save" value="save" onclick="return onSaveOt();"></td>
</tr>
</table>
</form>

<br><br>
<h3 class="wintitle">Pending Unpaid Over Time</h3>

<table border="0" cellpadding="4" cellspacing="0" width="100%" rules="all">
<tr>
	<td width="100px">Date</td>
	<td width="100px">Ot Start</td>
	<td width="100px">Ot End</td>
	<td width="100px">Hours</td>
	<td width="100px">Type</td>
	<td width="200px">Remarks</td>
	<td width="70px">Payroll#</td>
	<td>&nbsp;</td>
</tr>
<?php
$select = "select * from employee_ot where em_id = '" .  $_GET['id']. "' and status != 'deleted' order by `date` asc";
$result = mysql_query($select, connect());
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
$dis = '';
if($row['status'] == 'posted'){
$dis = 'disabled';
}
?>
<form method="POST">
<tr>
	<td><?php echo $row['date']; ?></td>
	<td><?php echo $row['timef']; ?></td>
	<td><?php echo $row['timet']; ?></td>
	<td><?php echo $row['hours']; ?></td>
	<td><?php echo $row['type']; ?></td>
	<td><?php echo $row['remarks']; ?></td>
	<td><?php echo $row['posted_id']; ?></td>
	<td><input <?php echo $dis; ?> type="submit" name="del" value="delete"><input type="hidden" name="id" value="<?php echo $row['id']; ?>"></td>
</tr>
</form>
<?php
}
?>
</table>

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
		echo 'self.location = "index.php?menu=otx&page=1&keyword=' . $keyword . '&company=' . $company . '";';
		}
	else{
		echo 'self.location = "index.php?menu=otx&page=' . $_GET['page'] . '&keyword=' . $keyword . '&company=' . $company . '";';
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
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=otx&keyword=' . $_GET['keyword'] . '&company=' . $_GET['company']);
##

if ($_SESSION['company'] == '0'){
	$select = "select * from employee where status='active' and name like '%" .  $_GET['keyword'] . "%'  and pay_id like '" . $_SESSION['dep'] . "' and company_id like '%" . $_GET['company'] . "'  order by em_id asc  LIMIT " . $start . " , " . $limit;
	$result = mysql_query($select, connect());
	}
else{
	if (empty($_GET['company'])){
		echo "<script>";
		echo 'self.location = "index.php?menu=otx&page=1&keyword=&company=' . $_SESSION['company'] . '";';
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
			self.location='index.php?menu=otx&idd='+myData[0];
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
		<td align="left" onclick="self.location='index.php?menu=otx&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=otx&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=otx&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['department']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=otx&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_number']; ?></td>
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

<script type="text/javascript">
	var cal = Calendar.setup({
		onSelect: function(cal) { cal.hide() },
		showTime: true
		});
	cal.manageFields("datebtn", "date", "%Y-%m-%d");
</script>