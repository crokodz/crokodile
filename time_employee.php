
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
$result = mysql_query("select *from users;", connect());
	
if (isset($_POST['fyy'])){
	$fyy = $_POST['fyy'];
	}
else{
	$fyy = date('Y');
	}
if (isset($_POST['tyy'])){
	$tyy = $_POST['tyy'];
	}
else{
	$tyy = date('Y');
	}
if (isset($_POST['fmm'])){
	$fmm = $_POST['fmm'];
	}
else{
	$fmm = date('m');
	}
if (isset($_POST['tmm'])){
	$tmm = $_POST['tmm'];
	}
else{
	$tmm = date('m');
	}
if (isset($_POST['fdd'])){
	$fdd = $_POST['fdd'];
	}
else{
	$fdd = date('d');
	}
if (isset($_POST['tdd'])){
	$tdd = $_POST['tdd'];
	}
else{
	$tdd = date('d');
	}
?>
<h3 class="wintitle"><?php echo getword("Time Attendance Search"); ?></h3>
<form method="post" autocomplete="off">
<table width=100% border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width=10% align="left"><?php echo getword("employee name"); ?></td>
		<td width=90% align="letf">
		<input type="text" name="name" id="name" size=100 value="<?php echo $_SESSION['name']; ?>" readonly></td>
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_SESSION['em_id']; ?>"></td>
	</tr>
	<tr>
		<td align="left"><?php echo getword("from"); ?></td>
		<td align="left"><select name="fyy" id="fyy"><option><?php echo $fyy; ?></option><?php echo $yy; ?></select><select name="fmm" id="fmm"><option><?php echo $fmm; ?><?php echo $mm; ?></select><select name="fdd" id="fdd"><option><?php echo $fdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td align="left"><?php echo getword("to"); ?></td>
		<td align="left"><select name="tyy" id="tyy"><option><?php echo $tyy; ?><?php echo $yy; ?></select><select name="tmm" id="tmm"><option><?php echo $tmm; ?><?php echo $mm; ?></select><select name="tdd" id="tdd"><option><?php echo $tdd; ?><?php echo $dd; ?></select></td>
	</tr>
	<tr>
		<td colspan=2 align="left"><input type="submit" name="search" value="search"></td>
	</tr>
</table>
<br>
<?php
$select = "select * from employee where status='active' and em_id = '" . $_POST['keyword'] . "'";
$result = mysql_query($select, connect());
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$allowed_ot = $row['allowed_ot'];
$allowed_ut = $row['allowed_ut'];
$allowed_late = $row['allowed_late'];

?>
<h3 class="wintitle"><?php echo getword("Employee Information"); ?></h3>
<input type="hidden" name="em_id" id="em_id" value="<?php echo $row['em_id']; ?>">

<table width=100% border="0" cellpadding="4" cellspacing="0">
<tr>
	<td width=20%><?php echo getword("Id Number"); ?> : <b><?php echo $row['em_id']; ?></b></td>
</tr>
<tr>
	<td width=20%><?php echo getword("Name"); ?> : <b><?php echo $row['name']; ?></td>
</tr>
</table>
<br>
<h3 class="wintitle"><?php echo getword("Employee Attendance History"); ?></h3>
<table width=100% class="timeattendance">
<tr>
	<td width="30px" align="center"><?php echo getword("Day"); ?></td>
	<td align="center"><?php echo getword("Status"); ?></td>
	<td width="70px" align="center"><?php echo getword("Date"); ?></td>
	<td width="60px" align="center"><?php echo getword("Shift Code"); ?></td>
	<td width="40px" align="center"><?php echo getword("Time In"); ?></td>
	<td width="40px" align="center"><?php echo getword("Time Out"); ?></td>
	<td width="40px" align="center"><?php echo getword("Total"); ?></td>
	<td width="40px" align="center"><?php echo getword("Late"); ?></td>
	<td width="40px" align="center"><?php echo getword("UT"); ?></td>
	<td width="40px" align="center"><?php echo getword("Start OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("End OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("OT"); ?></td>
	<td width="40px" align="center"><?php echo getword("OTX"); ?></td>
	<td width="40px" align="center"><?php echo getword("ND"); ?></td>
	<td width="120px" align="center"><?php echo getword("REMARKS"); ?></td>
</tr>
<?php
$from = $_POST['fyy'] . "-" . $_POST['fmm'] . "-" . $_POST['fdd'];
$to = $_POST['tyy'] . "-" . $_POST['tmm'] . "-" . $_POST['tdd'];

if($_POST['filter']==1){
	$sql = "";
	}
elseif($_POST['filter']==2){
	$sql = " and (`trxn_time_out` = '00:00:00' or `trxn_time_in` = '00:00:00' or `trxn_time_out` = `trxn_time_in`) 
		and (`status` = 'REGULAR' or `status` = 'UNFILED')
		";
	}
elseif($_POST['filter']==3){
	$sql = " and (`status` = 'RESTDAY' or `status` = 'UNFILED')
		";
	}	
elseif($_POST['filter']==4){
	
	}
	
$select = "select * from transaction where trxn_date between '" . $from . "' and '" . $to . "' and em_id = '" . $_POST['keyword'] . "' " . $sql . " order by `trxn_date` asc, `trxn_time_in` asc ";
$result = mysql_query($select, connect());
$x = 0;
while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
	$a = split("-" , $row['trxn_date']);
	
	$dayoftheweek = date("D", mktime(0, 0, 0, $a[1], $a[2], $a[0]));
	$hours = getTime($row['trxn_time_out'],1) - getTime($row['trxn_time_in'],1);
	$totz = $row['total'];

	
	if($row['approved']){
		$ttotal = $ttotal + $row['total'];
		$tlate = $tlate + $row['late'];
		$tut = $tut + $row['ut'];
		}
	if($row['cbot']){
		$tot = $tot + $row['ot'];
		$totx = $totx + $row['otx'];
		$tnd = $tnd + $row['nd'];
		}
	
	if($row['approved'] == "" and $row['status'] != 'UNFILED' and $dayoftheweek != 'Sat' and $dayoftheweek != 'Sun'){
		$bg = 'bgcolor="#EDDA74"';
		}
	else{
		$bg = "";
		}
	?>
	<tr <?php if ($row['status'] == 'UNFILED' or $dayoftheweek == 'Sat' or $dayoftheweek == 'Sun') { echo 'bgcolor="#4CC552"'; }?> <?php echo $bg; ?>>
		<td><b><?php echo $dayoftheweek; ?></b></td>
		<td><?php echo $row['status']; ?></td>
		<td><?php echo $row['trxn_date']; ?><input type="hidden" name="id<?php echo $x; ?>" value="<?php echo $row['trxn_id']; ?>"></td>
		<td><?php echo $row['shift_code']; ?></td>
		<td><?php echo cutz($row['trxn_time_in']); ?></td>
		<td><?php echo cutz($row['trxn_time_out']); ?></td>
		<td><?php echo m2h($row['total']); ?></td>
		<td><?php echo m2h($row['late']); ?></td>
		<td><?php echo m2h($row['ut']); ?></td>
		<td><?php echo cutz($row['ot_in']); ?></td>
		<td><?php echo cutz($row['ot_out']); ?></td>
		<td><?php echo m2h($row['ot']); ?></td>
		<td><?php echo m2h($row['otx']); ?></td>
		<td><?php echo m2h($row['nd']); ?></td>
		<td><?php echo $row['otremarks']; ?></td>
	</tr>
	<?php
	$x++;
	}
?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php echo m2h($tlate); ?></td>
	<td><?php echo m2h($tut); ?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?php echo m2h($tot); ?></td>
	<td><?php echo m2h($totx); ?></td>
	<td><?php echo m2h($tnd); ?></td>
	<td>&nbsp;</td>
</tr>
<input type="hidden" name="count" id="count" value="<?php echo $x; ?>">
<?php
if($_SESSION['level'] != 'employee'){
?>
<tr>
	<td colspan=15 align="right"><input type="submit" name="save" id="save" value="update" onclick="return checkAttendance()"> | <input type="button" value="Extract to Excel" onclick="timekeppingexl()"></td>
</tr>
<?php
}
?>
</table>
<div id="popDiv" class="pop">
	<iframe name="pop" width=600 height=300 frameborder="0"></iframe>
</div>

</form>

<div id="getupload" class="getupload">
	<form enctype="multipart/form-data" method="post">
	<input type="file" name="file" id="file"><br><br>
	<input type="submit" name="upload" value="upload">
	</form>
</div>
<input type="hidden" id="ccct" value="<?php echo $ccct; ?>">
