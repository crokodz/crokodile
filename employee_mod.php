
<?php

include "class.pager.php";

if(!$_GET['id']){
	$_GET['id'] = $_GET['idd'];
	}
	
$currentDate = date("Y-m-d");
$date21 = strtotime(date("Y-m-d", strtotime($currentDate)) . " -19 year");
$date = date('Y-m-d', $date21);

if(isset($_POST['save'])){
	$insert = "insert into employee_dependents (`em_id`,`fname`, `mname`, `lname`, `bdate`, `relationship`, `gender`,`status`,`username`,`datetime`) 
	values ('" . $_GET['id'] . "','" . $_POST['fname'] . "','" . $_POST['mname'] . "','" . $_POST['lname'] . "','" . $_POST['date1'] . "','" . $_POST['relationship'] . "','" . $_POST['gender'] . "','active','" . $_SESSION['user'] . "',now())";
	mysql_query($insert, connect());
	}
	
if(isset($_POST['del'])){
	$update = "update employee_dependents set status = 'inactive' where id = '" . $_POST['idx'] . "'";
	mysql_query($update, connect());
	}
	
$select = "select * from employee where em_id = '" . $_GET['id'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
?>

<script src="date/js/jscal2.js"></script>
<script src="date/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="date/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="date/css/steel/steel.css" />



<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<h3 class="wintitle">Monitoring of Dependents</h3>
<form method="post">
<?php
if($row['em_id']){
?>
<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=20%>
		Serach : <input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em_mod.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			//$('name').value=myData[1];
			$('keyword').value=myData[1];
			self.location='index.php?menu=mod&idd='+myData[0];
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

<form method="post" autocomplete="off">
<form method="post">

<input type="hidden" name="id">

<table width=100% border="0">
<tr>
	<td width=90px >First Name : </td>
	<td>
		<input type="text" id="fname" name="fname" style="width:140px">
	</td>
</tr>
<tr>
	<td>Middle Name : </td>
	<td>
		<input type="text" id="mname" name="mname" style="width:140px">
	</td>
</tr>
<tr>
	<td>Last Name : </td>
	<td>
		<input type="text" id="lname" name="lname" style="width:140px">
	</td>
</tr>
<tr>
	<td width=80px >Birth Date : </td>
	<td>
		<input type="text" id="date1" name="date1" style="width:80px" value="<?php echo $date; ?>"> <input type="button" id="datebtn1" value="...">
	</td>
</tr>
<tr>
	<td>Relationship</td>
	<td>
		<select id="relationship" name="relationship">
			<option>Child #1</option>
			<option>Child #2</option>
			<option>Child #3</option>
			<option>Child #4</option>
		</select>
	</td>
</tr>
<tr>
	<td>Gender</td>
	<td>
		<select id="gender" name="gender">
			<option>Male</option>
			<option>Female</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan=2><br><input type="submit" name="save" value="save" onclick="return checkMod();"></td>
</tr>
</table>
</form>
<br>
<br>
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
<tr>
	<td align="center">Name</td>
	<td width=50px align="center">Age</td>
	<td width=100px align="center">Birth Date</td>
	<td width=100px align="center">Gender</td>
	<td width=100px align="center">Relationship</td>
	<td width=10px align="center"></td>
</tr>
<?php
$select = "select * from employee_dependents where em_id = '" . $_GET['id'] . "' and status = 'active' ";
$result = mysql_query($select, connect());
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
<tr>
	<td><?php echo $row['lname'] . ", " . $row['fname'] . " " . $row['mname']; ?></td>
	<td><?php echo birthday($row['bdate']); ?></td>
	<td><?php echo $row['bdate']; ?></td>
	<td><?php echo $row['gender']; ?></td>
	<td><?php echo $row['relationship']; ?></td>
	<td><form method="post"><input type="hidden" name="idx" value="<?php echo $row['id']; ?>"><input type="submit" name="del" value="delete"></form></td>
</tr>
<?php 
}
?>
</table>
<?php
}
else{
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
		echo 'self.location = "index.php?menu=mod&page=1&keyword=' . $keyword . '&company=' . $company . '";';
		}
	else{
		echo 'self.location = "index.php?menu=mod&page=' . $_GET['page'] . '&keyword=' . $keyword . '&company=' . $company . '";';
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
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=mod&keyword=' . $_GET['keyword'] . '&company=' . $_GET['company']);
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
		new Ajax.Autocompleter("keyword","hint","server_em_mod.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			//$('name').value=myData[1];
			$('keyword').value=myData[1];
			self.location='index.php?menu=mod&idd='+myData[0];
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
		<td align="left" onclick="self.location='index.php?menu=mod&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=mod&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=mod&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['department']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=mod&idd=<?php echo $row['em_id']; ?>';"><?php echo $row['em_number']; ?></td>
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
	cal.manageFields("datebtn1", "date1", "%Y-%m-%d");
</script>