<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="medshtml">
<style>
	#search, .ulz { padding: 3px; border: 1px solid #999; font-family: verdana; arial, sans-serif; font-size: 12px;background: white;overflow:auto;}
	.ulz { list-style-type: none; font-family: verdana; arial, sans-serif; font-size: 14px;  margin: 1px 0 0 0}
	.liz { margin: 0 0 0px 0; cursor: default; color: red;}
	.liz:hover { background: #ffc; }
	.liz.selected { background: #FCC;}
</style>
<?php
$currentTimeoutInSecs = ini_get('session.gc_maxlifetime');
include "class.pager.php";

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
		echo 'self.location = "index.php?menu=1&page=1&keyword=' . $keyword . '&company=' . $company . '";';
		}
	else{
		echo 'self.location = "index.php?menu=1&page=' . $_GET['page'] . '&keyword=' . $keyword . '&company=' . $company . '";';
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
$pagelist = $p->pageList($_GET['page'], $pages,'index.php?menu=1&keyword=' . $_GET['keyword'] . '&company=' . $_GET['company']);
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

<h3 class="wintitle"><?php echo getword("Employee List"); ?></h3>
<form method="POST">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=80% align="left" ><input type="text" name="keyword" id="keyword" style="width:30%;" value="<?php echo $_GET['keyword']; ?>"><div id="hint"></div>
		<script type="text/javascript">
		new Ajax.Autocompleter("keyword","hint","server_em.php", {afterUpdateElement : getSelectedId});
		function getSelectedId(text, li) {
			myData = li.id.split("@@"); 
			//$('name').value=myData[1];
			$('keyword').value=myData[1];
			self.location='index.php?menu=1DE&id='+myData[0];
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
		
		<td width=20% align="right"><a href="index.php?menu=1DA&c=<?php echo $_GET['company']; ?>">add</a> | <a href="javascript:void(0);" onclick="ClickButton('delete')">delete</a><input type="submit" name="delete" id="delete" value="delete" style="display:none;"></td>
	</tr>
	<tr>
		<td colspan=2 align="center"><?php echo $pagelist; ?></td>
	</tr>
</table>	
<table width=100% border="0" cellpadding="4" cellspacing="0" rules="all">
	<tr>
		<td width=10% align="center" ><b><?php echo getword("id"); ?></b></td>
		<td width=50% align="center"><b><?php echo getword("name"); ?></b></td>
		<td width=20% align="center"><b><?php echo getword("division"); ?></b></td>
		<td width=15% align="center"><b><?php echo getword("paycode"); ?></b></td>
		<td width=5% align="center"><input type="checkbox" name="checkb" id="ca" onclick="checkall(this.form)"></td>
	</tr>
<?php
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
?>
	
	<tr style="cursor:pointer;" id="emlist">
		<td align="left" onclick="self.location='index.php?menu=1DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['em_id']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=1DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['name']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=1DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['division']; ?></td>
		<td align="left" onclick="self.location='index.php?menu=1DE&id=<?php echo $row['em_id']; ?>';"><?php echo $row['pay_id']; ?></td>
		<td align="center"><input type="checkbox" name="id<?php echo $x; ?>" id="cb<?php echo $x; ?>" value="<?php echo $row['em_id']; ?>"></td>
	</tr>
	<?php
	$x++;
	}
?>
<tr>
	<td align="center" colspan="5"><?php echo $pagelist; ?></td>
</tr>
<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
</table>
</form>
